<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MusicianProfile;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->user()->client;
        
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
        // 1. Obtenemos el cliente autenticado
        // (Ajusta esto dependiendo de cómo obtengas al cliente desde el usuario logueado)
        $client = $request->user()->client; 

        // 2. Verificamos que el músico exista
        $musician = MusicianProfile::findOrFail($id);

        // 3. Lo agregamos a favoritos sin duplicarlo (syncWithoutDetaching)
        $client->favoriteMusicians()->syncWithoutDetaching([$id]);

        return response()->json([
            'message' => 'Añadido a favoritos correctamente'
        ]);
    }

    public function removeFavorite(Request $request, $id)
    {
        $client = $request->user()->client;
        
        // Lo quitamos de la tabla pivote
        $client->favoriteMusicians()->detach($id);

        return response()->json([
            'message' => 'Eliminado de favoritos'
        ]);
    }
}
