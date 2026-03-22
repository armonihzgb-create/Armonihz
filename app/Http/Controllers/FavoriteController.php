<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MusicianProfile;
use App\Models\Client; // ⬅️ IMPORTANTE: Asegúrate de importar el modelo Client

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtenemos el UID que inyectó tu middleware
        $uid = $request->attributes->get('firebase_uid');
        
        // 2. Buscamos al cliente en la base de datos
        $client = Client::where('firebase_uid', $uid)->firstOrFail();
        
        // Obtenemos los músicos favoritos con sus relaciones necesarias
        $favorites = $client->favoriteMusicians()->with(['user', 'genres', 'media'])->get();

        // Reutilizamos tu Resource para que el formato sea igual al del perfil
        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\MusicianProfileResource::collection($favorites)
        ]);
    }
    
    public function addFavorite(Request $request, $id)
    {
        $uid = $request->attributes->get('firebase_uid');
        $client = Client::where('firebase_uid', $uid)->firstOrFail();

        // Verificamos que el músico exista
        $musician = MusicianProfile::findOrFail($id);

        // Lo agregamos a favoritos sin duplicarlo (syncWithoutDetaching)
        $client->favoriteMusicians()->syncWithoutDetaching([$id]);

        return response()->json([
            'message' => 'Añadido a favoritos correctamente'
        ]);
    }

    public function removeFavorite(Request $request, $id)
    {
        $uid = $request->attributes->get('firebase_uid');
        $client = Client::where('firebase_uid', $uid)->firstOrFail();
        
        // Lo quitamos de la tabla pivote
        $client->favoriteMusicians()->detach($id);

        return response()->json([
            'message' => 'Eliminado de favoritos'
        ]);
    }
}