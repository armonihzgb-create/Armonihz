<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HiringRequest;

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
        $user = $request->user();
        $hiringRequest = HiringRequest::findOrFail($id);

        // Seguridad: Asegurarnos de que este músico es el dueño de la solicitud
        if ($user->role === 'musico' && $hiringRequest->musician_profile_id !== $user->musicianProfile->id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        // Una solicitud aceptada solo puede pasar a "completed"
        if ($hiringRequest->status === 'accepted' && $request->status !== 'completed') {
             return response()->json(['success' => false, 'message' => 'Una solicitud aceptada solo puede marcarse como completada.'], 400);
        }

        // Si ya está completada o rechazada, no se puede mover más
        if (in_array($hiringRequest->status, ['completed', 'rejected'])) {
            return response()->json(['success' => false, 'message' => 'Esta solicitud ya tiene un estado final.'], 400);
        }

        // Validar que el estado sea correcto (agregamos counter_offer)
        $request->validate([
            'status' => 'required|in:accepted,rejected,counter_offer,completed',
            'musician_message' => 'nullable|string',
            'counter_offer' => 'nullable|numeric|min:0'
        ]);

        // Actualizar la base de datos
        $hiringRequest->status = $request->status;
        
        // Si es contraoferta, guardamos el mensaje y el nuevo precio
        if ($request->status === 'counter_offer') {
            $hiringRequest->musician_message = $request->musician_message;
            $hiringRequest->counter_offer = $request->counter_offer;
        }

        $hiringRequest->save();

        return response()->json([
            'success' => true, 
            'message' => 'Estado actualizado correctamente'
        ]);
    }
}
