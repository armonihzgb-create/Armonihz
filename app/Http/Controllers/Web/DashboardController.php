<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // If the user is an admin but hits the generic /dashboard route, redirect them to their specific panel
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Logic for the musician dashboard
        $user = Auth::user();

        $notificationsCount = $user->unreadNotifications()->count();
        $recentActivity = $user->notifications()->latest()->take(5)->get();

        $stats = [
            'notifications_count' => $notificationsCount,
            'pending_requests' => 0,
            'accepted_requests' => 0,
            'profile_completion' => 0,
            'profile_views' => 0,
            'promo_views' => 0, // NEW: Sum of views from all promotions
        ];

        $recentRequests = collect();
        $upcomingEvents = collect(); // NEW: Future accepted events

        if ($user->role === 'musico') {
            $profile = $user->musicianProfile;

            if ($profile) {
                // --- Stats from DB ---
                $stats['pending_requests'] = $profile->hiringRequests()->where('status', 'pending')->count();
                $stats['accepted_requests'] = $profile->hiringRequests()->where('status', 'accepted')->count();

                // --- Real profile completion score (same logic as ProfileController) ---
                $fields = [
                    'stage_name', 'bio', 'location', 'hourly_rate', 'profile_picture', 'phone',
                ];
                $filled = collect($fields)->filter(fn($f) => !empty($profile->$f))->count();
                $hasGenres = $profile->genres()->exists();
                $total = count($fields) + 1; // +1 for genres
                $stats['profile_completion'] = (int)round((($filled + ($hasGenres ? 1 : 0)) / $total) * 100);

                // Profile views: real counter incremented by the mobile app
                $stats['profile_views'] = $profile->profile_views;

                // --- Recent activity: last 5 hiring requests for this musician ---
                $recentRequests = $profile->hiringRequests()
                    ->with('client')
                    ->latest()
                    ->take(5)
                    ->get();

                // --- NEW: Total Promotion views ---
                $stats['promo_views'] = $profile->promotions()->sum('views') ?? 0;

                // --- NEW: Upcoming Events (accepted, date >= today) ---
                $upcomingEvents = $profile->hiringRequests()
                    ->with('client')
                    ->where('status', 'accepted')
                    ->whereDate('event_date', '>=', Carbon::today())
                    ->orderBy('event_date', 'asc')
                    ->take(4)
                    ->get();
            }
        }
        elseif ($user->role === 'cliente') {
            $stats['pending_requests'] = $user->clientRequests()->where('status', 'pending')->count();
            $stats['accepted_requests'] = $user->clientRequests()->where('status', 'accepted')->count();

            $recentRequests = $user->clientRequests()
                ->with('musicianProfile')
                ->latest()
                ->take(5)
                ->get();

            // --- NEW: Upcoming Events for Client ---
            $upcomingEvents = $user->clientRequests()
                ->with('musicianProfile')
                ->where('status', 'accepted')
                ->whereDate('event_date', '>=', Carbon::today())
                ->orderBy('event_date', 'asc')
                ->take(4)
                ->get();
        }

        return view('dashboard', compact('user', 'stats', 'recentActivity', 'recentRequests', 'upcomingEvents'));
    }
}
