<?php

namespace App\Http\Controllers;

use App\Models\ClientEvent;
use Illuminate\Http\Request;

class ClientEventController extends Controller
{
    // Obtener los eventos del cliente logueado
    public function index(Request $request)
    {
        // Asumiendo que tu middleware de Firebase inyecta el ID del cliente en el request
        // o puedes obtenerlo según como manejes tu autenticación actual.
        $firebaseUid = $request->attributes->get('firebase_uid'); 
        
        $eventos = ClientEvent::where('firebase_uid', $firebaseUid)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mapeamos para que coincida con lo que Android espera
        $formattedEvents = $eventos->map(function ($evento) {
            return [
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'tipoMusica' => $evento->tipo_musica,
                'fecha' => $evento->fecha,
                'ubicacion' => $evento->ubicacion,
                'propuestas' => 0 // Aquí luego conectarás con la tabla de propuestas
            ];
        });

        return response()->json($formattedEvents);
    }

    // Guardar un nuevo evento
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string',
            'tipoMusica' => 'required|string',
            'fecha' => 'required|string',
            'duracion' => 'required|string',
            'ubicacion' => 'required|string',
            'descripcion' => 'nullable|string',
            'presupuesto' => 'required|numeric',
        ]);

        $firebaseUid = $request->attributes->get('firebase_uid');

        $evento = ClientEvent::create([
            'firebase_uid' => $firebaseUid,
            'titulo' => $validated['titulo'],
            'tipo_musica' => $validated['tipoMusica'],
            'fecha' => $validated['fecha'],
            'duracion' => $validated['duracion'],
            'ubicacion' => $validated['ubicacion'],
            'descripcion' => $validated['descripcion'],
            'presupuesto' => $validated['presupuesto'],
        ]);

        return response()->json([
            'message' => 'Evento creado con éxito',
            'evento' => $evento
        ], 201);
    }
}