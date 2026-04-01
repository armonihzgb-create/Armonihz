<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadIdDocumentRequest;
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

    /**
     * Process the identity document upload.
     *
     * Role check, status guard, and file validation
     * are all handled by UploadIdDocumentRequest.
     */
    public function upload(UploadIdDocumentRequest $request)
    {
        $user    = Auth::user();
        $profile = $user->musicianProfile;

        $file     = $request->file('id_document');
        $filename = 'id_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('musician_ids', $filename, 'local');

        if (!$path) {
            return back()->with('error', 'Error del servidor: No se pudo escribir el archivo en el disco local.');
        }

        $profile->id_document_path    = $path;
        $profile->verification_status = 'pending';
        $profile->rejection_reason    = null; // Clear any previous rejection reason
        $profile->save();

        return back()->with('success', 'Documento subido correctamente. En breve será revisado.');
    }
}
