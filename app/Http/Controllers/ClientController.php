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
    public function uploadFotoPerfil(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $firebaseUid = $request->attributes->get('firebase_uid');

        if (!$firebaseUid) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Crear cliente si no existe
        $cliente = Client::firstOrCreate([
            'firebase_uid' => $firebaseUid
        ]);

        // Eliminar foto anterior si existe
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
        ], 200);
    }

    public function deleteFotoPerfil(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');

        if (!$firebaseUid) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $cliente = Client::where('firebase_uid', $firebaseUid)->first();

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        if ($cliente->fotoPerfil && Storage::disk('public')->exists($cliente->fotoPerfil)) {
            Storage::disk('public')->delete($cliente->fotoPerfil);
        }

        $cliente->fotoPerfil = null;
        $cliente->save();

        return response()->json([
            'message' => 'Foto eliminada'
        ], 200);
    }
    public function profile(Request $request)
{
    $firebaseUid = $request->attributes->get('firebase_uid');

    $cliente = Client::where('firebase_uid', $firebaseUid)->first();

    return response()->json([
       'photoUrl' => $cliente && $cliente->fotoPerfil
    ? url('file/' . $cliente->fotoPerfil)
    : null
    ]);
}

public function syncGooglePhoto(Request $request)
{
    $user = $request->user();

    if ($user->photo) {
        // Ya tiene foto en Laravel → no sobrescribir
        return response()->json(['message' => 'Foto ya existe'], 200);
    }

    $request->validate([
        'photoUrl' => 'required|url'
    ]);

    try {

        $imageContents = file_get_contents($request->photoUrl);

        if (!$imageContents) {
            return response()->json(['message' => 'No se pudo descargar la imagen'], 400);
        }

        $fileName = 'google_' . time() . '.jpg';

        Storage::disk('public')->put("perfiles/$fileName", $imageContents);

        $user->photo = $fileName;
        $user->save();

        return response()->json([
            'message' => 'Foto sincronizada',
            'photoUrl' => url("file/perfiles/$fileName")
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al sincronizar foto'
        ], 500);
    }
}

public function deleteAccount(Request $request)
{
    $firebaseUid = $request->attributes->get('firebase_uid');

    if (!$firebaseUid) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    $cliente = Client::where('firebase_uid', $firebaseUid)->first();

  if (!$cliente) {
    return response()->json([
        'message' => 'Cuenta eliminada (sin registro en BD)'
    ], 200);
}

    // 1. Eliminar foto de perfil si existe
    if ($cliente->fotoPerfil && Storage::disk('public')->exists($cliente->fotoPerfil)) {
        Storage::disk('public')->delete($cliente->fotoPerfil);
    }

    //  2. Eliminar relaciones (si tienes tablas relacionadas)
    // Ejemplo:
    // $cliente->favoritos()->delete();
    // $cliente->resenas()->delete();

    //  3. Eliminar cliente
    $cliente->delete();

    return response()->json([
        'message' => 'Cuenta eliminada correctamente'
    ], 200);
}

public function syncClient(Request $request)
{
    $firebaseUid = $request->attributes->get('firebase_uid');

    if (!$firebaseUid) {
        return response()->json(['message' => 'Token de Firebase no detectado'], 401);
    }

    // 1. Buscamos o creamos el Usuario en la tabla 'users'
    $user = User::updateOrCreate(
        ['email' => $request->email], // Buscamos por correo
        [
            'name' => $request->name,
            'firebase_uid' => $firebaseUid,
            'role' => 'cliente'
        ]
    );

    // 2. Vinculamos al Cliente con el ID de ese Usuario
    $cliente = Client::updateOrCreate(
        ['firebase_uid' => $firebaseUid],
        [
            'user_id' => $user->id, // <--- Aquí ya no será NULL
            'nombre' => $request->name,
            'email' => $request->email,
        ]
    );

    return response()->json([
        'message' => 'Cliente sincronizado correctamente',
        'user_id' => $user->id
    ], 200);
}

    /**
     * Guarda el token de notificaciones.
     */
   public function updateFcmToken(Request $request)
{
    $request->validate([
        'fcm_token' => 'required|string',
    ]);

    $firebaseUid = $request->attributes->get('firebase_uid');

    if (!$firebaseUid) {
        return response()->json([
            'error' => 'Usuario no autenticado'
        ], 401);
    }

    // Buscar cliente por Firebase UID
    $client = Client::where('firebase_uid', $firebaseUid)->first();

    if (!$client) {
        return response()->json([
            'error' => 'Cliente no encontrado'
        ], 404);
    }

    // Guardar token
    $client->update([
        'fcm_token' => $request->fcm_token
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Token guardado correctamente'
    ], 200);
}



public function testNotification(FirebaseNotificationService $fcm)
{
    $client = Client::first();

    $fcm->send(
        $client->fcm_token,
        "Armonihz",
        "Notificación de prueba"
    );

    return "Notificación enviada";
}
}