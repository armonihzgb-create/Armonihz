<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MusicianProfile;
use App\Models\Client;
use App\Models\ClientEvent;
use App\Models\HiringRequest;
use App\Models\CastingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with real metrics.
     */
    public function index()
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

        // 4. Lista de músicos recientes (priorizando pendientes de validación)
        $recentMusicians = MusicianProfile::with(['user', 'genres'])
            ->orderByRaw("FIELD(verification_status, 'pending', 'unverified', 'rejected', 'approved')")
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin', compact(
            'totalMusicians',
            'pendingMusiciansCount',
            'totalClients',
            'totalCompletedEvents',
            'recentMusicians'
        ));
    }

    public function musiciansIndex(Request $request)
    {
        // 1. Conteos usando los scopes — siempre precisos
        $counts = [
            'pending'    => MusicianProfile::pending()->count(),
            'approved'   => MusicianProfile::approved()->count(),
            'rejected'   => MusicianProfile::rejected()->count(),
            'unverified' => MusicianProfile::unverified()->count(),
        ];

        // 2. Estado activo: Recuperar de la URL y validar contra los 4 estados posibles
        $status = $request->query('status');
        if (!in_array($status, ['pending', 'approved', 'rejected', 'unverified'])) {
            $status = 'pending';
        }

        // 3. Consulta usando el scopeByStatus
        $musicians = MusicianProfile::with(['user', 'genres'])
            ->byStatus($status)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.musicians.index', compact('musicians', 'status', 'counts'));
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

            $successMsg = 'Músico aprobado correctamente.';
            $returnStatus = MusicianProfile::STATUS_PENDING; // Volvemos a pendientes
        } else {
            $musician->update([
                'verification_status' => MusicianProfile::STATUS_REJECTED,
                'rejection_reason'    => $request->rejection_reason,
            ]);

            $successMsg = 'Verificación rechazada correctamente.';
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

    public function castingsIndex(Request $request)
    {
        $query = ClientEvent::with(['client', 'applications'])->orderBy('created_at', 'desc');
        
        $status = $request->get('status');
        if (in_array($status, ['open', 'completed', 'canceled', 'inactive'])) {
            $query->where('status', $status);
        }

        $events = $query->paginate(15);

        return view('admin.castings.index', compact('events', 'status'));
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
}
