@extends('layouts.app')

@section('content')
    <div style="min-height: 100vh; display: flex; flex-direction: row; background: #fff; overflow-x: hidden;">

        {{-- COLUMNA IZQUIERDA: Branding e Info (Solo Desktop) --}}
        <div class="info-sidebar"
            style="flex: 1; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); padding: 60px; color: white; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden;">
            {{-- Adorno visual de fondo --}}
            <div
                style="position: absolute; top: -10%; right: -10%; width: 400px; height: 400px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; blur: 80px;">
            </div>
            <div
                style="position: absolute; bottom: -5%; left: -5%; width: 300px; height: 300px; background: rgba(79, 70, 229, 0.15); border-radius: 50%; blur: 60px;">
            </div>

            <div style="position: relative; z-index: 10; max-width: 500px;">
                <a href="/">
                    <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz"
                        style="height: 60px; margin-bottom: 48px; filter: brightness(0) invert(1);">
                </a>

                <h1 style="font-size: 42px; font-weight: 800; line-height: 1.1; margin-bottom: 24px; letter-spacing: -1px;">
                    Valida tu identidad y comienza a tocar.</h1>
                <p style="font-size: 18px; line-height: 1.6; color: #c7d2fe; margin-bottom: 40px;">Como comunidad premium de
                    músicos, la seguridad es nuestra prioridad. La verificación nos ayuda a conectar talento real con
                    clientes excepcionales.</p>

                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div
                            style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                            <i data-lucide="shield-check" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 4px; font-weight: 700;">Seguridad Garantizada</h4>
                            <p style="margin: 0; font-size: 14px; color: #a5b4fc;">Tus documentos están encriptados y solo
                                son accesibles por el equipo administrativo.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div
                            style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                            <i data-lucide="zap" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 4px; font-weight: 700;">Revisión Veloz</h4>
                            <p style="margin: 0; font-size: 14px; color: #a5b4fc;">Validamos tu perfil en menos de 12 horas
                                para que no pierdas oportunidades.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: Formulario/Estatus --}}
        <div class="action-panel"
            style="flex: 1.2; background: #fff; padding: 80px 20px; display: flex; align-items: center; justify-content: center;">

            <div style="width: 100%; max-width: 580px; animation: fadeIn 0.6s ease-out;">

                <div class="mobile-logo" style="display: none; text-align: center; margin-bottom: 32px;">
                    <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz" style="height: 42px;">
                </div>

                <div style="margin-bottom: 40px;">
                    <h2
                        style="font-size: 32px; font-weight: 800; color: #1e1b4b; margin-bottom: 8px; letter-spacing: -0.5px;">
                        Panel de Validación</h2>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="height: 4px; width: 40px; background: #6366f1; border-radius: 10px;"></div>
                        <span
                            style="font-size: 13px; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: 1px;">Documentación
                            requerida</span>
                    </div>
                </div>

                @if(session('success'))
                    <div
                        style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 20px; border-radius: 16px; margin-bottom: 32px; display: flex; align-items: center; gap: 12px; animation: slideIn 0.3s ease-out;">
                        <i data-lucide="check-circle-2" style="width: 24px; height: 24px; color: #16a34a;"></i>
                        <p style="margin: 0; font-size: 15px; font-weight: 600;">{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div
                        style="background: #fff1f2; border: 1px solid #fda4af; color: #9f1239; padding: 20px; border-radius: 16px; margin-bottom: 32px;">
                        <ul style="margin: 0; padding: 0; font-size: 14px; list-style: none;">
                            @foreach($errors->all() as $error)
                                <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                    <i data-lucide="alert-circle" style="width: 16px; color: #e11d48;"></i> {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!$profile || $profile->verification_status === 'unverified')
                    {{-- Formulario de Subida --}}
                    <div>
                        <p style="color: #64748b; font-size: 16px; line-height: 1.6; margin-bottom: 32px;">
                            Sube una foto legible de tu **identificación oficial** (DNI, Pasaporte o Licencia). Asegúrate de que
                            los datos sean visibles.
                        </p>

                        <form action="{{ route('id_verification.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div id="dropzone"
                                style="border: 2px dashed #e2e8f0; border-radius: 24px; padding: 60px 40px; text-align: center; background: #fbfcfe; transition: all 0.3s ease; position: relative; cursor: pointer;">
                                <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;">

                                <div id="upload-icon-container"
                                    style="width: 80px; height: 80px; background: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: #6366f1; transition: all 0.3s;">
                                    <i data-lucide="arrow-up-to-line" style="width: 36px; height: 36px;"></i>
                                </div>

                                <h4 style="margin: 0 0 8px; font-weight: 800; color: #1e293b; font-size: 18px;">Selecciona tu
                                    archivo</h4>
                                <p style="margin: 0; color: #94a3b8; font-size: 13px;">Arrastra aquí o haz clic para explorar
                                </p>

                                <div id="file-info"
                                    style="display: none; margin-top: 24px; padding: 16px; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                    <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
                                        <div style="background: #e0f2fe; color: #0ea5e9; padding: 8px; border-radius: 10px;">
                                            <i data-lucide="file-text" style="width: 20px;"></i>
                                        </div>
                                        <span id="file-name"
                                            style="color: #1e293b; font-weight: 700; font-size: 14px; word-break: break-all;"></span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="submit-btn" disabled
                                style="width: 100%; background: #1e1b4b; color: white; border: none; padding: 18px; border-radius: 18px; font-weight: 700; font-size: 16px; cursor: not-allowed; opacity: 0.5; transition: all 0.3s ease; margin-top: 32px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <span>Iniciar proceso de validación</span>
                                <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                            </button>
                        </form>
                    </div>

                @elseif($profile->verification_status === 'pending')
                    {{-- Estado Pendiente --}}
                    <div
                        style="background: #fdfaf3; border: 1px solid #fef3c7; border-radius: 28px; padding: 48px 32px; text-align: center;">
                        <div
                            style="width: 100px; height: 100px; background: #fffbeb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 32px; position: relative;">
                            <i data-lucide="hourglass"
                                style="width: 48px; height: 48px; color: #d97706; animation: pulse 2s ease-in-out infinite;"></i>
                            <div
                                style="position: absolute; width: 100%; height: 100%; border: 4px solid #f59e0b; border-radius: 50%; border-top-color: transparent; animation: spin 3s linear infinite;">
                            </div>
                        </div>
                        <h3 style="font-size: 24px; font-weight: 800; color: #92400e; margin-bottom: 12px;">Revisión en Progreso
                        </h3>
                        <p style="color: #78350f; font-size: 16px; line-height: 1.6; margin-bottom: 0;">Tu documento está en
                            manos de nuestro equipo administrativo. Te notificaremos vía correo electrónico en cuanto tu perfil
                            sea aprobado.</p>
                        <div
                            style="margin-top: 32px; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; color: #b45309; background: #fff; padding: 8px 16px; border-radius: 10px; border: 1px solid #fef3c7;">
                            <i data-lucide="check" style="width: 16px;"></i> Documento recibido
                        </div>
                    </div>

                @elseif($profile->verification_status === 'rejected')
                    {{-- Estado Rechazado --}}
                    <div
                        style="background: #fff1f2; border: 1px solid #fecaca; border-radius: 28px; padding: 40px 32px; margin-bottom: 32px; text-align: center;">
                        <div
                            style="width: 80px; height: 80px; background: #ffe4e6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                            <i data-lucide="alert-triangle" style="width: 40px; height: 40px; color: #be123c;"></i>
                        </div>
                        <h3 style="font-size: 24px; font-weight: 800; color: #9f1239; margin-bottom: 8px;">Validación Rechazada
                        </h3>

                        <div
                            style="background: white; border-radius: 16px; padding: 20px; margin: 24px 0; border: 1px solid #fecaca; text-align: left;">
                            <span
                                style="display: block; font-size: 12px; font-weight: 800; color: #be123c; text-transform: uppercase; margin-bottom: 6px;">Comentario
                                de la administración:</span>
                            <p style="margin: 0; color: #1e293b; font-size: 15px; font-weight: 600; line-height: 1.5;">
                                "{{ $profile->rejection_reason ?? 'No se especificó motivo detallado.' }}"
                            </p>
                        </div>

                        <p style="font-size: 14px; color: #475569;">Por favor, sube una nueva copia del documento corrigiendo lo
                            indicado arriba.</p>
                    </div>

                    {{-- Reintentar --}}
                    <form action="{{ route('id_verification.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div
                            style="border: 2px dashed #94a3b8; border-radius: 20px; padding: 32px; text-align: center; position: relative; background: #f8fafc;">
                            <input type="file" name="id_document" id="id_document_retry" accept=".jpg,.jpeg,.png,.pdf" required
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 5;">
                            <i data-lucide="file-up"
                                style="width: 28px; height: 28px; color: #64748b; margin-bottom: 12px;"></i>
                            <h5 style="margin: 0; color: #1e293b; font-weight: 700; font-size: 15px;">Intentar de nuevo</h5>
                            <p id="file-name-retry" style="margin-top: 8px; font-size: 13px; color: #6366f1; font-weight: 700;">
                            </p>
                        </div>
                        <button type="submit"
                            style="width: 100%; background: #1e1b4b; color: white; border: none; padding: 18px; border-radius: 18px; font-weight: 700; font-size: 16px; cursor: pointer; margin-top: 24px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                            Actualizar Documentación
                        </button>
                    </form>
                @endif

                <div style="margin-top: 48px; border-top: 1px solid #f1f5f9; padding-top: 32px; text-align: center;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            style="background: none; border: none; color: #94a3b8; font-size: 14px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: color 0.2s;">
                            <i data-lucide="power" style="width: 16px;"></i> Salir de Armonihz
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        lucide.createIcons();

        // Logic for Main Upload
        const fileInput = document.getElementById('id_document');
        const dropzone = document.getElementById('dropzone');
        const fileNameDisplay = document.getElementById('file-name');
        const fileInfo = document.getElementById('file-info');
        const iconContainer = document.getElementById('upload-icon-container');
        const submitBtn = document.getElementById('submit-btn');

        if (fileInput) {
            fileInput.addEventListener('change', function (e) {
                updateFileInfo(this);
            });

            fileInput.addEventListener('dragenter', () => {
                dropzone.style.borderColor = '#6366f1';
                dropzone.style.background = '#f5f7ff';
            });
            fileInput.addEventListener('dragleave', () => {
                dropzone.style.borderColor = '#e2e8f0';
                dropzone.style.background = '#fbfcfe';
            });
        }

        function updateFileInfo(input) {
            if (input.files.length > 0) {
                fileNameDisplay.textContent = input.files[0].name;
                fileInfo.style.display = 'block';
                dropzone.style.borderColor = '#6366f1';
                iconContainer.style.background = '#6366f1';
                iconContainer.style.color = '#fff';
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                submitBtn.disabled = false;
            }
        }

        // Logic for Retry Upload
        const retryInput = document.getElementById('id_document_retry');
        const retryNameDisplay = document.getElementById('file-name-retry');
        if (retryInput) {
            retryInput.addEventListener('change', function (e) {
                if (this.files.length > 0) {
                    retryNameDisplay.textContent = this.files[0].name;
                }
            });
        }
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.1);
            }
        }

        button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
        }

        button:active:not(:disabled) {
            transform: translateY(0);
        }

        @media (max-width: 992px) {
            .info-sidebar {
                display: none !important;
            }

            .action-panel {
                padding: 40px 20px !important;
            }

            .mobile-logo {
                display: block !important;
            }
        }
    </style>
@endsection