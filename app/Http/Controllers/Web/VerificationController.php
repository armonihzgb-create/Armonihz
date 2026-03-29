<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function notice()
    {
        $user = Auth::user();
        
        if ($user->role !== 'musico') {
            return redirect()->route('dashboard');
        }

        $profile = $user->musicianProfile;

        // Si ya está aprobado, no tiene sentido estar aquí
        if ($profile && $profile->verification_status === 'approved') {
            return redirect()->route('dashboard');
        }

        return view('musician.verification', compact('profile'));
    }

    public function upload(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'musico') {
            return abort(403);
        }

        $profile = $user->musicianProfile;

        // Si ya está aprobado o pendiente, no debería poder subir nada
        if ($profile && in_array($profile->verification_status, ['approved', 'pending'])) {
            return back()->with('error', 'Status actual no permite subir documentos.');
        }

        $request->validate([
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ], [
            'id_document.required' => 'Debes seleccionar un archivo.',
            'id_document.mimes' => 'El archivo debe ser JPG, PNG o PDF.',
            'id_document.max' => 'El archivo no debe exceder los 5MB.',
        ]);

        if ($request->hasFile('id_document')) {
            $file = $request->file('id_document');
            $filename = 'id_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Guardamos en storage/app/musician_ids (fuera del public explicitando el disco local)
            $path = $file->storeAs('musician_ids', $filename, 'local');

            // Actualizamos o creamos el perfil (en caso de que no tenga, aunque el registro lo crea)
            if ($profile) {
                $profile->id_document_path = $path;
                $profile->verification_status = 'pending';
                $profile->rejection_reason = null; // Limpiamos el motivo de rechazo anterior si existía
                $profile->save();
            }

            return back()->with('success', 'Documento subido correctamente. En breve será revisado.');
        }

        return back()->with('error', 'Error al procesar el archivo.');
    }
}
