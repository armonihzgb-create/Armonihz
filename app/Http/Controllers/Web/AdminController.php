<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MusicianProfile;
use App\Models\Client;
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
        $pendingMusiciansCount = MusicianProfile::where('is_verified', false)->count();

        // 2. Métricas de Clientes
        $totalClients = Client::count();

        // 3. Métricas de Eventos (Combinando Hiring y Casting)
        $completedHiring = HiringRequest::where('status', 'completed')->count();
        $completedCastings = CastingApplication::where('status', 'completed')->count();
        $totalCompletedEvents = $completedHiring + $completedCastings;

        // 4. Lista de músicos recientes (últimos 10, priorizando no verificados)
        $recentMusicians = MusicianProfile::with(['user', 'genres'])
            ->orderBy('is_verified', 'asc') // Primero los no verificados (false = 0)
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
        $status = $request->get('status', 'pending');
        
        $query = MusicianProfile::with(['user', 'genres']);

        if (in_array($status, ['pending', 'approved', 'rejected', 'unverified'])) {
            $query->where('verification_status', $status);
        }

        $musicians = $query->orderBy('created_at', 'desc')->paginate(15);

        // Conteos para los badges
        $counts = [
            'pending' => MusicianProfile::where('verification_status', 'pending')->count(),
            'approved' => MusicianProfile::where('verification_status', 'approved')->count(),
            'rejected' => MusicianProfile::where('verification_status', 'rejected')->count(),
            'unverified' => MusicianProfile::where('verification_status', 'unverified')->count(),
        ];

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
        $admin = Auth::user();

        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'nullable|string|max:1000|required_if:action,reject'
        ]);

        if ($request->action === 'approve') {
            $musician->verification_status = 'approved';
            $musician->is_verified = true; // Sincronizamos con el viejo campo para compatibilidad
            $musician->verified_at = now();
            $musician->verified_by = $admin->id;
            $musician->rejection_reason = null;
            $musician->save();

            return redirect()->route('admin.dashboard')->with('success', 'Músico verificado y aprobado correctamente.');
        } else {
            $musician->verification_status = 'rejected';
            $musician->is_verified = false;
            $musician->rejection_reason = $request->rejection_reason;
            $musician->save();

            return redirect()->route('admin.dashboard')->with('success', 'Verificación rechazada. El músico ha sido notificado (en el dashboard).');
        }
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
}
