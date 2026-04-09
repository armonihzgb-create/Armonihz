<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HiringRequest;
use App\Notifications\HiringRequestStatusNotification; // 1. FALTA ESTA IMPORTACIÓN

class RequestController extends Controller
{
    public function index(Request $request, $status = null)
    {
        $user = $request->user();
        
        // Tomcat o Nginx en entornos compartidos podrían arrojar vacíos en inputs, lo tomamos del path
        $status = $status ?: $request->input('status');

        $baseQuery = $user->role === 'musico' && $user->musicianProfile
            ? $user->musicianProfile->hiringRequests()
            : $user->clientRequests();

        // Extraer contadores con clones limpios de Laravel
        $counts = [
            'all'      => $baseQuery->clone()->count(),
            'pending'  => $baseQuery->clone()->where('status', 'pending')->count(),
            'accepted' => $baseQuery->clone()->where('status', 'accepted')->count(),
        ];

        $query = $baseQuery->clone();
        
        if ($user->role === 'musico' && $user->musicianProfile) {
            $query->with('client');
        } else {
            $query->with('musicianProfile');
        }

        if ($status && in_array($status, ['pending', 'accepted', 'rejected', 'completed', 'counter_offer'])) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        $requests = $query->latest()->get();

        return view('requests', compact('requests', 'status', 'counts'));
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $hiringRequest = HiringRequest::with(['client', 'musicianProfile.user'])->findOrFail($id);

        // Security check
        if ($user->role === 'musico' && $hiringRequest->musician_profile_id !== $user->musicianProfile->id) {
            abort(403);
        }

        if ($user->role === 'cliente' && $hiringRequest->client_id !== $user->id) {
            abort(403);
        }

        return view('requests.show', compact('hiringRequest'));
    }

   public function updateStatus(Request $request, $id)
{
    // 1. Validamos que si el status es counter_offer, traiga los datos necesarios
    $request->validate([
        'status' => 'required|in:accepted,rejected,counter_offer,completed',
        'counter_offer' => 'nullable|numeric|required_if:status,counter_offer',
        'musician_message' => 'nullable|string'
    ]);

    $hiringRequest = HiringRequest::with(['client', 'musicianProfile'])->findOrFail($id);

    $user = $request->user();
    if ($user->role === 'musico' && $hiringRequest->musician_profile_id !== $user->musicianProfile->id) {
        abort(403, 'No tienes permiso para modificar esta solicitud.');
    }

    // 2. Asignamos los valores al modelo
    $hiringRequest->status = $request->status;
    
    if ($request->status === 'counter_offer') {
        $hiringRequest->counter_offer = $request->counter_offer;
        $hiringRequest->musician_message = $request->musician_message;
    }

    $hiringRequest->save();

    $client = $hiringRequest->client;

    if ($client) {
        $notification = new HiringRequestStatusNotification($hiringRequest, $request->status);
        
        $client->notify($notification); 
        $notification->sendPush($client); 
    }

    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado y notificación enviada.'
        ]);
    }

    return redirect()->back()->with('success', 'Estado actualizado y notificación enviada.');
}
}