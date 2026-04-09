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

                // --- NEW: Upcoming Events Unified ---
                $hiringEvents = $profile->hiringRequests()
                    ->with('client')
                    ->where('status', 'accepted')
                    ->whereDate('event_date', '>=', Carbon::today())
                    ->get()
                    ->map(function ($req) {
                        return (object) [
                            'type' => 'hiring',
                            'title' => 'Evento de ' . ($req->client->name ?? 'Cliente'),
                            'date' => Carbon::parse($req->event_date),
                            'location' => $req->event_location,
                        ];
                    });

                $castingEvents = \App\Models\CastingApplication::where('musician_profile_id', $profile->id)
                    ->whereIn('status', ['accepted', 'completed']) // Assuming accepted castings
                    ->with('event')
                    ->get()
                    ->map(function ($app) {
                        $start = null;
                        if ($app->event && $app->event->fecha) {
                            try {
                                [$start, $end] = \App\Models\ClientEvent::parseDateTimeRange($app->event->fecha, $app->event->duracion);
                            } catch (\Exception $e) {
                                $start = null;
                            }
                        }
                        
                        // Keep only future castings
                        if (!$start || $start->lessThan(Carbon::today())) {
                            return null;
                        }

                        return (object) [
                            'type' => 'casting',
                            'title' => $app->event->titulo ?? 'Evento de Casting',
                            'date' => $start,
                            'location' => $app->event->ubicacion ?? 'Ubicación no especificada',
                        ];
                    })->filter(); // Remove nulls

                $calendarEvents = \App\Models\MusicianCalendarEvent::where('musician_profile_id', $profile->id)
                    ->whereDate('start', '>=', Carbon::today())
                    ->get()
                    ->map(function ($cal) {
                        return (object) [
                            'type' => 'blocked',
                            'title' => $cal->title ?? 'Fecha bloqueada',
                            'date' => Carbon::parse($cal->start),
                            'location' => 'Calendario Personal',
                        ];
                    });

                $upcomingEvents = $hiringEvents->concat($castingEvents)->concat($calendarEvents)
                    ->sortBy('date') // Order perfectly chronologically 
                    ->take(4)
                    ->values();
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

            // --- NEW: Upcoming Events for Client Unified shape ---
            $upcomingEvents = $user->clientRequests()
                ->with('musicianProfile')
                ->where('status', 'accepted')
                ->whereDate('event_date', '>=', Carbon::today())
                ->orderBy('event_date', 'asc')
                ->take(4)
                ->get()
                ->map(function ($req) {
                    return (object) [
                        'type' => 'hiring',
                        'title' => 'Concierto de ' . ($req->musicianProfile->stage_name ?? 'Músico'),
                        'date' => Carbon::parse($req->event_date),
                        'location' => $req->event_location,
                    ];
                });
        }

        // --- Welcome Modal: sugerencias dinámicas según perfil ---
        $welcomeTips = [];
        if ($user->role === 'musico') {
            $profile = $profile ?? $user->musicianProfile;
            if ($profile) {
                if (empty($profile->profile_picture)) {
                    $welcomeTips[] = [
                        'icon'    => 'camera',
                        'color'   => '#6c3fc5',
                        'message' => 'Sube una <strong>foto de perfil</strong> para dar mayor confianza a los clientes.',
                        'action'  => route('profile'),
                        'label'   => 'Subir foto',
                    ];
                }
                if (empty($profile->bio)) {
                    $welcomeTips[] = [
                        'icon'    => 'file-text',
                        'color'   => '#2f93f5',
                        'message' => 'Escribe tu <strong>biografía</strong>: cuéntales a los clientes tu historia y estilo musical.',
                        'action'  => route('profile'),
                        'label'   => 'Agregar bio',
                    ];
                }
                if (empty($profile->location)) {
                    $welcomeTips[] = [
                        'icon'    => 'map-pin',
                        'color'   => '#f59e0b',
                        'message' => 'Indica tu <strong>ciudad</strong> para recibir solicitudes de clientes cercanos.',
                        'action'  => route('profile'),
                        'label'   => 'Agregar ciudad',
                    ];
                }
                if (empty($profile->hourly_rate)) {
                    $welcomeTips[] = [
                        'icon'    => 'dollar-sign',
                        'color'   => '#10b981',
                        'message' => 'Define tu <strong>tarifa por hora</strong> para que los clientes sepan qué esperar.',
                        'action'  => route('profile'),
                        'label'   => 'Definir tarifa',
                    ];
                }
                $hasMedia = $profile->media()->exists();
                if (!$hasMedia) {
                    $welcomeTips[] = [
                        'icon'    => 'video',
                        'color'   => '#ef4444',
                        'message' => 'Los perfiles con <strong>fotos y videos</strong> tienen un 80% más de efectividad. ¡Sube una demostración!',
                        'action'  => route('profile'),
                        'label'   => 'Subir multimedia',
                    ];
                }

                // Verification status tip — injected at the START of tips so it is always the first one seen
                $verificationStatus = $profile->verification_status ?? 'unverified';
                if ($verificationStatus === 'unverified') {
                    array_unshift($welcomeTips, [
                        'icon'    => 'shield',
                        'color'   => '#f59e0b',
                        'message' => 'Tu perfil <strong>no es visible</strong> en la app móvil. Sube tu documento de identidad para iniciar la verificación.',
                        'action'  => route('id_verification.notice'),
                        'label'   => 'Verificar identidad',
                    ]);
                } elseif ($verificationStatus === 'pending') {
                    array_unshift($welcomeTips, [
                        'icon'    => 'clock',
                        'color'   => '#3b82f6',
                        'message' => 'Tu documento está <strong>en revisión</strong>. Tu perfil será visible en la app móvil una vez que un administrador lo apruebe.',
                        'action'  => null,
                        'label'   => null,
                    ]);
                } elseif ($verificationStatus === 'rejected') {
                    array_unshift($welcomeTips, [
                        'icon'    => 'alert-circle',
                        'color'   => '#ef4444',
                        'message' => 'Tu verificación fue <strong>rechazada</strong>. ' . (e($profile->rejection_reason) ?: 'Revisa el motivo y vuelve a subir tu documento.'),
                        'action'  => route('id_verification.notice'),
                        'label'   => 'Subir nuevo documento',
                    ]);
                }
            }
        }

        return view('dashboard', compact('user', 'stats', 'recentActivity', 'recentRequests', 'upcomingEvents', 'welcomeTips'));
    }
}
