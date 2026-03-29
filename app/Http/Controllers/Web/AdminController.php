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
}
