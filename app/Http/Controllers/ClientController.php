<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\User;
use App\Services\FirebaseNotificationService;

class ClientController extends Controller
{

    private function getClient(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');

        if (!$firebaseUid) {
            abort(response()->json([
                'message' => 'Usuario no autenticado'
            ], 401));
        }

        $cliente = Client::where('firebase_uid', $firebaseUid)->first();

        if (!$cliente) {
            abort(response()->json([
                'message' => 'Cliente no encontrado'
            ], 404));
        }

        return $cliente;
    }

    public function uploadFotoPerfil(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $cliente = $this->getClient($request);

        if ($cliente->fotoPerfil && Storage::disk('public')->exists($cliente->fotoPerfil)) {
            Storage::disk('public')->delete($cliente->fotoPerfil);
        }

        $file = $request->file('foto');

        $nombre = 'perfiles/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->putFileAs(
            'perfiles',
            $file,
            basename($nombre)
        );

        $cliente->fotoPerfil = $nombre;
        $cliente->save();

        return response()->json([
            'message' => 'Foto actualizada',
            'photoUrl' => url('file/' . $nombre)
        ]);
    }

    public function deleteFotoPerfil(Request $request)
    {
        $cliente = $this->getClient($request);

        if ($cliente->fotoPerfil && Storage::disk('public')->exists($cliente->fotoPerfil)) {
            Storage::disk('public')->delete($cliente->fotoPerfil);
        }

        $cliente->fotoPerfil = null;
        $cliente->save();

        return response()->json([
            'message' => 'Foto eliminada'
        ]);
    }

    public function profile(Request $request)
    {
        $cliente = $this->getClient($request);

        return response()->json([
            'photoUrl' => $cliente->fotoPerfil
                ? url('file/' . $cliente->fotoPerfil)
                : null
        ]);
    }

    public function syncGooglePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->photo) {
            return response()->json([
                'message' => 'Foto ya existe'
            ]);
        }

        $request->validate([
            'photoUrl' => 'required|url'
        ]);

        try {

            $imageContents = file_get_contents($request->photoUrl);

            if (!$imageContents) {
                return response()->json([
                    'message' => 'No se pudo descargar la imagen'
                ], 400);
            }

            $fileName = 'perfiles/google_' . time() . '.jpg';

            Storage::disk('public')->put($fileName, $imageContents);

            $user->photo = $fileName;
            $user->save();

            return response()->json([
                'message' => 'Foto sincronizada',
                'photoUrl' => url("file/$fileName")
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al sincronizar foto'
            ], 500);

        }
    }

    public function deleteAccount(Request $request)
    {
        $cliente = $this->getClient($request);

        if ($cliente->fotoPerfil && Storage::disk('public')->exists($cliente->fotoPerfil)) {
            Storage::disk('public')->delete($cliente->fotoPerfil);
        }

        $cliente->delete();

        return response()->json([
            'message' => 'Cuenta eliminada correctamente'
        ]);
    }

    public function syncClient(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');

        if (!$firebaseUid) {
            return response()->json([
                'message' => 'Token de Firebase no detectado'
            ], 401);
        }

        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'firebase_uid' => $firebaseUid,
                'role' => 'cliente'
            ]
        );

        Client::updateOrCreate(
            ['firebase_uid' => $firebaseUid],
            [
                'user_id' => $user->id,
                'nombre' => $request->name,
                'email' => $request->email
            ]
        );

        return response()->json([
            'message' => 'Cliente sincronizado correctamente',
            'user_id' => $user->id
        ]);
    }

    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $cliente = $this->getClient($request);

        $cliente->update([
            'fcm_token' => $request->fcm_token
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token guardado correctamente'
        ]);
    }

    public function testNotification(FirebaseNotificationService $fcm)
    {
        $client = Client::first();

        if (!$client || !$client->fcm_token) {
            return "No hay cliente con token";
        }

        $fcm->send(
            $client->fcm_token,
            "Armonihz",
            "Notificación de prueba"
        );

        return "Notificación enviada";
    }

    // Obtener la lista de favoritos del cliente
    public function getFavorites(Request $request)
    {
        $cliente = $this->getClient($request);
        
        // Traemos a los músicos favoritos. 
        // (Ajusta los 'select' según los datos que ocupes mostrar en la tarjeta de favoritos)
        $favorites = $cliente->favorites()
            ->select('musicians.id', 'musicians.stage_name', 'musicians.profile_picture', 'musicians.location') 
            ->get();

        return response()->json([
            'data' => $favorites
        ]);
    }

    // Agregar o quitar de favoritos (Toggle)
    public function toggleFavorite(Request $request, $musicianId)
    {
        $cliente = $this->getClient($request);

        // Verifica si ya está en favoritos
        $isFavorite = $cliente->favorites()->where('musician_id', $musicianId)->exists();

        if ($isFavorite) {
            // Si ya existe, lo quitamos (Dislike)
            $cliente->favorites()->detach($musicianId);
            return response()->json([
                'message' => 'Eliminado de favoritos',
                'is_favorite' => false
            ]);
        } else {
            // Si no existe, lo agregamos (Like)
            $cliente->favorites()->attach($musicianId);
            return response()->json([
                'message' => 'Añadido a favoritos',
                'is_favorite' => true
            ]);
        }
    }
}

