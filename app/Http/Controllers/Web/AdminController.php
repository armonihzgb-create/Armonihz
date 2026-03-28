<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MusicianProfile;
use App\Models\Client;
use App\Models\HiringRequest;
use App\Models\CastingApplication;
use Illuminate\Http\Request;

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
}
