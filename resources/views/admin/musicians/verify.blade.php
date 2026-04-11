@extends('layouts.admin')

@section('admin-content')
    <div class="page-header-premium">
        <div class="header-info">
            <a href="{{ route('admin.musicians.index') }}" class="back-pill">
                <i data-lucide="arrow-left"></i>
                <span>Regresar a la lista</span>
            </a>
            <div class="title-with-icon">
                <h2>Verificación de Identidad</h2>
                <i data-lucide="shield-check" style="color: #6366f1; width: 26px; height: 26px; opacity: 0.2;"></i>
            </div>
            <p>Revisión detallada de documentación para <strong>{{ $musician->stage_name }}</strong></p>
        </div>
        <div class="header-status">
            @php
                $vstatus = $musician->verification_status ?? 'unverified';
                $badgeMap = [
                    'approved'   => ['class' => 'badge-premium-success',   'label' => 'Validado', 'icon' => 'check-circle'],
                    'pending'    => ['class' => 'badge-premium-warning',   'label' => 'En Revisión', 'icon' => 'clock'],
                    'rejected'   => ['class' => 'badge-premium-danger',    'label' => 'Rechazado', 'icon' => 'x-circle'],
                    'unverified' => ['class' => 'badge-premium-default', 'label' => 'Sin Iniciar', 'icon' => 'file-warning'],
                ];
                $badge = $badgeMap[$vstatus] ?? $badgeMap['unverified'];
            @endphp
            <span class="badge-fancy {{ $badge['class'] }}">
                <i data-lucide="{{ $badge['icon'] }}"></i>
                {{ $badge['label'] }}
            </span>
        </div>
    </div>

    <div class="verification-grid">
        {{-- Document Viewer --}}
        <div class="viewer-column">
            <div class="premium-card">
                <div class="card-header-premium">
                    <i data-lucide="file-text"></i>
                    <h3>Documento de Identificación</h3>
                </div>
                <div class="document-container-premium">
                    @if($musician->id_document_path)
                        @php
                            $extension = pathinfo($musician->id_document_path, PATHINFO_EXTENSION);
                            $docUrl = route('admin.musicians.document', $musician->id);
                        @endphp

                        @if(strtolower($extension) === 'pdf')
                            <div class="pdf-frame">
                                <iframe src="{{ $docUrl }}" width="100%" height="100%"></iframe>
                            </div>
                        @else
                            <div class="image-viewer">
                                <img src="{{ $docUrl }}" alt="Documento de {{ $musician->stage_name }}" class="zoomable-img">
                                <div class="viewer-actions">
                                    <a href="{{ $docUrl }}" target="_blank" class="zoom-btn"><i data-lucide="maximize-2"></i> Abrir en tamaño real</a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="empty-doc-state">
                            <div class="empty-icon-circle">
                                <i data-lucide="file-x"></i>
                            </div>
                            <h4>Sin documento adjunto</h4>
                            <p>El músico aún no ha subido su identificación oficial.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Decision Panel --}}
        <div class="sidebar-column">
            <div class="premium-card mb-4 shadow-sm">
                <div class="card-header-premium">
                    <i data-lucide="user"></i>
                    <h3>Perfil del Músico</h3>
                </div>
                <div class="user-profile-summary">
                    @php $initials = strtoupper(substr($musician->stage_name, 0, 2)); @endphp
                    <div class="user-avatar-premium">{{ $initials }}</div>
                    <h4>{{ $musician->stage_name }}</h4>
                    <span class="user-email">{{ $musician->user->email }}</span>
                </div>

                <div class="details-list-premium">
                    <div class="detail-item">
                        <span class="label">WhatsApp / Tel</span>
                        <span class="value">{{ $musician->phone ?? 'Sin asignar' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Ubicación</span>
                        <span class="value">{{ $musician->location ?? 'No especificada' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Fecha Registro</span>
                        <span class="value">{{ $musician->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="premium-card shadow-lg border-primary">
                <div class="card-header-premium">
                    <i data-lucide="gantt-chart"></i>
                    <h3>Centro de Decisión</h3>
                </div>

                <div class="decision-content-premium">
                    @if($musician->verification_status === 'approved')
                        <div class="resolution-view success">
                            <div class="res-icon"><i data-lucide="check-circle-2"></i></div>
                            <h4>Usuario Verificado</h4>
                            <p>Esta cuenta ha sido aprobada. El músico puede operar libremente en la plataforma.</p>
                        </div>
                    @elseif($musician->verification_status === 'rejected')
                        <div class="resolution-view danger">
                            <div class="res-icon"><i data-lucide="x-circle"></i></div>
                            <h4>Perfil Rechazado</h4>
                            <p>Se ha solicitado una nueva carga de documentos al usuario.</p>
                            <div class="rejection-box-premium">
                                <strong>Motivo enviado:</strong>
                                <p>{{ $musician->rejection_reason ?? 'Sin motivo específico' }}</p>
                            </div>
                        </div>
                    @else
                        @if ($errors->any())
                            <div class="error-alert-premium">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li><i data-lucide="alert-triangle"></i> {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.musicians.verify.action', $musician->id) }}" method="POST" id="decision-form">
                            @csrf
                            <div class="action-selector-premium">
                                <p class="selector-title">Veredicto Final:</p>
                                <div class="radio-cards-premium">
                                    <label class="radio-card-premium approve" onclick="toggleRejectReason(false)">
                                        <input type="radio" name="action" value="approve">
                                        <div class="radio-content">
                                            <i data-lucide="shield-check"></i>
                                            <strong>Aprobar</strong>
                                        </div>
                                    </label>

                                    <label class="radio-card-premium reject" onclick="toggleRejectReason(true)">
                                        <input type="radio" name="action" value="reject">
                                        <div class="radio-content">
                                            <i data-lucide="shield-x"></i>
                                            <strong>Rechazar</strong>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="reject-reason-container" class="reason-container animate-slide-down">
                                <label for="rejection_reason">Motivo del rechazo <span class="req">*</span></label>
                                <textarea name="rejection_reason" id="rejection_reason" rows="4" placeholder="Ej: La foto del INE está borrosa..."></textarea>
                                <p class="helper-text">Este mensaje se enviará por correo al músico.</p>
                            </div>

                            <button type="submit" class="btn-save-premium">
                                <span>Guardar Decisión</span>
                                <i data-lucide="send"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRejectReason(show) {
            const container = document.getElementById('reject-reason-container');
            const textarea = document.getElementById('rejection_reason');
            const labels = document.querySelectorAll('.radio-card-premium');
            
            labels.forEach(label => {
                const input = label.querySelector('input');
                if (input.checked) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            });

            if (show) {
                container.style.display = 'block';
                textarea.setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                textarea.removeAttribute('required');
                textarea.value = '';
            }
        }
    </script>

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
        .header-info p strong { color: #1e293b; }

        .verification-grid { display: grid; grid-template-columns: 1fr 380px; gap: 32px; align-items: start; }
        .premium-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; }
        .card-header-premium { background: #fbfcfe; padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
        .card-header-premium i { width: 18px; height: 18px; color: #6366f1; }
        .card-header-premium h3 { font-size: 15px; font-weight: 800; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; }

        /* DOCUMENT VIEWER */
        .document-container-premium { padding: 24px; background: #f4f7fa; min-height: 600px; display: flex; flex-direction: column; }
        .pdf-frame { flex: 1; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 8px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .image-viewer { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px; position: relative; }
        .zoomable-img { max-width: 100%; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border: 4px solid #fff; object-fit: contain; }
        .viewer-actions { position: absolute; bottom: 20px; right: 20px; }
        .zoom-btn { background: rgba(15, 23, 42, 0.8); color: #fff; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 8px; backdrop-filter: blur(4px); transition: all 0.2s; border: 1px solid rgba(255,255,255,0.1); }
        .zoom-btn:hover { background: rgba(99, 102, 241, 1); transform: scale(1.05); }

        .empty-doc-state { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: #94a3b8; }
        .empty-icon-circle { width: 80px; height: 80px; border-radius: 50%; background: #e2e8f0; color: #94a3b8; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .empty-icon-circle i { width: 32px; height: 32px; }
        .empty-doc-state h4 { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 8px 0; }
        .empty-doc-state p { font-size: 14.5px; margin: 0; }

        /* SIDEBAR */
        .user-profile-summary { padding: 32px 24px; text-align: center; border-bottom: 1px solid #f1f5f9; }
        .user-avatar-premium { width: 70px; height: 70px; border-radius: 18px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; font-size: 26px; font-weight: 900; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); }
        .user-profile-summary h4 { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 4px 0; }
        .user-email { font-size: 14px; color: #64748b; font-weight: 500; }

        .details-list-premium { padding: 24px; display: flex; flex-direction: column; gap: 16px; }
        .detail-item { display: flex; justify-content: space-between; align-items: center; }
        .detail-item .label { font-size: 13.5px; color: #64748b; font-weight: 600; }
        .detail-item .value { font-size: 14px; color: #1e293b; font-weight: 700; }

        .resolution-view { text-align: center; padding: 24px; border-radius: 16px; }
        .resolution-view.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .resolution-view.danger { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .res-icon { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .res-icon i { width: 30px; height: 30px; }
        .resolution-view h4 { font-size: 17px; font-weight: 800; margin: 0 0 8px 0; }
        .resolution-view p { font-size: 14px; margin: 0; line-height: 1.6; opacity: 0.9; }

        .rejection-box-premium { margin-top: 20px; text-align: left; background: #fff; padding: 16px; border-radius: 12px; border: 1px solid #fca5a5; }
        .rejection-box-premium strong { display: block; font-size: 12px; text-transform: uppercase; color: #ef4444; margin-bottom: 6px; letter-spacing: 0.05em; }
        .rejection-box-premium p { color: #b91c1c; font-weight: 600; }

        /* DECISION FORM */
        .decision-content-premium { padding: 24px; }
        .action-selector-premium { margin-bottom: 24px; }
        .selector-title { font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 12px; }
        .radio-cards-premium { display: flex; gap: 12px; }
        .radio-card-premium { flex: 1; cursor: pointer; position: relative; }
        .radio-card-premium input { position: absolute; opacity: 0; }
        .radio-content { padding: 16px 12px; border-radius: 14px; border: 2px solid #f1f5f9; background: #f8fafc; text-align: center; transition: all 0.2s; }
        .radio-content i { width: 22px; height: 22px; margin-bottom: 8px; }
        .radio-content strong { display: block; font-size: 13.5px; font-weight: 800; }

        .radio-card-premium.approve .radio-content i { color: #94a3b8; }
        .radio-card-premium.approve.selected .radio-content { border-color: #10b981; background: #f0fdf4; }
        .radio-card-premium.approve.selected .radio-content i { color: #10b981; }
        .radio-card-premium.approve.selected .radio-content strong { color: #15803d; }

        .radio-card-premium.reject .radio-content i { color: #94a3b8; }
        .radio-card-premium.reject.selected .radio-content { border-color: #ef4444; background: #fef2f2; }
        .radio-card-premium.reject.selected .radio-content i { color: #ef4444; }
        .radio-card-premium.reject.selected .radio-content strong { color: #b91c1c; }

        .reason-container { margin-top: 24px; display: none; }
        .reason-container label { display: block; font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
        .reason-container label .req { color: #ef4444; }
        .reason-container textarea { width: 100%; border: 20px; border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px; font-size: 14px; outline: none; transition: all 0.2s; background: #fbfcfe; }
        .reason-container textarea:focus { border-color: #6366f1; background: #fff; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
        .helper-text { font-size: 12px; color: #94a3b8; margin-top: 6px; font-weight: 500; }

        .btn-save-premium { width: 100%; margin-top: 24px; padding: 14px; border-radius: 14px; border: none; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; font-size: 15px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: all 0.2s; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); }
        .btn-save-premium:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(99, 102, 241, 0.4); }

        .error-alert-premium { background: #fff1f2; border: 1px solid #fecaca; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; }
        .error-alert-premium ul { margin: 0; padding: 0; list-style: none; }
        .error-alert-premium li { color: #b91c1c; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .error-alert-premium li i { width: 14px; height: 14px; }

        .badge-fancy { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 30px; font-size: 13px; font-weight: 700; }
        .badge-premium-success { background: #dcfce7; color: #15803d; }
        .badge-premium-warning { background: #fef3c7; color: #b45309; }
        .badge-premium-danger { background: #fee2e2; color: #dc2626; }
        .badge-premium-default { background: #f1f5f9; color: #475569; }

        @media (max-width: 1100px) {
            .verification-grid { grid-template-columns: 1fr; }
            .sidebar-column { order: -1; }
        }
    </style>
    @endsection
@endsection