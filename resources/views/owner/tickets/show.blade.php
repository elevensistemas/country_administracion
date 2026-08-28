@extends('layouts.app')

@section('title', 'Reclamo #' . $ticket->id)
@section('page_title', 'Seguimiento de Ticket #' . $ticket->id)

@section('content')
<div class="row">
    <!-- Chat conversation (Left) -->
    <div class="col-lg-8 mb-4">
        <!-- Ticket details header -->
        <div class="ios-card bg-body-secondary border-0 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios" style="font-size: 0.75rem;">
                        {{ $ticket->category->display_name }}
                    </span>
                    <h4 class="fw-bold m-0 mt-2">{{ $ticket->title }}</h4>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Lote {{ $ticket->lot->number }}</small>
                    <small class="text-muted d-block">{{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                </div>
            </div>
            <p class="m-0 mt-3">{{ $ticket->description }}</p>
        </div>

        <!-- Chat Stream -->
        <div class="ios-card mb-4" style="max-height: 400px; overflow-y: auto;">
            <h6 class="fw-bold mb-4 border-bottom border-ios pb-2">Conversación con la Administración</h6>

            <div class="d-flex flex-column gap-3">
                @forelse($ticket->messages as $msg)
                    @php
                        $isAdmin = in_array($msg->user->relationship_type, ['admin', 'superadmin', 'operator', 'accounting']);
                    @endphp
                    
                    <div class="d-flex {{ !$isAdmin ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="p-3 rounded-4 {{ !$isAdmin ? 'bg-success text-white' : 'bg-body-secondary text-body' }}" style="max-width: 75%; font-size: 0.95rem;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong style="font-size: 0.8rem;">{{ $isAdmin ? 'Administración' : $msg->user->full_name }}</strong>
                                <small style="font-size: 0.7rem; opacity: 0.8;">{{ $msg->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="m-0" style="line-height: 1.4;">{{ $msg->message }}</p>

                            @if($msg->attachment)
                                <div class="mt-2 pt-2 border-top border-light-subtle">
                                    <a href="{{ asset('storage/' . $msg->attachment->file_path) }}" target="_blank" class="fw-bold text-decoration-none d-inline-flex align-items-center text-white" style="font-size: 0.8rem;">
                                        <i class="bi bi-paperclip me-1"></i> {{ $msg->attachment->file_name }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        No hay mensajes de respuesta en este ticket.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Reply form -->
        @if($ticket->status !== 'closed' && $ticket->status !== 'resolved')
            <div class="ios-card">
                <h6 class="fw-bold mb-3">Escribir Mensaje</h6>
                
                <form method="POST" action="{{ route('owner.tickets.message', $ticket) }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <textarea name="message" id="message" rows="3" class="form-control form-control-ios" required placeholder="Escribe tu consulta o aclaración..."></textarea>
                    </div>

                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <input type="file" name="attachment" id="attachment" class="form-control form-control-ios form-control-sm">
                        </div>
                        <div class="col-md-4 d-grid">
                            <button type="submit" class="btn btn-ios btn-ios-primary">Enviar Mensaje</button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="ios-card bg-body-secondary border-0 text-center">
                <i class="bi bi-lock-fill text-muted fs-2 d-block mb-2"></i>
                <span class="text-muted">Este ticket de reclamo se encuentra cerrado y resuelto. No es posible enviar más respuestas.</span>
            </div>
        @endif
    </div>

    <!-- Ticket status sidebar (Right) -->
    <div class="col-lg-4">
        <div class="ios-card">
            <h6 class="fw-bold mb-4"><i class="bi bi-info-circle-fill text-success me-2"></i>Estado del Ticket</h6>
            
            <div class="mb-3">
                <span class="text-muted d-block" style="font-size: 0.8rem;">N° Ticket</span>
                <span class="fw-bold fs-5">#{{ $ticket->id }}</span>
            </div>

            <div class="mb-3">
                <span class="text-muted d-block" style="font-size: 0.8rem;">Estado Actual</span>
                @if($ticket->status === 'open')
                    <span class="badge bg-danger text-white badge-ios">Nuevo / Abierto</span>
                @elseif($ticket->status === 'in_progress')
                    <span class="badge bg-warning text-dark badge-ios">En Proceso</span>
                @elseif($ticket->status === 'resolved')
                    <span class="badge bg-success text-white badge-ios">Resuelto</span>
                @else
                    <span class="badge bg-secondary text-white badge-ios">Cerrado</span>
                @endif
            </div>

            <div class="mb-4">
                <span class="text-muted d-block" style="font-size: 0.8rem;">Operador Asignado</span>
                <span class="fw-semibold">{{ $ticket->assignee ? $ticket->assignee->full_name : 'Buscando operador disponible...' }}</span>
            </div>

            <a href="{{ route('owner.tickets.index') }}" class="btn btn-ios btn-ios-secondary w-100">Volver a mis reclamos</a>
        </div>
    </div>
</div>
@endsection
