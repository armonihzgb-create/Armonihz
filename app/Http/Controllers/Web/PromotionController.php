<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $promotions = [];

        if ($user->role === 'musico' && $user->musicianProfile) {
            $promotions = $user->musicianProfile->promotions()->latest()->get();
        }

        return view('promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('promotions.create');
    }

    public function store(Request $request)
    {
        // 1. Validar los datos recibidos del formulario
        $request->validate([
            'plan_type' => 'required|string|in:Basico,Estandar,Premium',
            'receipt'   => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Máximo 5MB
        ]);

        $user = Auth::user();
        $profile = $user->musicianProfile;

        // Validar que el usuario sea músico y tenga perfil
        if ($user->role !== 'musico' || !$profile) {
            return redirect()->route('dashboard')->with('error', 'Acceso denegado.');
        }

        // 2. Manejar la subida del archivo (Usando tu misma lógica)
        $file = $request->file('receipt');
        $filename = 'receipt_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Usamos el disco 'public' para poder mostrar la imagen fácilmente en el panel de administrador
        $path = $file->storeAs('receipts', $filename, 'public');

        if (!$path) {
            return back()->with('error', 'Error del servidor: No se pudo guardar el comprobante.');
        }

        // 3. Crear el registro en estado "pendiente"
        Promotion::create([
            'musician_profile_id' => $profile->id,
            'plan_type'           => $request->input('plan_type'),
            'receipt_path'        => $path,
            'status'              => 'pendiente',
            'is_active'           => false,
            // valid_from y valid_until quedan nulos por ahora
        ]);

        // Redirigimos al index de promociones con un mensaje de éxito
        return redirect()->route('promotions.index')
            ->with('success', 'Comprobante enviado correctamente. Tu plan será activado en cuanto validemos el pago.');
    }
}

