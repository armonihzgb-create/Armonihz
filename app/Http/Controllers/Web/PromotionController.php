<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;

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
}
