@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="back-link" style="color: #64748b; text-decoration: none; font-size: 14px; margin-bottom: 5px; display: inline-flex; align-items: center; gap: 5px;"><i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i> Volver al panel</a>
        <h2>Verificación de Músico</h2>
        <p class="dashboard-subtitle">Revisar documentación de {{ $musician->user->name }}</p>
    </div>
</div>

<div class="grid" style="display: grid; grid-template-columns: 1fr 350px; gap: 24px;">
    {{-- Document Viewer --}}
    <div class="dashboard-box" style="display: flex; flex-direction: column; height: 100%;">
        <div class="box-header">
            <h3>Documento de Identidad</h3>
        </div>
        <div class="document-viewer" style="flex: 1; min-height: 500px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; display: flex; align-items: center; justify-content: center;">
            @if($musician->id_document_path)
                @php
                    $extension = pathinfo($musician->id_document_path, PATHINFO_EXTENSION);
                    $docUrl = route('admin.id_document', basename($musician->id_document_path));
                @endphp
                
                @if(strtolower($extension) === 'pdf')
                    <iframe src="{{ $docUrl }}" width="100%" height="100%" style="border: none; min-height: 500px;"></iframe>
                @else
                    <img src="{{ $docUrl }}" alt="Documento de {{ $musician->stage_name }}" style="max-width: 100%; max-height: 700px; object-fit: contain;">
                @endif
            @else
                <div style="text-align: center; color: #94a3b8;">
                    <i data-lucide="file-warning" style="width: 48px; height: 48px; margin-bottom: 15px;"></i>
                    <p>No se encontró ningún documento subido.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Decision Panel --}}
    <div>
        <div class="dashboard-box mb-24">
            <div class="box-header">
                <h3>Perfil del Usuario</h3>
            </div>
            
            <div style="text-align: center; margin-bottom: 20px;">
                @php $initials = strtoupper(substr($musician->stage_name, 0, 2)); @endphp
                <div class="avatar-circle" style="width: 60px; height: 60px; font-size: 24px; margin: 0 auto 10px;">{{ $initials }}</div>
                <h4 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0;">{{ $musician->stage_name }}</h4>
                <p style="color: #64748b; font-size: 14px; margin: 5px 0 0;">{{ $musician->user->email }}</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                    <span style="color: #64748b; font-size: 14px;">Teléfono</span>
                    <span style="font-weight: 600; color: #1e293b; font-size: 14px;">{{ $musician->phone ?? 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                    <span style="color: #64748b; font-size: 14px;">Ubicación</span>
                    <span style="font-weight: 600; color: #1e293b; font-size: 14px;">{{ $musician->location ?? 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                    <span style="color: #64748b; font-size: 14px;">Estado Actual</span>
                    <span class="status-pill warning" style="font-size: 12px;">{{ ucfirst($musician->verification_status) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b; font-size: 14px;">Fecha Registro</span>
                    <span style="font-weight: 600; color: #1e293b; font-size: 14px;">{{ $musician->created_at->format('d M, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="dashboard-box">
            <div class="box-header">
                <h3>Decisión</h3>
            </div>
            
            <form action="{{ route('admin.musicians.verify.action', $musician->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b; font-size: 14px;">Acción a tomar</label>
                    <div style="display: flex; gap: 10px;">
                        <label style="flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; cursor: pointer; text-align: center; transition: all 0.2s; position: relative;" onclick="toggleRejectReason(false)">
                            <input type="radio" name="action" value="approve" required style="position: absolute; opacity: 0;">
                            <i data-lucide="check-circle" style="color: #16a34a; margin-bottom: 5px;"></i>
                            <div style="font-weight: 600; font-size: 14px; color: #1e293b;">Aprobar</div>
                        </label>

                        <label style="flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; cursor: pointer; text-align: center; transition: all 0.2s; position: relative;" onclick="toggleRejectReason(true)">
                            <input type="radio" name="action" value="reject" required style="position: absolute; opacity: 0;">
                            <i data-lucide="x-circle" style="color: #dc2626; margin-bottom: 5px;"></i>
                            <div style="font-weight: 600; font-size: 14px; color: #1e293b;">Rechazar</div>
                        </label>
                    </div>
                </div>

                <div id="reject-reason-container" style="display: none; margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b; font-size: 14px;">Motivo de rechazo <span style="color: #ef4444;">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; font-size: 14px; resize: none;" placeholder="Ej: La imagen está borrosa, Por favor sube tu documento original..."></textarea>
                    <p style="font-size: 12px; color: #64748b; margin-top: 5px;">Este mensaje será visible para el músico.</p>
                </div>

                <button type="submit" style="width: 100%; background: linear-gradient(135deg, #0f172a, #334155); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i data-lucide="save" style="width: 16px; height: 16px;"></i> Guardar Decisión
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleRejectReason(show) {
        const container = document.getElementById('reject-reason-container');
        const textarea = document.getElementById('rejection_reason');
        
        // Estilo visual de los botones radio
        const labels = document.querySelectorAll('input[name="action"]');
        labels.forEach(input => {
            const label = input.closest('label');
            if (input.checked) {
                label.style.borderColor = input.value === 'approve' ? '#16a34a' : '#dc2626';
                label.style.background = input.value === 'approve' ? '#f0fdf4' : '#fef2f2';
            } else {
                label.style.borderColor = '#e2e8f0';
                label.style.background = 'white';
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
@endsection
