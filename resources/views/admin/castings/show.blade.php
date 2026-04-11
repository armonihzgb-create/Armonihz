@extends('layouts.admin')

@section('admin-content')

    <div class="page-header-premium">
        <div class="header-info">
            <a href="{{ route('admin.castings.index') }}" class="back-pill">
                <i data-lucide="arrow-left"></i>
                <span>Volver a Eventos</span>
            </a>
            <div class="title-with-icon">
                <h2>Detalle del Evento</h2>
                <i data-lucide="info" style="color: #6366f1; width: 24px; height: 24px; opacity: 0.2;"></i>
            </div>
            <p>Supervisión administrativa del casting: <strong>{{ $event->titulo }}</strong></p>
        </div>
        <div class="header-status">
            @php
                $statusMap = [
                    'open'      => ['class' => 'badge-premium-success', 'label' => 'Abierto', 'icon' => 'globe'],
                    'completed' => ['class' => 'badge-premium-blue',    'label' => 'Completado', 'icon' => 'check-circle'],
                    'canceled'  => ['class' => 'badge-premium-danger',  'label' => 'Cancelado', 'icon' => 'slash'],
                    'inactive'  => ['class' => 'badge-premium-default', 'label' => 'Inactivo', 'icon' => 'eye-off'],
                ];
                $st = $statusMap[$event->status] ?? ['class' => 'badge-premium-warning', 'label' => ucfirst($event->status), 'icon' => 'help-circle'];
            @endphp
            <span class="badge-fancy {{ $st['class'] }}">
                <i data-lucide="{{ $st['icon'] }}"></i>
                {{ $st['label'] }}
            </span>
        </div>
    </div>

    <div class="details-grid-premium">
        {{-- Left Column: Event Core Info --}}
        <div class="main-column">
            <div class="premium-card">
                <div class="card-header-premium">
                    <i data-lucide="calendar"></i>
                    <h3>Información del Casting</h3>
                </div>
                <div class="card-body-premium">
                    <div class="info-highlight-fancy">
                        <div class="highlight-item">
                            <label>Título del Evento</label>
                            <h4>{{ $event->titulo }}</h4>
                        </div>
                        <div class="highlight-meta">
                            <div class="meta-pill">
                                <i data-lucide="music"></i>
                                <span>{{ $event->genre ? $event->genre->name : 'Género no especificado' }}</span>
                            </div>
                            <div class="meta-pill">
                                <i data-lucide="dollar-sign"></i>
                                <span>{{ $event->presupuesto ? '$' . number_format($event->presupuesto, 0) : 'A negociar' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="details-list-grid">
                        <div class="detail-block">
                            <i data-lucide="map-pin"></i>
                            <div class="block-content">
                                <label>Ubicación</label>
                                <span>{{ $event->ubicacion ?? 'No especificada' }}</span>
                            </div>
                        </div>
                        <div class="detail-block">
                            <i data-lucide="calendar-days"></i>
                            <div class="block-content">
                                <label>Fecha Programada</label>
                                <span>{{ \Carbon\Carbon::parse(str_replace('/', '-', $event->fecha))->locale('es')->isoFormat('dddd D [de] MMMM') }}</span>
                            </div>
                        </div>
                        <div class="detail-block">
                            <i data-lucide="clock"></i>
                            <div class="block-content">
                                <label>Duración / Horario</label>
                                <span>{{ $event->duracion ?? 'Por definir' }}</span>
                            </div>
                        </div>
                        <div class="detail-block">
                            <i data-lucide="send"></i>
                            <div class="block-content">
                                <label>Publicado el</label>
                                <span>{{ $event->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($event->descripcion)
                        <div class="description-section-premium">
                            <label><i data-lucide="align-left"></i> Descripción del Casting</label>
                            <p>{{ $event->descripcion }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="admin-actions-card animate-slide-up">
                <div class="actions-title">Controles de Moderación</div>
                <div class="actions-flex">
                    @if($event->status === 'open')
                        <form action="{{ route('admin.castings.status', $event->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Cancelar este evento?');">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="canceled">
                            <button type="submit" class="btn-action-premium danger-glow">
                                <i data-lucide="x-circle"></i> Cancelar Evento
                            </button>
                        </form>
                    @elseif(in_array($event->status, ['canceled', 'inactive']))
                        <form action="{{ route('admin.castings.status', $event->id) }}" method="POST" class="m-0">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="open">
                            <button type="submit" class="btn-action-premium success-glow">
                                <i data-lucide="refresh-cw"></i> Reactivar Evento
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.castings.destroy', $event->id) }}" method="POST" class="m-0" onsubmit="return confirm('¿Eliminar definitivamente?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-action-premium ghost-red">
                            <i data-lucide="trash-2"></i> Borrar Registro
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Client & Stats --}}
        <div class="sidebar-column">
            <div class="premium-card mb-4">
                <div class="card-header-premium">
                    <i data-lucide="user"></i>
                    <h3>Organizador</h3>
                </div>
                <div class="client-profile-premium">
                    @if($event->client)
                        <div class="client-avatar-fancy">
                            {{ strtoupper(substr($event->client->nombre ?? 'C', 0, 1)) }}
                        </div>
                        <h4>{{ $event->client->nombre }}</h4>
                        <span class="client-email">{{ $event->client->email }}</span>
                        
                        <div class="client-info-pills">
                            <div class="info-pill-item">
                                <i data-lucide="phone"></i>
                                <span>{{ $event->client->telefono ?? 'S.T.' }}</span>
                            </div>
                            <div class="info-pill-item">
                                <i data-lucide="map-pin"></i>
                                <span>{{ $event->client->ciudad ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="client-empty-state">
                            <i data-lucide="user-x"></i>
                            <p>Sin información del cliente</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="stats-pills-fancy">
                <div class="stat-pill-fancy purple">
                    <div class="stat-icon"><i data-lucide="users"></i></div>
                    <div class="stat-data">
                        <span class="stat-val">{{ $event->applications->count() }}</span>
                        <span class="stat-lab">Postulados</span>
                    </div>
                </div>
                <div class="stat-pill-fancy emerald">
                    <div class="stat-icon"><i data-lucide="check-circle"></i></div>
                    <div class="stat-data">
                        <span class="stat-val">{{ $event->applications->where('status', 'accepted')->count() }}</span>
                        <span class="stat-lab">Aceptados</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- TABLA DE POSTULADOS --}}
    <div class="table-container-premium shadow-sm animate-fade-in">
        <div class="table-header-premium">
            <div class="header-left">
                <i data-lucide="users-2"></i>
                <h3>Músicos Postulados</h3>
            </div>
            <div class="header-right">
                <span class="count-pill">{{ $event->applications->count() }} candidaturas</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="padding-left: 32px;">Músico / Candidato</th>
                        <th>Habilidades / Géneros</th>
                        <th>Oferta Económica</th>
                        <th>Estado</th>
                        <th style="padding-right: 32px; text-align: right;">Acceso</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($event->applications as $app)
                    @php
                        $appStatusMap = [
                            'pending'  => ['class' => 'badge-premium-warning', 'label' => 'Pendiente', 'icon' => 'clock'],
                            'accepted' => ['class' => 'badge-premium-success', 'label' => 'Aceptado',  'icon' => 'check'],
                            'rejected' => ['class' => 'badge-premium-danger',  'label' => 'Rechazado', 'icon' => 'x'],
                        ];
                        $as = $appStatusMap[$app->status] ?? ['class' => 'badge-premium-default', 'label' => $app->status, 'icon' => 'minus'];
                    @endphp
                    <tr>
                        <td style="padding-left: 32px;">
                            <div class="user-cell-fancy">
                                <div class="user-avatar-small">
                                    {{ strtoupper(substr($app->musician->stage_name ?? 'M', 0, 1)) }}
                                </div>
                                <div class="user-info">
                                    <span class="user-name">{{ $app->musician->stage_name ?? 'Sin nombre artístico' }}</span>
                                    <span class="user-sub">{{ $app->musician->user->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="genres-wrap">
                                @forelse($app->musician->genres->take(3) as $genre)
                                    <span class="genre-pill-small">{{ $genre->name }}</span>
                                @empty
                                    <span class="text-dim">No especificados</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="price-callout">
                                <span class="price-val">${{ number_format($app->proposed_price ?? 0, 0) }}</span>
                                <i data-lucide="info" class="msg-icon" title="{{ $app->message ?? 'Sin mensaje' }}"></i>
                            </div>
                        </td>
                        <td>
                            <span class="badge-fancy {{ $as['class'] }}">
                                <i data-lucide="{{ $as['icon'] }}"></i>
                                {{ $as['label'] }}
                            </span>
                        </td>
                        <td style="padding-right: 32px; text-align: right;">
                            <a href="{{ route('admin.musicians.verify', $app->musician->id) }}" class="btn-action-premium ghost" title="Ver perfil">
                                <i data-lucide="external-link"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="premium-empty-state">
                                <div class="empty-glow-icon"><i data-lucide="inbox"></i></div>
                                <h4>Bandeja sin postulaciones</h4>
                                <p>Aún no se han recibido candidaturas para este casting.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @section('head')
    <style>
        .page-header-premium {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;
            background: #ffffff; padding: 24px 32px; border-radius: 20px; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        }
        .back-pill {
            display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; background: #f8fafc;
            border: 1px solid #e2e8f0; border-radius: 30px; font-size: 13px; color: #64748b; text-decoration: none;
            font-weight: 600; margin-bottom: 12px; transition: all 0.2s;
        }
        .back-pill:hover { background: #fff; color: #6366f1; border-color: #6366f1; }
        .title-with-icon { display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
        .title-with-icon h2 { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em; }
        .header-info p { font-size: 15px; color: #64748b; margin: 0; }

        .details-grid-premium { display: grid; grid-template-columns: 1fr 380px; gap: 32px; margin-bottom: 32px; }
        .premium-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
        .card-header-premium { background: #fbfcfe; padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
        .card-header-premium h3 { font-size: 14px; font-weight: 800; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; }
        .card-header-premium i { width: 18px; height: 18px; color: #6366f1; }

        .card-body-premium { padding: 32px; }
        .info-highlight-fancy { margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9; }
        .highlight-item h4 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0; }
        .highlight-item label { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; display: block; }
        
        .highlight-meta { display: flex; gap: 12px; }
        .meta-pill { display: flex; align-items: center; gap: 8px; background: #f8fafc; padding: 6px 14px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .meta-pill i { width: 15px; height: 15px; color: #6366f1; }
        .meta-pill span { font-size: 13.5px; font-weight: 700; color: #1e293b; }

        .details-list-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .detail-block { display: flex; gap: 12px; }
        .detail-block i { width: 44px; height: 44px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #64748b; padding: 12px; }
        .block-content label { display: block; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
        .block-content span { font-size: 14px; font-weight: 700; color: #1e293b; }

        .description-section-premium { background: #fcfdfe; padding: 20px; border-radius: 16px; border: 1px solid #f1f5f9; }
        .description-section-premium label { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 800; color: #6366f1; margin-bottom: 12px; text-transform: uppercase; }
        .description-section-premium p { font-size: 14.5px; color: #475569; line-height: 1.7; margin: 0; }

        .admin-actions-card { background: #1e293b; border-radius: 20px; padding: 24px; color: #fff; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; }
        .actions-title { font-size: 13px; font-weight: 800; text-transform: uppercase; opacity: 0.6; letter-spacing: 0.05em; }
        .actions-flex { display: flex; gap: 12px; }
        
        .btn-action-premium { padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 700; border: none; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s; }
        .btn-action-premium.danger-glow { background: #ef4444; color: #fff; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3); }
        .btn-action-premium.success-glow { background: #10b981; color: #fff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }
        .btn-action-premium.ghost-red { background: transparent; border: 1.5px solid rgba(239,68,68,0.2); color: #fca5a5; }
        .btn-action-premium.ghost-red:hover { background: rgba(239,68,68,0.1); border-color: #ef4444; color: #fff; }

        .client-profile-premium { padding: 32px 24px; text-align: center; border-bottom: 1px solid #f1f5f9; }
        .client-avatar-fancy { width: 72px; height: 72px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 900; margin: 0 auto 16px auto; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2); }
        .client-profile-premium h4 { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0; }
        .client-email { font-size: 13px; color: #94a3b8; font-weight: 500; margin-bottom: 20px; display: block; }
        .client-info-pills { display: flex; flex-direction: column; gap: 10px; text-align: left; }
        .info-pill-item { display: flex; align-items: center; gap: 10px; background: #f8fafc; padding: 10px 16px; border-radius: 12px; border: 1px solid #f1f5f9; }
        .info-pill-item i { width: 14px; height: 14px; color: #94a3b8; }
        .info-pill-item span { font-size: 13px; font-weight: 700; color: #475569; }

        .stats-pills-fancy { display: flex; flex-direction: column; gap: 12px; margin-top: 24px; }
        .stat-pill-fancy { display: flex; align-items: center; gap: 16px; padding: 18px 24px; border-radius: 20px; background: #fff; border: 1px solid #e2e8f0; }
        .stat-icon { width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff; }
        .stat-data { display: flex; flex-direction: column; }
        .stat-val { font-size: 22px; font-weight: 900; color: #1e293b; line-height: 1; }
        .stat-lab { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-top: 4px; }
        
        .stat-pill-fancy.purple .stat-icon { background: linear-gradient(135deg, #a855f7, #7e22ce); }
        .stat-pill-fancy.emerald .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }

        .table-container-premium { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; margin-top: 32px; }
        .table-header-premium { padding: 24px 32px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fbfcfe; }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .header-left i { width: 22px; height: 22px; color: #6366f1; }
        .header-left h3 { margin: 0; font-size: 16px; font-weight: 800; color: #1e293b; }
        .count-pill { background: #f1f5f9; color: #64748b; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }

        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { background: #f8fafc; padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 1px solid #f1f5f9; }
        .premium-table td { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; }
        .premium-table tr:hover { background: #fcfdfe; }

        .user-cell-fancy { display: flex; align-items: center; gap: 12px; }
        .user-avatar-small { width: 38px; height: 38px; border-radius: 10px; background: #6366f1; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; }
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-size: 14px; font-weight: 700; color: #1e293b; }
        .user-sub { font-size: 12px; color: #94a3b8; }

        .genres-wrap { display: flex; flex-wrap: wrap; gap: 6px; }
        .genre-pill-small { background: #f5f3ff; color: #7c3aed; padding: 3px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; border: 1px solid #ede9fe; }

        .price-callout { display: flex; align-items: center; gap: 10px; }
        .price-val { font-size: 15px; font-weight: 800; color: #1e293b; }
        .msg-icon { width: 14px; height: 14px; color: #cbd5e1; cursor: pointer; }

        .badge-fancy { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 30px; font-size: 12px; font-weight: 700; }
        .badge-premium-success { background: #dcfce7; color: #15803d; }
        .badge-premium-blue { background: #e0f2fe; color: #0369a1; }
        .badge-premium-danger { background: #fee2e2; color: #dc2626; }
        .badge-premium-warning { background: #fef3c7; color: #b45309; }
        .badge-premium-default { background: #f1f5f9; color: #475569; }

        .btn-action-premium.ghost { width: 36px; height: 36px; border-radius: 10px; border: 1.5px solid #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; transition: all 0.2s; text-decoration: none; }
        .btn-action-premium.ghost:hover { border-color: #6366f1; color: #6366f1; background: #fcfdfe; }

        .premium-empty-state { padding: 60px 40px; text-align: center; }
        .empty-glow-icon { width: 64px; height: 64px; border-radius: 20px; background: #f8fafc; color: #cbd5e1; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; }
        .empty-glow-icon i { width: 28px; height: 28px; }

        @media (max-width: 1100px) {
            .details-grid-premium { grid-template-columns: 1fr; }
            .sidebar-column { order: -1; }
            .admin-actions-card { flex-direction: column; gap: 16px; text-align: center; }
        }
    </style>
    @endsection

@endsection
