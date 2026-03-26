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
    // Agrega esta función debajo de tu función show()
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $hiringRequest = HiringRequest::findOrFail($id);

        // Seguridad: Asegurarnos de que este músico es el dueño de la solicitud
        if ($user->role === 'musico' && $hiringRequest->musician_profile_id !== $user->musicianProfile->id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        // Validar que el estado sea correcto
        $request->validate([
            'status' => 'required|in:accepted,rejected'
        ]);

        // Actualizar la base de datos
        $hiringRequest->status = $request->status;
        $hiringRequest->save();

        return response()->json([
            'success' => true, 
            'message' => 'Estado actualizado correctamente'
        ]);
    }
}
