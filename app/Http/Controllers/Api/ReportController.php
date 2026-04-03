<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, $musicianId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // Asumiendo que tu middleware de Firebase autentica al usuario y lo pone en $request->user()
        Report::create([
            'client_id' => $request->user()->id, // Asegúrate de que tu middleware Firebase devuelve un Client aquí
            'musician_profile_id' => $musicianId,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Reporte enviado exitosamente.'
        ], 201);
    }
}