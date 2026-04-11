@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- PAGE HEADER --}}
    <div class="rv-header">
        <div>
            <div class="rv-eyebrow">
                <i data-lucide="star" style="width:14px;height:14px;color:#6c3fc5;"></i>
                RESEÑAS
            </div>
            <h1 class="rv-title">Reseñas y Calificaciones</h1>
            <p class="rv-subtitle">Opiniones de los clientes que ya trabajaron contigo.</p>
        </div>
        <div class="rv-sort-tabs">
            <button class="rv-sort-tab active">Más recientes</button>
            <button class="rv-sort-tab">Mejor calificación</button>
            <button class="rv-sort-tab">Sin responder</button>
        </div>
    </div>

    <div class="rv-layout">

        {{-- ── LEFT: Summary ───────────────────────── --}}
        <div class="rv-summary-col">

            <div class="rv-section-card">
                <h3 class="rv-card-title">
                    <i data-lucide="bar-chart-2" style="width:15px;height:15px;color:#6c3fc5;"></i>
                    Calificación general
                </h3>

                {{-- Big score --}}
                <div class="rv-score-block">
                    <span class="rv-big-score">{{ $averageRating }}</span>
                    <div>
                        <div class="rv-stars-row">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($averageRating))
                                    <i data-lucide="star" class="rv-star rv-star--filled"></i>
                                @else
                                    <i data-lucide="star" class="rv-star rv-star--empty"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="rv-score-sub">Basado en {{ $totalReviews }} reseñas</span>
                    </div>
                </div>

                {{-- Breakdown bars --}}
                <div class="rv-breakdown">
                    @foreach($breakdown as $b)
                        @php
                            $stars = $b['stars'];
                            $pct = $b['pct'];
                            $count = $b['count'];
                        @endphp
                    <div class="rv-bar-row">
                        <span class="rv-bar-label">{{ $stars }} ⭐</span>
                        <div class="rv-bar-bg">
                            <div class="rv-bar-fill" style="width:{{ $pct }};"></div>
                        </div>
                        <span class="rv-bar-count">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Highlight card --}}
            <div class="rv-highlight-card">
                <div class="rv-highlight-icon">
                    @if($totalReviews == 0)
                        <i data-lucide="info" style="width:18px;height:18px;color:#6c3fc5;"></i>
                    @elseif($averageRating >= 4.5)
                        <i data-lucide="trending-up" style="width:18px;height:18px;color:#6c3fc5;"></i>
                    @else
                        <i data-lucide="award" style="width:18px;height:18px;color:#6c3fc5;"></i>
                    @endif
                </div>
                <div>
                    @if($totalReviews == 0)
                        <span class="rv-highlight-title">Sin reseñas aún</span>
                        <p class="rv-highlight-text">Completa tu primer evento para empezar a recibir feedback de tus clientes.</p>
                    @elseif($averageRating >= 4.8)
                        <span class="rv-highlight-title">¡Reputación de élite!</span>
                        <p class="rv-highlight-text">Tu promedio de <strong>{{ $averageRating }}</strong> estrellas es excepcional. ¡Sigue con ese nivel!</p>
                    @elseif($averageRating >= 4.5)
                        <span class="rv-highlight-title">¡Excelente reputación!</span>
                        <p class="rv-highlight-text">Los clientes están muy satisfechos con tu trabajo. Estás haciendo un gran papel.</p>
                    @elseif($averageRating >= 4.0)
                        <span class="rv-highlight-title">¡Muy buena reputación!</span>
                        <p class="rv-highlight-text">Tienes una calificación sólida. Sigue respondiendo y mejorando cada día.</p>
                    @else
                        <span class="rv-highlight-title">Reputación en progreso</span>
                        <p class="rv-highlight-text">Sigue ofreciendo lo mejor en cada evento para elevar tu promedio actual.</p>
                    @endif
                </div>
            </div>

            {{-- Mini stats --}}
            <div class="rv-mini-stats">
                <div class="rv-mini-stat">
                    <span class="rv-mini-value">{{ $totalReviews }}</span>
                    <span class="rv-mini-label">Reseñas totales</span>
                </div>
                <div class="rv-mini-stat">
                    <span class="rv-mini-value" style="color:#16a34a;">{{ $stars5 }}</span>
                    <span class="rv-mini-label">Con 5 estrellas</span>
                </div>
                <div class="rv-mini-stat">
                    <span class="rv-mini-value" style="color:#2563eb;">{{ $replied }}</span>
                    <span class="rv-mini-label">Respondidas</span>
                </div>
            </div>

        </div>

        {{-- ── RIGHT: Reviews list ──────────────────── --}}
        <div class="rv-list-col">

            @if($reviews->isEmpty())
                <div class="rv-card text-center" style="padding:40px;">
                    <i data-lucide="star" style="width:40px;height:40px;color:#cbd5e1;margin-bottom:12px;margin-left:auto;margin-right:auto;display:block;"></i>
                    <h3 style="margin:0;color:#64748b;font-size:16px;text-align:center;">Aún no tienes reseñas</h3>
                    <p style="margin:8px 0 0;font-size:14px;color:#94a3b8;text-align:center;">Cuando completes eventos, tus clientes podrán dejar sus comentarios aquí.</p>
                </div>
            @endif

          @foreach($reviews as $rv)
            @php
                $clientName = $rv->client ? trim($rv->client->nombre . ' ' . $rv->client->apellido) : 'Cliente Anónimo';
                $initials = $rv->client ? strtoupper(substr($rv->client->nombre, 0, 1) . substr($rv->client->apellido, 0, 1)) : 'CA';
                $bg = '#ede9fe'; $color = '#6c3fc5';
                
                $eventName = 'Contratación Directa';
                if ($rv->castingApplication) {
                    $eventName = 'Casting: ' . ($rv->castingApplication->event->titulo ?? 'Evento');
                } elseif ($rv->hiringRequest) {
                    $eventName = 'Contratación Directa';
                }

                // Definimos qué foto usar basándonos en tu base de datos
                $imagenFinal = null;
                if ($rv->client) {
                    if (!empty($rv->client->fotoPerfil)) {
                        $cleanPath = ltrim($rv->client->fotoPerfil, '/');
                        $imagenFinal = url('/file/' . $cleanPath);
                    } elseif (!empty($rv->client->google_picture)) {
                        $imagenFinal = $rv->client->google_picture;
                    }
                }
            @endphp
            <div class="rv-card">
                {{-- Card top --}}
                <div class="rv-card-top">
                    <div class="rv-reviewer">
                        
                        {{-- Avatar dinámico --}}
                        <div class="rv-avatar" style="background:{{ $bg }}; color:{{ $color }}; overflow: hidden; padding: 0;">
                            @if($imagenFinal)
                                <img src="{{ $imagenFinal }}" alt="{{ $clientName }}" style="width: 100%; height: 100%; object-fit: cover; display: block; border-radius: inherit;">
                            @else
                                {{ $initials }}
                            @endif
                        </div>

                        <div>
                            <span class="rv-reviewer-name">{{ $clientName }}</span>
                            <span class="rv-reviewer-date">
                                <i data-lucide="calendar" style="width:11px;height:11px;"></i>
                                {{ $rv->created_at->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="rv-card-right">
                        <div class="rv-card-stars">
                            @for($s = 1; $s <= 5; $s++)
                                <i data-lucide="star"
                                   class="rv-star {{ $s <= $rv->rating ? 'rv-star--filled' : 'rv-star--empty' }}"
                                   style="width:15px;height:15px;"></i>
                            @endfor
                        </div>
                        <span class="rv-event-chip">
                            <i data-lucide="music" style="width:11px;height:11px;"></i>
                            {{ $eventName }}
                        </span>
                    </div>
                </div>

                {{-- Comment --}}
                @if($rv->comment)
                <p class="rv-comment">{{ $rv->comment }}</p>
                @else
                <p class="rv-comment" style="color:#94a3b8; font-style:normal; background:transparent; padding:0; margin-bottom:12px;">(Sin comentario escrito)</p>
                @endif

                {{-- Reply (if any) --}}
                @if($rv->response)
                <div class="rv-reply-box">
                    <div class="rv-reply-label">
                        <i data-lucide="corner-down-right" style="width:13px;height:13px;"></i>
                        Tu respuesta
                    </div>
                    <p class="rv-reply-text">{{ $rv->response }}</p>
                </div>
                @endif

                {{-- Footer --}}
                <div class="rv-card-footer">
                    @if(!$rv->response)
                    <button class="rv-reply-btn" onclick="responderResena({{ $rv->id }})">
                        <i data-lucide="corner-up-left" style="width:13px;height:13px;"></i>
                        Responder
                    </button>
                    @else
                    <span class="rv-replied-tag">✓ Respondida</span>
                    @endif
                </div>

            </div>
            @endforeach

        </div>
    </div>

    <style>
        /* ── Header ─────────────────────────────── */
        .rv-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 24px; padding-bottom: 22px;
            border-bottom: 1px solid #f1f5f9;
        }
        .rv-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .rv-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .rv-subtitle { font-size: 14px; color: #64748b; margin: 0; }
        .rv-sort-tabs { display: flex; background: #f1f5f9; border-radius: 10px; padding: 3px; gap: 2px; align-self: center; }
        .rv-sort-tab {
            padding: 7px 14px; border: none; border-radius: 7px;
            font-size: 12px; font-weight: 500; color: #64748b;
            background: transparent; cursor: pointer; transition: all .2s; white-space: nowrap;
        }
        .rv-sort-tab.active { background: #fff; color: #6c3fc5; font-weight: 700; box-shadow: 0 1px 4px rgba(0,0,0,.08); }

        /* ── Layout ──────────────────────────────── */
        .rv-layout { display: grid; grid-template-columns: 300px 1fr; gap: 22px; align-items: start; }
        .rv-section-card {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 16px; padding: 22px; margin-bottom: 14px;
        }
        .rv-card-title {
            display: flex; align-items: center; gap: 8px;
            font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 18px;
        }

        /* ── Score block ─────────────────────────── */
        .rv-score-block {
            display: flex; align-items: center; gap: 16px; margin-bottom: 20px;
        }
        .rv-big-score {
            font-size: 52px; font-weight: 900; color: #0f172a;
            line-height: 1; letter-spacing: -2px;
        }
        .rv-stars-row { display: flex; gap: 3px; margin-bottom: 4px; }
        .rv-star { width: 16px; height: 16px; }
        .rv-star--filled { fill: #f59e0b; color: #f59e0b; }
        .rv-star--empty  { fill: #e2e8f0; color: #e2e8f0; }
        .rv-score-sub { font-size: 12px; color: #94a3b8; }

        /* ── Breakdown bars ──────────────────────── */
        .rv-breakdown { display: flex; flex-direction: column; gap: 9px; }
        .rv-bar-row { display: flex; align-items: center; gap: 10px; }
        .rv-bar-label { font-size: 12px; color: #64748b; width: 36px; flex-shrink: 0; }
        .rv-bar-bg { flex: 1; height: 7px; background: #f1f5f9; border-radius: 999px; overflow: hidden; }
        .rv-bar-fill { height: 100%; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 999px; }
        .rv-bar-count { font-size: 12px; color: #94a3b8; width: 20px; text-align: right; flex-shrink: 0; }

        /* ── Highlight ───────────────────────────── */
        .rv-highlight-card {
            display: flex; align-items: flex-start; gap: 12px;
            background: linear-gradient(135deg, #f5f3ff, #eff6ff);
            border: 1.5px solid #e0e7ff; border-radius: 14px;
            padding: 16px 18px; margin-bottom: 14px;
        }
        .rv-highlight-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: #ede9fe; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .rv-highlight-title { display: block; font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
        .rv-highlight-text { font-size: 13px; color: #4c1d95; margin: 0; line-height: 1.5; }

        /* ── Mini stats ──────────────────────────── */
        .rv-mini-stats {
            display: grid; grid-template-columns: repeat(3,1fr); gap: 10px;
        }
        .rv-mini-stat {
            text-align: center; background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 12px; padding: 14px 8px;
        }
        .rv-mini-value { display: block; font-size: 20px; font-weight: 900; color: #0f172a; margin-bottom: 3px; }
        .rv-mini-label { font-size: 11px; color: #94a3b8; }

        /* ── Review Cards ────────────────────────── */
        .rv-list-col { display: flex; flex-direction: column; gap: 14px; }
        .rv-card {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 16px; padding: 20px 22px; transition: box-shadow .2s;
        }
        .rv-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.05); }

        .rv-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
        .rv-reviewer { display: flex; align-items: center; gap: 12px; }
        .rv-avatar {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; flex-shrink: 0;
        }
        .rv-reviewer-name { display: block; font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
        .rv-reviewer-date { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #94a3b8; }

        .rv-card-right { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
        .rv-card-stars { display: flex; gap: 2px; }
        .rv-event-chip {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600; padding: 3px 10px;
            border-radius: 999px; background: #fdf4ff; color: #a855f7;
            border: 1px solid #e9d5ff;
        }

        .rv-comment {
            font-size: 14px; color: #334155; line-height: 1.75;
            background: #f8fafc; border-radius: 10px; padding: 14px 16px;
            margin: 0 0 12px; font-style: italic;
        }

        /* Reply --*/
        .rv-reply-box {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 10px; padding: 12px 14px; margin-bottom: 12px;
        }
        .rv-reply-label {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 700; color: #16a34a;
            text-transform: uppercase; letter-spacing: .04em; margin-bottom: 6px;
        }
        .rv-reply-text { font-size: 13px; color: #166534; margin: 0; line-height: 1.6; }

        .rv-card-footer { display: flex; align-items: center; }
        .rv-reply-btn {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600; padding: 6px 14px;
            border-radius: 7px; border: 1.5px solid #e2e8f0;
            background: #f8fafc; color: #475569; cursor: pointer; transition: all .2s;
        }
        .rv-reply-btn:hover { border-color: #6c3fc5; color: #6c3fc5; }
        .rv-replied-tag { font-size: 12px; color: #16a34a; font-weight: 600; }

        @media (max-width: 860px) {
            .rv-layout { grid-template-columns: 1fr; }
            .rv-header { flex-direction: column; }
        }
    </style>

    <script>
        function responderResena(reviewId) {
            Swal.fire({
                title: 'Responder a la reseña',
                input: 'textarea',
                inputPlaceholder: 'Escribe tu respuesta aquí...',
                inputAttributes: {
                    'aria-label': 'Tu respuesta'
                },
                showCancelButton: true,
                confirmButtonText: 'Enviar respuesta',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#6c3fc5'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/reviews/${reviewId}/respond`;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    
                    const responseInput = document.createElement('input');
                    responseInput.type = 'hidden';
                    responseInput.name = 'response';
                    responseInput.value = result.value;
                    
                    form.appendChild(csrf);
                    form.appendChild(responseInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

@endsection