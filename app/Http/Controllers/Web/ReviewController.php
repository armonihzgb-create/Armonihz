<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'musico') {
            return redirect('/dashboard');
        }

        $musician = $user->musicianProfile;
        
        if (!$musician) {
            return redirect('/profile')->with('error', 'Crea tu perfil de músico primero.');
        }

        // Cargamos las reseñas con el cliente y el evento
        $reviews = $musician->reviews()->with([
            'client', // 👈 Corregido: Solo llamamos al cliente asociado a la reseña
            'hiringRequest'
            // 'castingApplication.event' // 👈 Descomenta esto solo si también recibes reseñas por castings
        ])->latest()->get();

        $totalReviews = $reviews->count();
        
        // 👈 Calculamos el promedio de forma segura (si hay 0 reseñas, devuelve 0.0)
        $averageRating = $totalReviews > 0 ? number_format($reviews->avg('rating'), 1) : '0.0';
        
        $stars5 = $reviews->where('rating', 5)->count();
        $replied = $reviews->whereNotNull('response')->count();

        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $reviews->where('rating', $i)->count();
            $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $breakdown[] = ['stars' => $i, 'pct' => $pct . '%', 'count' => $count];
        }

        return view('reviews', compact(
            'reviews', 'musician', 'averageRating', 'totalReviews', 'stars5', 'replied', 'breakdown'
        ));
    }

    public function respond(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        $musician = $user->musicianProfile;

        $review = Review::where('id', $id)
            ->where('musician_profile_id', $musician->id)
            ->firstOrFail();

        $review->update(['response' => $request->response]);

        return redirect()->back()->with('success', 'Respuesta enviada exitosamente.');
    }
}