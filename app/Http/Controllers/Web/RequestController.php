<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HiringRequest;
use App\Notifications\HiringRequestStatusNotification; // 1. FALTA ESTA IMPORTACIÓN

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'musico' && $user->musicianProfile) {
            $requests = $user->musicianProfile->hiringRequests()->with('client')->latest()->get();
        }
        else {
            $requests = $user->clientRequests()->with('musicianProfile')->latest()->get();
        }

        return view('requests', compact('requests'));
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
        $request->validate([
            'status' => 'required|in:accepted,rejected,counter_offer',
            // 'counter_price' => 'nullable|numeric'
        ]);

        // 2. CORREGIR EL NOMBRE DE LA RELACIÓN ('musician' a 'musicianProfile')
        $hiringRequest = HiringRequest::with(['client', 'musicianProfile'])->findOrFail($id);

        // 3. AGREGAR SEGURIDAD (Igual que en tu método show)
        $user = $request->user();
        if ($user->role === 'musico' && $hiringRequest->musician_profile_id !== $user->musicianProfile->id) {
            abort(403, 'No tienes permiso para modificar esta solicitud.');
        }

        $hiringRequest->status = $request->status;
        $hiringRequest->save();

        $client = $hiringRequest->client;

        if ($client) {
            $notification = new HiringRequestStatusNotification($hiringRequest, $request->status);
            
            $client->notify($notification); 
            $notification->sendPush($client); 
        }

        return redirect()->back()->with('success', 'Estado actualizado y notificación enviada.');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado y notificación enviada.'
            ]);
        }

        // Si la petición viene de un formulario normal (fallback):
        return redirect()->back()->with('success', 'Estado actualizado y notificación enviada.');
    
    }
}