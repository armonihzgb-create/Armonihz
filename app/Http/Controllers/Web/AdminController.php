<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MusicianProfile;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\HiringRequest;
use App\Models\CastingApplication;
use App\Models\Report; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\MusicianVerifiedNotification;
use App\Notifications\MusicianRejectedVerificationNotification;

class AdminController extends Controller
{
    public function index(Request $request, ?string $search = null)
    {
        // 1. Métricas de Músicos
        $totalMusicians = MusicianProfile::count();

        // Usar verification_status para que coincida exactamente
        // con el contador de la pestaña de "Validar Músicos"
        $pendingMusiciansCount = MusicianProfile::pending()->count();

        // 2. Métricas de Clientes
        $totalClients = Client::count();

        // 3. Métricas de Eventos (Combinando Hiring y Casting)
        $completedHiring   = HiringRequest::where('status', 'completed')->count();
        $completedCastings = CastingApplication::where('status', 'completed')->count();
        $totalCompletedEvents = $completedHiring + $completedCastings;

        // 4. Lista de músicos (Búsqueda local o Recientes)
        // Soporte tanto para query params (?search=) como para path segments (/search/term)
        $search = $search ? urldecode($search) : $request->input('search');

        $recentMusicians = MusicianProfile::with(['user', 'genres'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('stage_name', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
                      });
                });
            }, function ($query) {
                // Si no hay búsqueda, mantenemos la prioridad de validación
                $query->orderByRaw("FIELD(verification_status, 'pending', 'unverified', 'rejected', 'approved')")
                      ->orderBy('created_at', 'desc')
                      ->take(10);
            })
            ->get();

        return view('admin', compact(
            'totalMusicians',
            'pendingMusiciansCount',
            'totalClients',
            'totalCompletedEvents',
            'recentMusicians',
            'search'
        ));
    }

    public function musiciansIndex(Request $request, string $status = 'pending', ?string $search = null)
    {
        // 1. Conteos usando los scopes — siempre precisos
        $counts = [
            'pending'    => MusicianProfile::pending()->count(),
            'approved'   => MusicianProfile::approved()->count(),
            'rejected'   => MusicianProfile::rejected()->count(),
            'unverified' => MusicianProfile::unverified()->count(),
        ];

        // $status y $search vienen del path de la URL (ej. /admin/musicians/pending/jose)
        // Esto evita que proxies como Traefik corrompan los query strings.
        $search = $search ? urldecode($search) : null;

        // 2. Consulta usando el scopeByStatus y filtro de búsqueda (opcional)
        $musicians = MusicianProfile::with(['user', 'genres'])
            ->byStatus($status)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('stage_name', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.musicians.index', compact('musicians', 'status', 'counts', 'search'));
    }

    public function verifyMusicianView($id)
    {
        $musician = MusicianProfile::with(['user', 'genres'])->findOrFail($id);
        
        // Solo podemos verificar a los que están 'pending' (o también podríamos dejar ver a 'rejected'/'approved')
        return view('admin.musicians.verify', compact('musician'));
    }

    public function verifyMusicianAction(Request $request, $id)
    {
        $musician = MusicianProfile::findOrFail($id);

        $request->validate([
            'action'           => 'required|in:approve,reject',
            'rejection_reason' => 'nullable|string|max:1000|required_if:action,reject',
        ]);

        if ($request->action === 'approve') {
            // is_verified se sincroniza automáticamente por el booted() hook del modelo
            $musician->update([
                'verification_status' => MusicianProfile::STATUS_APPROVED,
                'verified_at'         => now(),
                'verified_by'         => Auth::id(),
                'rejection_reason'    => null,
            ]);

            // Enviar email de aprobación al músico
            try {
                $musician->user->notify(new MusicianVerifiedNotification($musician));
            } catch (\Exception $e) {
                Log::error('Error al enviar email de verificación aprobada: ' . $e->getMessage(), [
                    'musician_id' => $musician->id,
                    'user_email'  => $musician->user->email ?? 'N/A',
                ]);
            }

            $successMsg = 'Músico aprobado correctamente. Se ha enviado un correo de confirmación.';
            $returnStatus = MusicianProfile::STATUS_PENDING; // Volvemos a pendientes
        } else {
            $musician->update([
                'verification_status' => MusicianProfile::STATUS_REJECTED,
                'rejection_reason'    => $request->rejection_reason,
            ]);

            // Enviar email de rechazo con el motivo
            try {
                $musician->user->notify(new MusicianRejectedVerificationNotification($musician, $request->rejection_reason));
            } catch (\Exception $e) {
                Log::error('Error al enviar email de verificación rechazada: ' . $e->getMessage(), [
                    'musician_id' => $musician->id,
                    'user_email'  => $musician->user->email ?? 'N/A',
                ]);
            }

            $successMsg = 'Verificación rechazada. Se ha notificado al músico por correo.';
            $returnStatus = MusicianProfile::STATUS_REJECTED; // Volvemos a rechazados
        }

        // Redirigir a la lista conservando el filtro de estado correcto
        return redirect()
            ->route('admin.musicians.index', ['status' => $returnStatus])
            ->with('success', $successMsg);
    }

    public function streamDocument($id)
    {
        $admin = Auth::user();
        if ($admin->role !== 'admin') {
            abort(403, 'Acceso denegado.');
        }

        $musician = MusicianProfile::findOrFail($id);

        if (!$musician->id_document_path) {
            abort(404, 'El músico no ha subido ningún documento.');
        }

        $path = basename($musician->id_document_path);

        // Primer intento: Laravel Storage default disk (Puede ser S3 o configuraciones de Easypanel)
        if (Storage::exists('musician_ids/' . $path)) {
            // Si el driver actual permite devolver directamente una respuesta de stream:
            return Storage::response('musician_ids/' . $path);
        }

        // Segundo intento: explícitamente Local disk (si forzamos en la subida)
        if (Storage::disk('local')->exists('musician_ids/' . $path)) {
            return Storage::disk('local')->response('musician_ids/' . $path);
        }

        // Tercer intento: explícitamente Public disk
        if (Storage::disk('public')->exists('musician_ids/' . $path)) {
            return Storage::disk('public')->response('musician_ids/' . $path);
        }

        // Tercer intento manual por si Local Disk mapeó a otro lado o no reconoce la capeta private internamente
        $manualPaths = [
            storage_path('app/private/musician_ids/' . $path),
            storage_path('app/musician_ids/' . $path),
            storage_path('app/public/musician_ids/' . $path)
        ];

        foreach ($manualPaths as $fullPath) {
            if (file_exists($fullPath)) {
                return response()->file($fullPath, [
                    'Content-Type' => mime_content_type($fullPath),
                    'Cache-Control' => 'no-cache, private',
                ]);
            }
        }

        // Si nada funciona, arrojar un error que contenga rutas de depuración en texto plano para que el usuario pueda verlo al abrir el enlace directo
        return response("File not found.\n\nDB Path: " . $musician->id_document_path . "\n\nChecked:\n" . implode("\n", $manualPaths), 404, ['Content-Type' => 'text/plain']);
    }

    public function showCasting($id)
    {
        $event = ClientEvent::with([
            'client',
            'genre',
            'applications.musician.user',
            'applications.musician.genres',
        ])->findOrFail($id);

        return view('admin.castings.show', compact('event'));
    }

    public function castingsIndex(Request $request)
    {
        $status = $request->get('status');
        
        $query = ClientEvent::with(['client', 'applications'])->orderBy('created_at', 'desc');
        
        if (in_array($status, ['open', 'completed', 'canceled', 'inactive'])) {
            $query->where('status', $status);
        }

        $events = $query->paginate(15);

        // Contadores para los Tabs
        $counts = [
            'all'       => ClientEvent::count(),
            'open'      => ClientEvent::where('status', 'open')->count(),
            'completed' => ClientEvent::where('status', 'completed')->count(),
            'other'     => ClientEvent::whereIn('status', ['canceled', 'inactive'])->count(),
        ];

        return view('admin.castings.index', compact('events', 'status', 'counts'));
    }

    public function updateCastingStatus(Request $request, $id)
    {
        $event = ClientEvent::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:open,canceled,inactive'
        ]);

        $event->status = $request->status;
        $event->save();

        return redirect()->route('admin.castings.index')->with('success', 'El estado del evento ha sido actualizado correctamente a ' . strtoupper($request->status) . '.');
    }

    public function destroyCasting($id)
    {
        $event = ClientEvent::findOrFail($id);
        
        // Esto usa SoftDeletes gracias a que lo añadimos al modelo
        $event->delete();

        return redirect()->route('admin.castings.index')->with('success', 'El evento ha sido eliminado correctamente (Soft Delete).');
    }

    public function reportsIndex(Request $request)
    {
      $status = $request->get('status');
        
        // ACTUALIZADO: Cambiamos 'reporter' por 'client'
        $query = Report::with(['client', 'musicianProfile.user'])->orderBy('created_at', 'desc');
        if (in_array($status, ['pending', 'reviewed', 'resolved'])) {
            $query->where('status', $status);
        }

        $reports = $query->paginate(15);

        // Contadores para los Tabs en la vista
        $counts = [
            'all'       => Report::count(),
            'pending'   => Report::where('status', 'pending')->count(),
            'reviewed'  => Report::where('status', 'reviewed')->count(),
            'resolved'  => Report::where('status', 'resolved')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'status', 'counts'));
    }

    public function updateReportStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved'
        ]);

        $report->update([
            'status' => $request->status    
        ]);

        $statusLabels = [
            'pending'  => 'Pendiente',
            'reviewed' => 'Revisado',
            'resolved' => 'Resuelto',
        ];
        $label = $statusLabels[$request->status] ?? $request->status;

        return redirect()->back()->with('success', 'El reporte ha sido marcado como «' . $label . '» correctamente.');
    }
}
