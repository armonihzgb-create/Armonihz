<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Client;

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
            'photoUrl' => asset('storage/' . $nombre)
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
            ? asset('storage/' . $cliente->fotoPerfil)
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
            'photoUrl' => asset("storage/perfiles/$fileName")
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
        // 🔥 CAMBIO: Obtenemos el usuario autenticado (el que tiene el fcm_token)
        $user = $request->user(); 

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado en la tabla users'], 401);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        // 🔥 CAMBIO: Ahora guardamos el user_id para vincular la tabla clients con users
        $cliente = Client::updateOrCreate(
            ['email' => $user->email], // Usamos email para evitar duplicados
            [
                'firebase_uid' => $user->firebase_uid,
                'nombre' => $request->name,
                'user_id' => $user->id, // <--- AQUÍ SE HACE LA VINCULACIÓN
            ]
        );

        return response()->json([
            'message' => 'Cliente sincronizado y vinculado',
            'client_id' => $cliente->id,
            'user_id' => $user->id
        ], 200);
    }

    /**
     * Guarda el token de Firebase del celular del usuario.
     */
    public function updateFcmToken(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        // 🔥 NOTA: Esto ya está bien, se guarda en la tabla 'users'
        $user = $request->user();
        if ($user) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
            return response()->json(['success' => true, 'message' => 'Token guardado en tabla users']);
        }

        return response()->json(['error' => 'No se encontró el usuario'], 404);
    }
}