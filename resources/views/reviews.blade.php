@extends('layouts.dashboard')

@section('dashboard-content')

    <div class="page-header">
        <div>
            <h2>Reseñas y calificaciones</h2>
            <p class="dashboard-subtitle">Opiniones de clientes que ya trabajaron contigo</p>
        </div>
        
        {{-- Filtro de orden --}}
        <div class="filter-tabs">
            <button class="filter-tab active">Más recientes</button>
            <button class="filter-tab">Mejores primero</button>
        </div>
    </div>

    {{-- GRID DE RESEÑAS --}}
    <div class="reviews-grid">

        {{-- COLUMNA IZQUIERDA: RESUMEN --}}
        <div class="dashboard-box summary-card">
            <h3>Calificación General</h3>
            
            <div class="rating-big-container">
                <span class="rating-big">4.5</span>
                <div class="stars-static">
                    <i data-lucide="star" class="filled"></i>
                    <i data-lucide="star" class="filled"></i>
                    <i data-lucide="star" class="filled"></i>
                    <i data-lucide="star" class="filled"></i>
                    <i data-lucide="star-half" class="filled"></i>
                </div>
                <span class="rating-total">Basado en 32 reseñas</span>
            </div>

            {{-- Barras de desglose (5 estrellas, 4 estrellas...) --}}
            <div class="rating-breakdown">
                <div class="rating-row">
                    <span>5 <i data-lucide="star" class="tiny"></i></span>
                    <div class="bar-bg"><div class="bar-fill" style="width: 80%"></div></div>
                    <span class="count">24</span>
                </div>
                <div class="rating-row">
                    <span>4 <i data-lucide="star" class="tiny"></i></span>
                    <div class="bar-bg"><div class="bar-fill" style="width: 15%"></div></div>
                    <span class="count">6</span>
                </div>
                <div class="rating-row">
                    <span>3 <i data-lucide="star" class="tiny"></i></span>
                    <div class="bar-bg"><div class="bar-fill" style="width: 5%"></div></div>
                    <span class="count">2</span>
                </div>
                <div class="rating-row">
                    <span>2 <i data-lucide="star" class="tiny"></i></span>
                    <div class="bar-bg"></div>
                    <span class="count">0</span>
                </div>
                <div class="rating-row">
                    <span>1 <i data-lucide="star" class="tiny"></i></span>
                    <div class="bar-bg"></div>
                    <span class="count">0</span>
                </div>
            </div>

            <div class="info-box blue-box mt-large">
                <i data-lucide="trending-up"></i>
                <p>¡Buen trabajo! Estás en el <strong>top 10%</strong> de músicos en tu zona.</p>
            </div>
        </div>

        {{-- COLUMNA DERECHA: LISTA DE COMENTARIOS --}}
        <div class="reviews-list-container">
            
            {{-- Reseña 1 --}}
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="avatar-circle">CP</div>
                        <div>
                            <h4>Carlos Pérez</h4>
                            <span class="review-date">10 Feb 2026</span>
                        </div>
                    </div>
                    <div class="review-rating">
                        <i data-lucide="star" class="filled"></i>
                        <i data-lucide="star" class="filled"></i>
                        <i data-lucide="star" class="filled"></i>
                        <i data-lucide="star" class="filled"></i>
                        <i data-lucide="star" class="filled"></i>
                    </div>
                </div>

                <div class="review-body">
                    <p>"Excelente servicio y puntualidad. El grupo llegó antes para prepararse y tocaron todas las canciones que pedimos. Muy profesionales, sin duda los volveré a contratar para el próximo cumpleaños."</p>
                </div>

                <div class="review-footer">
                    <span class="event-tag">Evento privado</span>
                    <button class="text-btn small"><i data-lucide="corner-up-left"></i> Responder</button>
                </div>
            </div>

            {{-- Reseña 2 --}}
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="avatar-circle blue">AL</div>
                        <div>
                            <h4>Ana López</h4>
                            <span class="review-date">28 Ene 2026</span>
                        </div>
                    </div>
                    <div class="review-rating">
                        <i data-lucide="star" class="filled"></i>
                        <i data-lucide="star" class="filled"></i>
                        <i data-lucide="star" class="filled"></i>
                        <i data-lucide="star" class="filled"></i>
                        <i data-lucide="star" class="empty"></i>
                    </div>
                </div>

                <div class="review-body">
                    <p>"Muy buena música y ambiente. Lo único que mejoraría es que tardaron un poco en responder los mensajes al principio, pero el día del evento todo salió perfecto."</p>
                </div>

                <div class="review-footer">
                    <span class="event-tag">Boda</span>
                    <div class="response-box">
                        <strong>Tu respuesta:</strong>
                        <p>¡Gracias Ana! Tomaremos en cuenta tu comentario para mejorar nuestra atención.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection