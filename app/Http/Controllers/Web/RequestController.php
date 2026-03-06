<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HiringRequest;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'musico' && $user->musicianProfile) {
            $requests = $user->musicianProfile->hiringRequests()->with('client')->latest()->get();
        }
        else {
            $requests = $user->clientRequests()->with('musicianProfile')->latest()->get();
        }

        return view('requests', compact('requests'));
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $hiringRequest = HiringRequest::with(['client', 'musicianProfile.user'])->findOrFail($id);

        // Security check
        if ($user->role === 'musico' && $hiringRequest->musician_profile_id !== $user->musicianProfile->id) {
            abort(403);
        }

        if ($user->role === 'cliente' && $hiringRequest->client_id !== $user->id) {
            abort(403);
        }

        return view('requests.show', compact('hiringRequest'));
    }
}
