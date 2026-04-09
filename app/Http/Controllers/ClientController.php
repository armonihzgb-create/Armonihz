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

    // ✅ Si apellido es NULL en BD, devolvemos string vacío para evitar problemas
    return response()->json([
        'nombre'   => $cliente->nombre   ?? '',
        'apellido' => $cliente->apellido ?? '',
        'email'    => $cliente->email,
        'telefono' => $cliente->telefono,
        'terminos_aceptados' => (bool) $cliente->terminos_aceptados,
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

        // 1. Eliminar foto de perfil del storage si existe
        if ($cliente->fotoPerfil && Storage::disk('public')->exists($cliente->fotoPerfil)) {
            Storage::disk('public')->delete($cliente->fotoPerfil);
        }

        // 2. Guardamos el ID del User antes de que el cliente sea destruido
        $userId = $cliente->user_id;

        // 3. Eliminamos el registro de la tabla 'clients'
        $cliente->delete();

        // 4. Buscamos y eliminamos el registro de la tabla 'users'
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->delete();
            }
        }

        return response()->json([
            'message' => 'Cuenta eliminada completamente de la base de datos'
        ]);
    }

public function syncClient(Request $request)
    {
        $firebaseUid = $request->attributes->get('firebase_uid');

        if (!$firebaseUid) {
            return response()->json(['message' => 'Token de Firebase no detectado'], 401);
        }

        $nombreCompleto = trim($request->name ?? '');
        $partes = explode(' ', $nombreCompleto, 2);

        $nombre   = !empty($request->nombre)   ? $request->nombre   : ($partes[0] ?? '');
        $apellido = !empty($request->apellido) ? $request->apellido : ($partes[1] ?? '');
        
        // Extraemos la URL de la foto de Google que manda la app
        $googlePhotoUrl = $request->photoUrl ?? $request->picture ?? null;

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            $user = User::create([
                'email'        => $request->email,
                'name'         => $nombreCompleto,
                'firebase_uid' => $firebaseUid,
                'role'         => 'cliente',
                'password'     => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            ]);
        } else {
            // Actualizamos name solo si está vacío o si se quedó como 'Usuario'
            $nuevoName = (empty($user->name) || $user->name === 'Usuario') ? $nombreCompleto : $user->name;
            $user->update([
                'name'         => $nuevoName,
                'firebase_uid' => $firebaseUid,
            ]);
        }

        $cliente = Client::where('firebase_uid', $firebaseUid)->first();

        if ($cliente) {
            $changed = false;

            if (!$cliente->user_id) {
                $cliente->user_id = $user->id;
                $changed = true;
            }

            // 🔥 LA MAGIA AQUÍ: Si dice 'Usuario', lo sobreescribimos con el nombre real
            if (empty($cliente->nombre) || $cliente->nombre === 'Usuario') {
                $cliente->nombre = $nombre;
                $changed = true;
            }

            if (empty($cliente->apellido)) {
                $cliente->apellido = $apellido;
                $changed = true;
            }

            // Guardamos la foto de Google como respaldo si no la tiene
            if (empty($cliente->google_picture) && $googlePhotoUrl) {
                $cliente->google_picture = $googlePhotoUrl;
                $changed = true;
            }

            if ($changed) {
                $cliente->save();
            }

        } else {
            Client::create([
                'firebase_uid'   => $firebaseUid,
                'user_id'        => $user->id,
                'nombre'         => $nombre,
                'apellido'       => $apellido,
                'email'          => $request->email,
                'google_picture' => $googlePhotoUrl
            ]);
        }

        return response()->json([
            'message' => 'Cliente sincronizado correctamente',
            'user_id' => $user->id
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
        
        // Traemos a los músicos favoritos desde musician_profiles
        $favorites = $cliente->favorites()
            ->select(
                'musician_profiles.id', 
                'musician_profiles.stage_name', 
                'musician_profiles.profile_picture', 
                'musician_profiles.location'
            ) 
            ->get();

        // Puedes mapear la respuesta si necesitas formatear la URL de la imagen como lo haces en "profile"
        $formattedFavorites = $favorites->map(function ($musician) {
            return [
                'id' => $musician->id,
                'stage_name' => $musician->stage_name,
                'location' => $musician->location,
                'profile_picture' => $musician->profile_picture ? url('file/' . $musician->profile_picture) : null
            ];
        });

        return response()->json([
            'data' => $formattedFavorites
        ]);
    }

    // Agregar o quitar de favoritos (Toggle)
    public function toggleFavorite(Request $request, $musicianId)
    {
        $cliente = $this->getClient($request);

        // Verifica si ya está en favoritos usando el nombre correcto de la columna
        $isFavorite = $cliente->favorites()->where('musician_profile_id', $musicianId)->exists();

        if ($isFavorite) {
            // Si ya existe, lo quitamos
            $cliente->favorites()->detach($musicianId);
            return response()->json([
                'message' => 'Eliminado de favoritos',
                'is_favorite' => false
            ]);
        } else {
            // Si no existe, lo agregamos
            $cliente->favorites()->attach($musicianId);
            return response()->json([
                'message' => 'Añadido a favoritos',
                'is_favorite' => true
            ]);
        }
    }

   public function updateProfile(Request $request)
{
    $request->validate([
        'nombre'   => 'required|string|min:2|max:50',
        'apellido' => 'required|string|min:2|max:50',
        'telefono' => 'nullable|string|max:15',
    ]);

    $cliente = $this->getClient($request);

    // ✅ Guardar nombre y apellido por separado
    $cliente->nombre   = trim($request->nombre);
    $cliente->apellido = trim($request->apellido);
    $cliente->telefono = $request->telefono;
    $cliente->terminos_aceptados = true;
    $cliente->save();

    // ✅ Sincronizar en tabla users con nombre completo
    $user = User::find($cliente->user_id);
    if ($user) {
        $user->name = trim($request->nombre . ' ' . $request->apellido);
        $user->save();
    }

    return response()->json([
        'message'  => 'Perfil actualizado correctamente',
        'cliente'  => $cliente
    ]);
}

public function updateFcmToken(Request $request)
    {
        // 1. Validamos que la petición incluya el token
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        // 2. Obtenemos el cliente autenticado usando tu método helper
        $cliente = $this->getClient($request);

        // 3. Actualizamos y guardamos
        $cliente->fcm_token = $request->fcm_token;
        $cliente->save();

        // 4. Retornamos respuesta de éxito
        return response()->json([
            'message' => 'FCM Token actualizado correctamente'
        ]);
    }
}

