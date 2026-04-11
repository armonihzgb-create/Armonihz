@extends('layouts.dashboard')

@section('dashboard-content')

    {{-- PAGE HEADER --}}
    <div class="mm-header">
        <div>
            <div class="mm-eyebrow">
                <i data-lucide="image" style="width:14px;height:14px;color:#6c3fc5;"></i>
                MULTIMEDIA
            </div>
            <h1 class="mm-title">Tu Portafolio</h1>
            <p class="mm-subtitle">Gestiona las fotos y videos que ven los clientes en tu perfil.</p>
        </div>
        <input type="file" id="media-upload" style="display:none;" accept="image/*,video/mp4,video/quicktime" onchange="uploadMedia(this)">
    </div>

    @php
        $photosCount = $media->where('type', 'photo')->count();
        $videosCount = $media->where('type', 'video')->count();
        $featuredCount = $media->where('type', 'video')->where('is_featured', true)->count();
    @endphp

    {{-- STATS ROW --}}
    <div class="mm-stats">
        <div class="mm-stat">
            <div class="mm-stat-icon" style="background:#ede9fe;">
                <i data-lucide="image" style="width:18px;height:18px;color:#6c3fc5;"></i>
            </div>
            <div>
                <div class="mm-stat-value">{{ $photosCount }}</div>
                <div class="mm-stat-label">Fotos (máx. 20)</div>
            </div>
        </div>
        <div class="mm-stat">
            <div class="mm-stat-icon" style="background:#eff6ff;">
                <i data-lucide="video" style="width:18px;height:18px;color:#2563eb;"></i>
            </div>
            <div>
                <div class="mm-stat-value" style="color:#2563eb;">{{ $videosCount }}</div>
                <div class="mm-stat-label">Videos (máx. 5)</div>
            </div>
        </div>
        <div class="mm-stat">
            <div class="mm-stat-icon" style="background:#f0fdf4;">
                <i data-lucide="eye" style="width:18px;height:18px;color:#16a34a;"></i>
            </div>
            <div>
                <div class="mm-stat-value" style="color:#16a34a;">{{ $profile->profile_views ?? 0 }}</div>
                <div class="mm-stat-label">Vistas del portafolio</div>
            </div>
        </div>
        <div class="mm-stat">
            <div class="mm-stat-icon" style="background:#fefce8;">
                <i data-lucide="star" style="width:18px;height:18px;color:#ca8a04;"></i>
            </div>
            <div>
                <div class="mm-stat-value" style="color:#ca8a04;">{{ $featuredCount }}</div>
                <div class="mm-stat-label">Video Destacado</div>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    <div id="media-alert" style="display:none; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 600;"></div>

    {{-- PHOTO GALLERY --}}
    <div class="mm-section-card">
        <div class="mm-section-header">
            <div>
                <h2 class="mm-section-title">Galería de Fotos</h2>
                <p class="mm-section-subtitle">Tus mejores momentos. Los clientes las verán en tu perfil público.</p>
            </div>
            <button class="mm-add-small-btn" onclick="document.getElementById('media-upload').click()">
                <i data-lucide="plus" style="width:14px;height:14px;"></i> Agregar foto
            </button>
        </div>

        <div class="mm-photo-grid">
            @foreach($media->where('type', 'photo') as $photo)
            <div class="mm-photo-item" id="media-item-{{ $photo->id }}">
                <img src="{{ $photo->url() }}" alt="Foto">
                <div class="mm-photo-overlay">
                    <button class="mm-overlay-btn" title="Ver" onclick="openViewModal('{{ $photo->url() }}', 'photo')">
                        <i data-lucide="expand" style="width:14px;height:14px;"></i>
                    </button>
                    <button class="mm-overlay-btn mm-overlay-delete" title="Eliminar" onclick="showDeleteModal({{ $photo->id }})">
                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                    </button>
                </div>
            </div>
            @endforeach

            {{-- Add new photo --}}
            <div class="mm-photo-add" onclick="document.getElementById('media-upload').click()">
                <i data-lucide="plus-circle" style="width:28px;height:28px;color:#6c3fc5;"></i>
                <span>Agregar foto</span>
            </div>
        </div>
    </div>

    {{-- VIDEO GALLERY --}}
    <div class="mm-section-card">
        <div class="mm-section-header">
            <div>
                <h2 class="mm-section-title">Videos</h2>
                <p class="mm-section-subtitle">Muestra tu talento con actuaciones en vivo.</p>
            </div>
            <button class="mm-add-small-btn" onclick="document.getElementById('media-upload').click()">
                <i data-lucide="plus" style="width:14px;height:14px;"></i> Agregar video
            </button>
        </div>

        <div class="mm-video-grid">
            @foreach($media->where('type', 'video') as $video)
            <div class="mm-video-card" id="media-item-{{ $video->id }}">
                <div class="mm-video-thumb" onclick="openViewModal('{{ $video->url() }}', 'video')" style="cursor:pointer; background:#000;">
                    <video src="{{ $video->url() }}" style="width:100%; height:100%; object-fit:cover; opacity: 0.8;" preload="metadata"></video>
                    <div class="mm-play-btn">
                        <i data-lucide="play" style="width:20px;height:20px;color:#fff;margin-left:2px;"></i>
                    </div>
                </div>
                <div class="mm-video-info">
                    <div class="mm-video-info-top">
                        <h4 class="mm-video-title">{{ $video->title ?? 'Presentación' }}</h4>
                        <button class="mm-video-delete" title="Eliminar" onclick="showDeleteModal({{ $video->id }})">
                            <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                        </button>
                    </div>
                    <p class="mm-video-date">
                        <i data-lucide="calendar" style="width:12px;height:12px;"></i>
                        Subido el {{ $video->created_at->format('d M Y') }}
                    </p>
                    <span class="mm-video-badge {{ $video->is_featured ? 'featured' : '' }}" onclick="featureVideo({{ $video->id }})" style="cursor:pointer; transition: all .2s;" id="badge-video-{{ $video->id }}">
                        <i data-lucide="star" style="width:11px;height:11px;"></i> 
                        {{ $video->is_featured ? 'Destacado' : 'Marcar destacado' }}
                    </span>
                </div>
            </div>
            @endforeach

            {{-- Add new video --}}
            <div class="mm-video-add" onclick="document.getElementById('media-upload').click()">
                <div class="mm-video-add-icon">
                    <i data-lucide="video" style="width:28px;height:28px;color:#6c3fc5;"></i>
                </div>
                <span class="mm-video-add-label">Agregar video</span>
                <span class="mm-video-add-hint">MP4, MOV · Máx. 100 MB</span>
            </div>
        </div>
    </div>

    {{-- TIPS CARD --}}
    <div class="mm-tips-card">
        <i data-lucide="lightbulb" style="width:18px;height:18px;color:#ca8a04;flex-shrink:0;"></i>
        <div>
            <span class="mm-tips-title">Consejos para un mejor portafolio</span>
            <div class="mm-tips-list">
                <span>📸 Usa fotos de alta calidad en ambientes bien iluminados</span>
                <span>🎬 Los videos de 1–3 minutos generan más interés</span>
                <span>⭐ Marca tu mejor video como "Destacado" para que aparezca en primera línea</span>
            </div>
        </div>
    </div>


    {{-- DELETE MEDIA MODAL --}}
    <div id="delete-media-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.45); backdrop-filter:blur(4px); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:20px; padding:32px 28px; max-width:380px; width:90%; box-shadow:0 24px 60px rgba(0,0,0,.2); text-align:center;">
            <div style="width:56px;height:56px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i data-lucide="trash-2" style="width:24px;height:24px;color:#dc2626;"></i>
            </div>
            <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 8px;">¿Eliminar archivo?</h3>
            <p style="font-size:14px;color:#64748b;margin:0 0 24px;line-height:1.6;">Esta acción no se puede deshacer. El archivo desaparecerá de tu perfil público.</p>
            <div style="display:flex;gap:10px;">
                <button onclick="hideDeleteModal()" class="mm-modal-cancel">
                    Cancelar
                </button>
                <button id="confirm-delete-btn" class="mm-modal-confirm">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>

    {{-- VIEW MEDIA MODAL --}}
    <div id="view-media-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,23,42,.95); backdrop-filter:blur(8px); align-items:center; justify-content:center; flex-direction:column; padding:20px;">
        <button onclick="hideViewModal()" class="mm-modal-close">
            <i data-lucide="x" style="width:24px;height:24px;"></i>
        </button>
        <div id="view-media-container" style="max-width:90vw; max-height:85vh; border-radius:12px; overflow:hidden; box-shadow:0 24px 80px rgba(0,0,0,.6); position: relative;">
            <!-- content injected via js -->
        </div>
    </div>

    {{-- SCRIPTS DE FUNCIONALIDAD AJAX --}}
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function showAlert(msg, isError = false) {
            const el = document.getElementById('media-alert');
            el.innerText = msg;
            el.style.display = 'block';
            el.style.backgroundColor = isError ? '#fef2f2' : '#f0fdf4';
            el.style.color = isError ? '#dc2626' : '#166534';
            el.style.border = isError ? '1px solid #fecaca' : '1px solid #bbf7d0';
            setTimeout(() => { el.style.display = 'none'; }, 5000);
        }

        function uploadMedia(input) {
            if (!input.files || input.files.length === 0) return;
            
            const file = input.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', csrfToken);
            
            // Si es video, pedir titulo
            if (file.type.startsWith('video/')) {
                const title = prompt("Ingresa un título corto para el video (opcional):", "Presentación en vivo");
                if (title) formData.append('title', title);
            }

            showAlert("Subiendo archivo, por favor espera...", false);
            
            fetch("{{ route('multimedia.upload') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                return res.text().then(text => ({ status: res.status, text: text }));
            })
            .then(obj => {
                let data = {};
                try {
                    data = JSON.parse(obj.text);
                } catch(e) {
                    data = { message: "Error del servidor (HTTP " + obj.status + ")" };
                    console.error("HTML Error Response:", obj.text);
                }

                if (obj.status >= 400) {
                    showAlert(data.error || data.message || "Error al subir chivo", true);
                } else {
                    window.location.reload(); // Reload to show new item
                }
            })
            .catch(err => {
                console.error("Grave error de red:", err);
                showAlert("Error crítico (" + err.name + "): " + err.message, true);
            })
            .finally(() => {
                input.value = ""; // Reset file input
            });
        }

        let mediaToDeleteId = null;

        function showDeleteModal(id) {
            mediaToDeleteId = id;
            document.getElementById('delete-media-modal').style.display = 'flex';
        }

        function hideDeleteModal() {
            mediaToDeleteId = null;
            document.getElementById('delete-media-modal').style.display = 'none';
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            if(!mediaToDeleteId) return;
            const id = mediaToDeleteId;
            hideDeleteModal();
            
            fetch(`/multimedia/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => {
                if(res.ok) {
                    const el = document.getElementById(`media-item-${id}`);
                    if(el) el.remove();
                    showAlert("Eliminado correctamente.", false);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showAlert("Error al eliminar", true);
                }
            });
        });

        function openViewModal(url, type) {
            const container = document.getElementById('view-media-container');
            container.innerHTML = ''; 
            
            if(type === 'photo') {
                const img = document.createElement('img');
                img.src = url;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '85vh';
                img.style.display = 'block';
                img.style.objectFit = 'contain';
                container.appendChild(img);
            } else {
                const vid = document.createElement('video');
                vid.src = url;
                vid.controls = true;
                vid.autoplay = true;
                vid.style.maxWidth = '100%';
                vid.style.maxHeight = '85vh';
                vid.style.display = 'block';
                vid.style.backgroundColor = '#000';
                vid.style.outline = 'none';
                container.appendChild(vid);
            }
            
            document.getElementById('view-media-modal').style.display = 'flex';
            // Re-init lucide icons just in case
            if (window.lucide) { lucide.createIcons(); }
        }

        function hideViewModal() {
            const container = document.getElementById('view-media-container');
            container.innerHTML = ''; // This stops the video from playing
            document.getElementById('view-media-modal').style.display = 'none';
        }

        function featureVideo(id) {
            fetch(`/multimedia/${id}/feature`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => {
                if(res.ok) {
                    window.location.reload(); // Reload to update badges
                } else {
                    res.json().then(data => showAlert(data.error || "Error al destacar", true));
                }
            });
        }
    </script>

    <style>
        /* ── Header ─────────────────────────────── */
        .mm-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 20px; margin-bottom: 24px; padding-bottom: 22px;
            border-bottom: 1px solid #f1f5f9;
        }
        .mm-eyebrow {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; font-weight: 700; letter-spacing: .08em;
            color: #6c3fc5; text-transform: uppercase; margin-bottom: 6px;
        }
        .mm-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .mm-subtitle { font-size: 14px; color: #64748b; margin: 0; }
        .mm-upload-btn {
            display: inline-flex !important; align-items: center !important; gap: 7px !important;
            padding: 10px 20px !important; border-radius: 8px !important;
            background: linear-gradient(135deg, #6c3fc5, #2f93f5) !important;
            color: #fff !important; font-size: 13px !important; font-weight: 700 !important; border: none !important;
            box-shadow: 0 4px 16px rgba(108,63,197,.25) !important;
            cursor: pointer !important; transition: opacity .2s !important; white-space: nowrap !important;
        }
        .mm-upload-btn:hover { opacity: .9 !important; }

        /* Force SVGs inside our buttons to display correctly */
        .mm-section-card button svg, .mm-header button svg, .mm-photo-overlay button svg, #delete-media-modal button svg, #view-media-modal button svg, .mm-modal-close svg {
            display: inline-block !important;
            flex-shrink: 0 !important;
            visibility: visible !important;
        }

        /* Modal Button Overrides */
        .mm-modal-cancel {
            flex:1 !important; padding:11px !important; border-radius:9px !important; border:1.5px solid #e2e8f0 !important;
            background:#f8fafc !important; color:#475569 !important; font-size:14px !important; font-weight:600 !important;
            cursor:pointer !important; transition:all .2s !important; text-align: center !important; margin: 0 !important;
        }
        .mm-modal-cancel:hover { background: #f1f5f9 !important; }
        .mm-modal-confirm {
            flex:1 !important; padding:11px !important; border-radius:9px !important; border:none !important; margin: 0 !important;
            background:linear-gradient(135deg,#dc2626,#ef4444) !important; color:#fff !important; font-size:14px !important; font-weight:700 !important;
            cursor:pointer !important; box-shadow:0 4px 14px rgba(220,38,38,.3) !important; transition:opacity .2s !important; text-align: center !important;
        }
        .mm-modal-confirm:hover { opacity: 0.9 !important; }
        .mm-modal-close {
            position:absolute !important; top:24px !important; right:24px !important; background:rgba(255,255,255,.1) !important;
            border:none !important; width:48px !important; height:48px !important; border-radius:50% !important; color:#fff !important;
            display:flex !important; align-items:center !important; justify-content:center !important; cursor:pointer !important;
            transition:background .2s !important; z-index: 10000 !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important;
        }
        .mm-modal-close:hover { background: rgba(255,255,255,.2) !important; transform: scale(1.1) !important; }

        /* ── Stats ─────────────────────────────── */
        .mm-stats {
            display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 22px;
        }
        .mm-stat {
            background: #fff; border: 1.5px solid #e8edf3; border-radius: 14px;
            padding: 16px 18px; display: flex; align-items: center; gap: 12px;
        }
        .mm-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .mm-stat-value { font-size: 20px; font-weight: 900; color: #0f172a; line-height: 1; margin-bottom: 2px; }
        .mm-stat-label { font-size: 12px; color: #64748b; }

        /* ── Section Card ───────────────────────── */
        .mm-section-card {
            background: #fff; border: 1.5px solid #e8edf3;
            border-radius: 16px; padding: 24px; margin-bottom: 20px;
        }
        .mm-section-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 16px; margin-bottom: 20px; padding-bottom: 16px;
            border-bottom: 1px solid #f8fafc;
        }
        .mm-section-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 3px; }
        .mm-section-subtitle { font-size: 13px; color: #94a3b8; margin: 0; }
        .mm-add-small-btn {
            display: inline-flex !important; align-items: center !important; gap: 6px !important;
            padding: 8px 14px !important; border-radius: 8px !important; border: 1.5px solid #e2e8f0 !important;
            background: #f8fafc !important; color: #475569 !important; font-size: 13px !important; font-weight: 600 !important;
            cursor: pointer !important; white-space: nowrap !important; transition: all .2s !important;
        }
        .mm-add-small-btn:hover { border-color: #6c3fc5 !important; color: #6c3fc5 !important; background: #f5f3ff !important; }

        /* ── Photo Grid ─────────────────────────── */
        .mm-photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 14px;
        }
        .mm-photo-item {
            position: relative; border-radius: 12px; overflow: hidden;
            aspect-ratio: 3/2; background: #f1f5f9;
            border: 1.5px solid #f1f5f9;
        }
        .mm-photo-item img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; display: block; }
        .mm-photo-item:hover img { transform: scale(1.05); }
        .mm-photo-overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,.45);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            opacity: 0; transition: opacity .25s;
        }
        .mm-photo-item:hover .mm-photo-overlay { opacity: 1; }
        .mm-overlay-btn {
            width: 36px !important; height: 36px !important; border-radius: 50% !important;
            background: rgba(255,255,255,.9) !important; border: none !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            cursor: pointer !important; color: #0f172a !important; transition: transform .15s !important;
        }
        .mm-overlay-btn:hover { transform: scale(1.1) !important; }
        .mm-overlay-delete { color: #ef4444 !important; }
        
        .mm-photo-add {
            aspect-ratio: 3/2; border-radius: 12px;
            border: 2px dashed #d1d5db; background: transparent;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 8px; cursor: pointer; color: #94a3b8; font-size: 13px; font-weight: 500;
            transition: all .2s;
        }
        .mm-photo-add:hover { border-color: #6c3fc5; color: #6c3fc5; background: rgba(108,63,197,.03); }

        /* ── Video Grid ─────────────────────────── */
        .mm-video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }
        .mm-video-card {
            background: #f8fafc; border: 1.5px solid #e8edf3;
            border-radius: 14px; overflow: hidden; transition: box-shadow .2s;
        }
        .mm-video-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.07); }
        .mm-video-thumb {
            position: relative; height: 150px; overflow: hidden;
        }
        .mm-video-thumb img, .mm-video-thumb video { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s; }
        .mm-video-card:hover .mm-video-thumb video { transform: scale(1.04); }
        .mm-play-btn {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 44px; height: 44px; border-radius: 50%;
            background: rgba(108,63,197,.85);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,.3);
            transition: transform .2s;
            pointer-events: none;
        }
        .mm-video-card:hover .mm-play-btn { transform: translate(-50%, -50%) scale(1.1); }
        
        .mm-video-info { padding: 14px 16px; }
        .mm-video-info-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px; }
        .mm-video-title { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; }
        .mm-video-delete {
            width: 28px !important; height: 28px !important; border-radius: 6px !important; border: none !important;
            background: transparent !important; color: #94a3b8 !important; cursor: pointer !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            transition: all .2s !important; flex-shrink: 0 !important;
        }
        .mm-video-delete:hover { background: #fef2f2 !important; color: #ef4444 !important; }
        .mm-video-date {
            font-size: 12px; color: #94a3b8; margin: 0 0 8px;
            display: flex; align-items: center; gap: 5px;
        }
        .mm-video-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600; padding: 3px 9px;
            border-radius: 999px; background: #f8fafc; color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .mm-video-badge:hover { background: #fefce8; color: #ca8a04; border-color: #fef08a;}
        .mm-video-badge.featured {
            background: #fefce8; color: #ca8a04; border-color: #fef08a;
        }

        .mm-video-add {
            height: 100%; min-height: 220px;
            border-radius: 14px; border: 2px dashed #d1d5db;
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; gap: 8px; cursor: pointer;
            background: transparent; transition: all .2s;
        }
        .mm-video-add:hover { border-color: #6c3fc5; background: rgba(108,63,197,.03); }
        .mm-video-add-icon {
            width: 56px; height: 56px; border-radius: 14px;
            background: rgba(108,63,197,.08);
            display: flex; align-items: center; justify-content: center;
        }
        .mm-video-add-label { font-size: 14px; font-weight: 600; color: #475569; }
        .mm-video-add-hint { font-size: 12px; color: #94a3b8; }

        /* ── Tips ───────────────────────────────── */
        .mm-tips-card {
            display: flex; align-items: flex-start; gap: 12px;
            background: #fefce8; border: 1.5px solid #fef08a;
            border-radius: 12px; padding: 16px 20px;
        }
        .mm-tips-title { display: block; font-size: 13px; font-weight: 700; color: #a16207; margin-bottom: 8px; }
        .mm-tips-list { display: flex; flex-direction: column; gap: 5px; font-size: 13px; color: #713f12; }

        @media (max-width: 900px) { .mm-stats { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 640px) {
            .mm-header { flex-direction: column; }
            .mm-stats { grid-template-columns: 1fr 1fr; }
            .mm-photo-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>

@endsection
