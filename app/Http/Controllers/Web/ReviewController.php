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

        $reviews = $musician->reviews()->with([
            'client.client', // To get client profile info (Name, Photo)
            'hiringRequest',
            'castingApplication.event'
        ])->latest()->get();

        $averageRating = number_format($musician->averageRating(), 1);
        $totalReviews = $reviews->count();
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
