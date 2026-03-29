@extends('layouts.app')

@section('content')
<div class="auth-container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8f9ff 0%, #f1f4ff 100%); padding: 20px;">
    
    <div class="auth-box" style="width: 100%; max-width: 550px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.06); padding: 48px; border: 1px solid rgba(255,255,255,0.5);">
        
        <div class="text-center" style="margin-bottom: 40px; text-align: center;">
            <a href="/">
                <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz" style="height: 48px; margin-bottom: 24px;">
            </a>
            <h2 style="font-size: 28px; font-weight: 800; color: #1e1b4b; margin-bottom: 12px; letter-spacing: -0.5px;">Validación de Identidad</h2>
            <p style="color: #6366f1; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Portal para Músicos</p>
            <div style="height: 3px; width: 60px; background: #6366f1; margin: 0 auto; border-radius: 10px;"></div>
        </div>

        @if(session('success'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 16px; border-radius: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; animation: slideIn 0.3s ease-out;">
                <div style="background: #15803d; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                </div>
                <p style="margin: 0; font-size: 14px; font-weight: 600;">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 16px; border-radius: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; animation: slideIn 0.3s ease-out;">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px; color: #ef4444;"></i>
                <p style="margin: 0; font-size: 14px; font-weight: 600;">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fff1f2; border: 1px solid #fda4af; color: #9f1239; padding: 16px; border-radius: 16px; margin-bottom: 24px; animation: slideIn 0.3s ease-out;">
                <ul style="margin: 0; padding-left: 10px; font-size: 14px; list-style: none;">
                    @foreach($errors->all() as $error)
                        <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="color: #ef4444;">•</span> {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$profile || $profile->verification_status === 'unverified')
            {{-- Formulario de Subida --}}
            <div style="margin-bottom: 32px;">
                <p style="color: #475569; font-size: 15px; line-height: 1.6; text-align: center; margin-bottom: 24px;">
                    Para mantener la integridad de Armonihz, requerimos validar tu identidad. Escanea o toma una foto clara de tu identificación oficial.
                </p>
                
                <form action="{{ route('id_verification.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="dropzone" style="border: 2px dashed #cbd5e1; border-radius: 20px; padding: 40px; text-align: center; background: #fff; transition: all 0.3s ease; position: relative; cursor: pointer; overflow: hidden;">
                        <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;">
                        
                        <div id="upload-icon-container" style="width: 64px; height: 64px; background: #eff6ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #3b82f6; transition: all 0.3s;">
                            <i data-lucide="upload-cloud" style="width: 32px; height: 32px;"></i>
                        </div>
                        
                        <h4 style="margin: 0 0 8px; font-weight: 700; color: #1e293b; font-size: 16px;">Sube tu documento</h4>
                        <p style="margin: 0 0 4px; color: #64748b; font-size: 13px;">DNI, Pasaporte o Licencia de conducir</p>
                        <p style="margin: 0; color: #94a3b8; font-size: 11px;">Máximo 5MB • Formatos JPG, PNG o PDF</p>
                        
                        <div id="file-info" style="display: none; margin-top: 16px; padding: 12px; background: #f0f9ff; border-radius: 12px; border: 1px solid #bae6fd;">
                            <div style="display: flex; align-items: center; gap: 10px; text-align: left;">
                                <i data-lucide="file-check" style="color: #0ea5e9; width: 20px;"></i>
                                <span id="file-name" style="color: #0369a1; font-weight: 700; font-size: 13px; word-break: break-all;"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submit-btn" disabled style="width: 100%; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; border: none; padding: 16px; border-radius: 16px; font-weight: 700; font-size: 16px; cursor: not-allowed; opacity: 0.6; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); transition: all 0.3s ease; margin-top: 24px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <span>Enviar para revisión</span>
                        <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                    </button>
                </form>
            </div>

        @elseif($profile->verification_status === 'pending')
            {{-- Estado Pendiente --}}
            <div style="text-align: center; padding: 10px 0;">
                <div class="status-box" style="background: #fdfaf3; border: 1px solid #fef3c7; border-radius: 20px; padding: 32px;">
                    <div style="width: 80px; height: 80px; background: #fffbeb; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; position: relative;">
                        <i data-lucide="clock" style="width: 40px; height: 40px; color: #d97706; animation: spin 4s linear infinite;"></i>
                        <div style="position: absolute; width: 100%; height: 100%; border: 3px solid #f59e0b; border-radius: 50%; border-top-color: transparent; animation: spin 2s linear infinite;"></div>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 800; color: #92400e; margin-bottom: 12px;">Revisión en curso</h3>
                    <p style="color: #78350f; font-size: 15px; margin-bottom: 24px; line-height: 1.5;">Hemos recibido tu documento correctamente. Tu perfil está actualmente en cola de revisión por nuestro equipo administrativo.</p>
                    <div style="padding: 12px 20px; background: #fff; border: 1px solid #fef3c7; border-radius: 12px; font-size: 13px; color: #b45309; display: inline-block; font-weight: 600;">
                        Tiempo estimado: < 12 horas
                    </div>
                </div>
            </div>

        @elseif($profile->verification_status === 'rejected')
            {{-- Estado Rechazado --}}
            <div style="text-align: center;">
                <div style="background: #fff1f2; border: 1px solid #fecaca; border-radius: 20px; padding: 24px; margin-bottom: 24px;">
                    <div style="width: 64px; height: 64px; background: #ffe4e6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i data-lucide="x-circle" style="width: 32px; height: 32px; color: #be123c;"></i>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 800; color: #9f1239; margin-bottom: 8px;">Acceso Denegado</h3>
                    <p style="color: #be123c; font-size: 14px;">Tu documento anterior no ha sido aprobado por el siguiente motivo:</p>
                    
                    <div style="background: white; border-radius: 12px; padding: 16px; margin: 16px 0; border: 1px solid #fecaca; text-align: left;">
                        <p style="margin: 0; color: #1e293b; font-size: 14px; font-weight: 500; font-style: italic;">
                            "{{ $profile->rejection_reason ?? 'No se especificó motivo.' }}"
                        </p>
                    </div>

                    <p style="font-size: 13px; color: #475569; margin: 0;">Por favor, intenta subir un documento más legible o vigente.</p>
                </div>

                <form action="{{ route('id_verification.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="dropzone-retry" style="border: 2px dashed #94a3b8; border-radius: 16px; padding: 24px; text-align: center; position: relative;">
                        <input type="file" name="id_document" id="id_document_retry" accept=".jpg,.jpeg,.png,.pdf" required style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                        <i data-lucide="refresh-cw" style="width: 24px; height: 24px; color: #64748b; margin-bottom: 8px;"></i>
                        <p style="margin: 0; color: #1e293b; font-weight: 700; font-size: 14px;">Subir nuevo archivo</p>
                        <p id="file-name-retry" style="margin-top: 8px; font-size: 12px; color: #4f46e5; font-weight: 700;"></p>
                    </div>
                    <button type="submit" style="width: 100%; background: #1e293b; color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 700; font-size: 15px; cursor: pointer; margin-top: 16px;">
                        Volver a Enviar
                    </button>
                </form>
            </div>
        @endif

        <div style="margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 24px; text-align: center;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background: none; border: none; color: #94a3b8; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: color 0.2s;">
                    <i data-lucide="log-out" style="width: 16px; height: 16px;"></i> Salir de la plataforma
                </button>
            </form>
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
        fileInput.addEventListener('change', function(e) {
            updateFileInfo(this);
        });

        // Hover effect for Dropzone
        fileInput.addEventListener('dragenter', () => { 
            dropzone.style.borderColor = '#6366f1';
            dropzone.style.background = '#f5f7ff';
        });
        fileInput.addEventListener('dragleave', () => { 
            dropzone.style.borderColor = '#cbd5e1';
            dropzone.style.background = '#fff';
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
        retryInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                retryNameDisplay.textContent = this.files[0].name;
            }
        });
    }
</script>

<style>
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .status-box {
        animation: slideIn 0.5s ease-out;
    }
    
    form button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(99, 102, 241, 0.3);
    }
    
    form button:active:not(:disabled) {
        transform: translateY(0);
    }
</style>
@endsection
