@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <div>
            <h2>Multimedia 📸</h2>
            <p class="dashboard-subtitle">Gestiona tus fotos y videos para mostrar tu talento.</p>
        </div>
        <button class="primary-btn">
            <i data-lucide="upload-cloud"></i>   Subir nuevo
        </button>
    </div>

    {{-- GALERÍA DE FOTOS --}}
    <div class="dashboard-box mb-4">
        <div class="box-header">
            <h3>Galería de Fotos</h3>
        </div>
        
        <div class="grid-gallery">
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?q=80&w=300&h=200&fit=crop" alt="Foto 1">
                <button class="delete-btn"><i data-lucide="trash-2"></i></button>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?q=80&w=300&h=200&fit=crop" alt="Foto 2">
                <button class="delete-btn"><i data-lucide="trash-2"></i></button>
            </div>
            <div class="gallery-item">
                <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=300&h=200&fit=crop" alt="Foto 3">
                <button class="delete-btn"><i data-lucide="trash-2"></i></button>
            </div>
            <div class="gallery-item add-new">
                <i data-lucide="plus"></i>
                <span>Agregar foto</span>
            </div>
        </div>
    </div>

    {{-- VIDEOS --}}
    <div class="dashboard-box">
        <div class="box-header">
            <h3>Videos Destacados</h3>
        </div>
        
        <div class="grid-gallery">
            <div class="gallery-item video">
                <div class="video-thumbnail">
                    <img src="https://images.unsplash.com/photo-1501612780327-45045538702b?q=80&w=300&h=200&fit=crop" alt="Video 1">
                    <div class="play-icon"><i data-lucide="play"></i></div>
                </div>
                <div class="video-info">
                    <h4>Presentación en vivo</h4>
                    <p>Subido el 12 Oct 2023</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .mb-4 { margin-bottom: 24px; }
        .grid-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            padding: 16px 0;
        }
        .gallery-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 3/2;
            background: #f3f4f6;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        
        .delete-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255,255,255,0.9);
            border: none;
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #ef4444;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .gallery-item:hover .delete-btn { opacity: 1; }
        
        .gallery-item.add-new {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #d1d5db;
            background: transparent;
            cursor: pointer;
            color: var(--text-dim);
            gap: 8px;
        }
        .gallery-item.add-new:hover {
            border-color: var(--accent-blue);
            color: var(--accent-blue);
            background: rgba(47, 147, 245, 0.05);
        }
        
        /* Video styles */
        .video-thumbnail { position: relative; height: 140px; }
        .video-thumbnail img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
        .play-icon {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 40px; height: 40px;
            background: rgba(0,0,0,0.6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white;
        }
        .video-info { padding: 12px; }
        .video-info h4 { margin: 0; font-size: 14px; }
        .video-info p { margin: 4px 0 0 0; font-size: 12px; color: var(--text-dim); }
        .gallery-item.video { aspect-ratio: auto; display: flex; flex-direction: column; }
    </style>
@endsection
