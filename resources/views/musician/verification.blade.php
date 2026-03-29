@extends('layouts.app')

@section('content')
<div class="auth-container" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f8fafc; padding: 20px;">
    <div class="auth-box" style="width: 100%; max-width: 500px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 40px;">
        
        <div class="text-center" style="margin-bottom: 30px; text-align: center;">
            <img src="{{ asset('images/Armonihz_logo.png') }}" alt="Armonihz" style="height: 50px; margin-bottom: 20px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 10px;">Verificación de Identidad</h2>
            <p style="color: #64748b; font-size: 14px; line-height: 1.5;">Para garantizar la seguridad de nuestra comunidad, necesitamos verificar tu identidad antes de que puedas acceder al portal de músicos.</p>
        </div>

        @if(session('success'))
            <div style="background: #ecfdf5; border: 1px solid #10b981; color: #047857; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;">
                <i data-lucide="check-circle" style="min-width: 20px;"></i>
                <p style="margin: 0; font-size: 14px;">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fef2f2; border: 1px solid #ef4444; color: #b91c1c; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;">
                <i data-lucide="alert-circle" style="min-width: 20px;"></i>
                <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #ef4444; color: #b91c1c; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$profile || $profile->verification_status === 'unverified')
            {{-- Formulario de Subida --}}
            <form action="{{ route('verification.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b; font-size: 14px;">Identificación Oficial</label>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">Sube una copia clara de tu DNI, Pasaporte o Licencia de conducir. Máximo 5MB (JPG, PNG o PDF).</p>
                    
                    <div style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px; text-align: center; background: #f8fafc; transition: all 0.3s ease; position: relative;">
                        <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                        <i data-lucide="upload-cloud" style="width: 40px; height: 40px; color: #94a3b8; margin-bottom: 10px;"></i>
                        <p style="margin: 0; color: #475569; font-weight: 500;">Haz clic o arrastra tu archivo aquí</p>
                        <p id="file-name" style="margin-top: 10px; font-size: 13px; color: #6c3fc5; font-weight: 600;"></p>
                    </div>
                </div>

                <button type="submit" style="width: 100%; background: linear-gradient(135deg, #6c3fc5, #a855f7); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; box-shadow: 0 4px 14px rgba(108, 63, 197, 0.3); transition: all 0.3s ease;">
                    Enviar Documento
                </button>
            </form>

        @elseif($profile->verification_status === 'pending')
            {{-- Estado Pendiente --}}
            <div style="text-align: center; padding: 20px 0;">
                <div style="width: 80px; height: 80px; background: #e0e7ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i data-lucide="clock" style="width: 40px; height: 40px; color: #4f46e5;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">Evaluación en progreso</h3>
                <p style="color: #64748b; font-size: 15px; margin-bottom: 25px;">Hemos recibido tu documento. Nuestro equipo lo está revisando y pronto tendrás acceso total a la plataforma.</p>
                <div style="padding: 15px; background: #f1f5f9; border-radius: 10px; font-size: 14px; color: #475569;">
                    Este proceso normalmente toma menos de 24 horas.
                </div>
            </div>

        @elseif($profile->verification_status === 'rejected')
            {{-- Estado Rechazado --}}
            <div style="text-align: center; padding: 10px 0 20px;">
                <div style="width: 60px; height: 60px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="x-circle" style="width: 30px; height: 30px; color: #dc2626;"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">Verificación Rechazada</h3>
                <p style="color: #64748b; font-size: 15px; margin-bottom: 15px;">Lamentablemente, no pudimos verificar tu identidad.</p>
                
                @if($profile->rejection_reason)
                    <div style="background: #fff0f2; border-left: 4px solid #ef4444; padding: 15px; text-align: left; border-radius: 0 8px 8px 0; margin-bottom: 25px;">
                        <span style="display: block; font-size: 12px; font-weight: 700; color: #b91c1c; text-transform: uppercase; margin-bottom: 5px;">Motivo:</span>
                        <p style="margin: 0; color: #7f1d1d; font-size: 14px;">{{ $profile->rejection_reason }}</p>
                    </div>
                @endif

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 20px;">
                
                <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 15px; text-align: left;">Sube un nuevo documento</h4>
                <form action="{{ route('verification.upload') }}" method="POST" enctype="multipart/form-data" style="text-align: left;">
                    @csrf
                    <div style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; text-align: center; background: #f8fafc; position: relative; margin-bottom: 15px;">
                        <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                        <i data-lucide="upload-cloud" style="width: 30px; height: 30px; color: #94a3b8; margin-bottom: 5px;"></i>
                        <p style="margin: 0; color: #475569; font-weight: 500; font-size: 14px;">Arrastra un archivo nuevo</p>
                        <p id="file-name" style="margin-top: 5px; font-size: 13px; color: #6c3fc5; font-weight: 600;"></p>
                    </div>
                    <button type="submit" style="width: 100%; background: #1e293b; color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; font-size: 15px; cursor: pointer;">
                        Volver a Enviar
                    </button>
                </form>
            </div>
        @endif

        <div style="margin-top: 25px; text-align: center;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background: none; border: none; color: #64748b; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                    <i data-lucide="log-out" style="width: 16px; height: 16px;"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
    lucide.createIcons();
    
    // Script para mostrar el nombre del archivo seleccionado
    const fileInput = document.getElementById('id_document');
    const fileNameDisplay = document.getElementById('file-name');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                fileNameDisplay.textContent = e.target.files[0].name;
                document.querySelector('[data-lucide="upload-cloud"]').style.color = '#6c3fc5';
            } else {
                fileNameDisplay.textContent = '';
                document.querySelector('[data-lucide="upload-cloud"]').style.color = '#94a3b8';
            }
        });
    }
</script>
@endsection
