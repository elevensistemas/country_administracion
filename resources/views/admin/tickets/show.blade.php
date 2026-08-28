@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)
@section('page_title', 'Detalle de Ticket #' . $ticket->id)

@section('content')
<div class="row">
    <!-- Chat conversation (Left) -->
    <div class="col-lg-8 mb-4">
        <!-- Ticket Origin Details -->
        <div class="ios-card bg-body-secondary border-0 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios me-2" style="font-size: 0.7rem;">
                        {{ $ticket->category->display_name }}
                    </span>
                    <h4 class="fw-bold m-0 mt-2">{{ $ticket->title }}</h4>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Iniciado por: {{ $ticket->user->full_name }}</small>
                    <small class="text-muted d-block">Lote {{ $ticket->lot->number }} • {{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
            <p class="m-0 mt-3 fs-6">{{ $ticket->description }}</p>

            @if($ticket->attachments->where('ticket_message_id', null)->count() > 0)
                <div class="mt-3 pt-3 border-top border-ios">
                    <small class="text-muted d-block mb-1">Archivos adjuntos iniciales:</small>
                    @foreach($ticket->attachments->where('ticket_message_id', null) as $att)
                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="d-inline-flex align-items-center text-success text-decoration-none me-3 mb-1" style="font-size: 0.85rem;">
                            <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> {{ $att->file_name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Chat Stream -->
        <div class="ios-card mb-4" style="max-height: 500px; overflow-y: auto;">
            <h6 class="fw-bold mb-4 border-bottom border-ios pb-2">Conversación del Reclamo</h6>

            <div class="d-flex flex-column gap-3">
                @forelse($ticket->messages as $msg)
                    @php
                        $isAdmin = in_array($msg->user->relationship_type, ['admin', 'superadmin', 'operator', 'accounting']);
                    @endphp
                    
                    <div class="d-flex {{ $isAdmin ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="p-3 rounded-4 {{ $isAdmin ? 'bg-success text-white' : 'bg-body-secondary text-body' }}" style="max-width: 75%; font-size: 0.95rem;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong style="font-size: 0.8rem;">{{ $msg->user->full_name }}</strong>
                                <small style="font-size: 0.7rem; opacity: 0.8;">{{ $msg->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="m-0" style="line-height: 1.4;">{{ $msg->message }}</p>

                            <!-- Attachment if exists -->
                            @if($msg->attachment)
                                <div class="mt-2 pt-2 border-top border-light-subtle">
                                    <a href="{{ asset('storage/' . $msg->attachment->file_path) }}" target="_blank" class="text-white fw-bold text-decoration-none d-inline-flex align-items-center" style="font-size: 0.8rem;">
                                        <i class="bi bi-paperclip me-1"></i> {{ $msg->attachment->file_name }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        No hay mensajes de respuesta aún.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Reply Form -->
        <div class="ios-card">
            <h6 class="fw-bold mb-3">Enviar Respuesta al Propietario</h6>
            
            <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <textarea name="message" id="message" rows="3" class="form-control form-control-ios" required placeholder="Escribe tu mensaje aquí..."></textarea>
                </div>

                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <input type="file" name="attachment" id="attachment" class="form-control form-control-ios form-control-sm">
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" class="btn btn-ios btn-ios-primary"><i class="bi bi-send-fill me-1"></i> Enviar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Administrative sidebar (Right) -->
    <div class="col-lg-4">
        <!-- Ticket Controls -->
        <div class="ios-card">
            <h6 class="fw-bold mb-4"><i class="bi bi-gear-fill text-success me-2"></i>Controles del Ticket</h6>
            
            <form method="POST" action="{{ route('admin.tickets.update', $ticket) }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold" style="font-size: 0.8rem;">Estado del Ticket</label>
                    <select name="status" id="status" class="form-select form-control-ios" required>
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Abierto / Nuevo</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>En Proceso</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resuelto</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Cerrado</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="assignee_id" class="form-label fw-semibold" style="font-size: 0.8rem;">Operador Responsable</label>
                    <select name="assignee_id" id="assignee_id" class="form-select form-control-ios">
                        <option value="">Sin Asignar</option>
                        @foreach($operators as $op)
                            <option value="{{ $op->id }}" {{ $ticket->assignee_id == $op->id ? 'selected' : '' }}>
                                {{ $op->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-ios btn-ios-primary w-100 mb-2">Guardar Cambios</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-ios btn-ios-secondary w-100">Volver a la Lista</a>
            </form>
        </div>

        <!-- Internal Notes (Private) -->
        <div class="ios-card border-warning">
            <h6 class="fw-bold mb-3 text-warning"><i class="bi bi-lock-fill me-2"></i>Notas Internas (Privado)</h6>
            <p class="text-muted" style="font-size: 0.8rem;">Estas notas solo son visibles para administradores y operadores del consorcio. El propietario no las recibirá.</p>

            <div class="list-group list-group-flush mb-3" style="max-height: 200px; overflow-y: auto;">
                @forelse($ticket->internalNotes as $note)
                    <div class="list-group-item bg-transparent border-0 px-0 py-2 border-bottom border-ios" style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-warning">{{ $note->user->name }}</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $note->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="text-muted">{{ $note->note }}</span>
                    </div>
                @empty
                    <span class="text-muted d-block text-center py-2" style="font-size: 0.85rem;">No hay notas internas</span>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.tickets.internal-note', $ticket) }}">
                @csrf
                <div class="mb-3">
                    <textarea name="note" id="note" rows="2" class="form-control form-control-ios" required placeholder="Escribe un comentario interno..."></textarea>
                </div>
                <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-warning w-100">Guardar Nota Privada</button>
            </form>
        </div>
    </div>
</div>
@endsection
