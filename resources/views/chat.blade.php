@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="chat-layout">
        
        {{-- SIDEBAR DE CHAT --}}
        <div class="chat-sidebar dashboard-box">
            <div class="chat-header">
                <h3>Mensajes</h3>
                <button class="icon-btn"><i data-lucide="edit"></i></button>
            </div>
            <div class="chat-search">
                <input type="text" placeholder="Buscar conversación...">
            </div>
            
            <div class="chat-list">
                {{-- Chat Item Active --}}
                <div class="chat-item active">
                    <div class="avatar-circle">JP</div>
                    <div class="chat-info">
                        <div class="chat-top">
                            <strong>Juan Pérez</strong>
                            <span class="time">10:30 am</span>
                        </div>
                        <p class="preview">Hola, ¿tienen disponibilidad para el...</p>
                    </div>
                </div>
                
                {{-- Chat Item --}}
                <div class="chat-item">
                    <div class="avatar-circle blue">EH</div>
                    <div class="chat-info">
                        <div class="chat-top">
                            <strong>Empresa Hotelera</strong>
                            <span class="time">Ayer</span>
                        </div>
                        <p class="preview">Confirmado, nos vemos el viernes.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- AREA DE MENSAJES --}}
        <div class="chat-main dashboard-box">
            <div class="chat-main-header">
                <div class="user-details">
                    <div class="avatar-circle">JP</div>
                    <div>
                        <strong>Juan Pérez</strong>
                        <span class="status">En línea</span>
                    </div>
                </div>
                <div class="actions">
                    <button class="icon-btn"><i data-lucide="phone"></i></button>
                    <button class="icon-btn"><i data-lucide="more-vertical"></i></button>
                </div>
            </div>

            <div class="messages-area">
                <div class="msg received">
                    <p>Hola, buenas tardes. Me interesa su grupo para una boda.</p>
                    <span class="msg-time">10:28 am</span>
                </div>
                <div class="msg sent">
                    <p>¡Hola Juan! Claro que sí, con gusto. ¿Para qué fecha sería?</p>
                    <span class="msg-time">10:29 am</span>
                </div>
                <div class="msg received">
                    <p>Sería para el 15 de Octubre. ¿Tienen disponibilidad?</p>
                    <span class="msg-time">10:30 am</span>
                </div>
            </div>

            <div class="chat-input-area">
                <button class="icon-btn"><i data-lucide="paperclip"></i></button>
                <input type="text" placeholder="Escribe un mensaje...">
                <button class="send-btn"><i data-lucide="send"></i></button>
            </div>
        </div>

    </div>

    <style>
        .chat-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 24px;
            height: calc(100vh - 100px); /* Ajuste altura */
        }
        
        /* Chat Sidebar */
        .chat-sidebar { display: flex; flex-direction: column; padding: 0; overflow: hidden; }
        .chat-header { padding: 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-light); }
        .chat-header h3 { margin: 0; font-size: 18px; }
        .chat-search { padding: 12px; border-bottom: 1px solid var(--border-light); }
        .chat-list { flex: 1; overflow-y: auto; }
        
        .chat-item { display: flex; padding: 16px; gap: 12px; cursor: pointer; border-bottom: 1px solid var(--border-light); transition: background 0.2s; }
        .chat-item:hover { background: #f9fafb; }
        .chat-item.active { background: rgba(47, 147, 245, 0.05); border-left: 3px solid var(--accent-blue); }
        
        .chat-info { flex: 1; }
        .chat-top { display: flex; justify-content: space-between; margin-bottom: 4px; }
        .chat-top strong { font-size: 14px; }
        .chat-top .time { font-size: 11px; color: var(--text-dim); }
        .preview { font-size: 12px; color: var(--text-dim); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
        
        /* Chat Main */
        .chat-main { display: flex; flex-direction: column; padding: 0; overflow: hidden; }
        .chat-main-header { padding: 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-light); background: #fff; }
        .user-details { display: flex; align-items: center; gap: 12px; }
        .status { font-size: 12px; color: var(--accent-green); }
        
        .messages-area { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 16px; }
        
        .msg { max-width: 70%; padding: 12px 16px; border-radius: 12px; position: relative; font-size: 14px; line-height: 1.5; }
        .msg.received { align-self: flex-start; background: white; border: 1px solid var(--border-light); border-bottom-left-radius: 4px; }
        .msg.sent { align-self: flex-end; background: var(--accent-blue); color: white; border-bottom-right-radius: 4px; }
        
        .msg-time { display: block; font-size: 10px; margin-top: 4px; opacity: 0.7; text-align: right; }
        
        .chat-input-area { padding: 16px; border-top: 1px solid var(--border-light); display: flex; gap: 12px; align-items: center; background: white; }
        .chat-input-area input { flex: 1; margin: 0; }
        
        .send-btn { background: var(--accent-blue); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .icon-btn { background: none; border: none; cursor: pointer; color: var(--text-dim); }
        .icon-btn:hover { color: var(--accent-blue); }
        
        @media (max-width: 768px) {
            .chat-layout { grid-template-columns: 1fr; }
            .chat-sidebar { display: none; } /* En mobile mostrar lista primero, luego chat logicamente seria toggle */
        }
    </style>
@endsection
