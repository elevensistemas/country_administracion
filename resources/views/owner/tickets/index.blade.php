@extends('layouts.app')

@section('title', 'Mis Reclamos')
@section('page_title', 'Reclamos y Sugerencias')

@section('content')
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Mis Reclamos Iniciados</h5>
        <a href="{{ route('owner.tickets.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-chat-plus-fill me-2"></i>Nuevo Reclamo</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 10%;">ID</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">LOTE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 20%;">CATEGORÍA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ASUNTO / DETALLES</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 10%;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 10%;">ACCION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr class="border-bottom border-ios">
                        <td>#{{ $ticket->id }}</td>
                        <td class="fw-bold">Lote {{ $ticket->lot->number }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary badge-ios" style="font-size: 0.75rem;">
                                {{ $ticket->category->display_name }}
                            </span>
                        </td>
                        <td>
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">
                                <a href="{{ route('owner.tickets.show', $ticket) }}" class="text-decoration-none text-success">
                                    {{ $ticket->title }}
                                </a>
                            </h6>
                            <p class="text-muted m-0 text-truncate" style="font-size: 0.85rem; max-width: 400px;">
                                {{ $ticket->description }}
                            </p>
                            <small class="text-muted" style="font-size: 0.75rem;">Actualizado: {{ $ticket->updated_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if($ticket->status === 'open')
                                <span class="badge bg-danger text-white badge-ios">Nuevo</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="badge bg-warning text-dark badge-ios">En Proceso</span>
                            @elseif($ticket->status === 'resolved')
                                <span class="badge bg-success text-white badge-ios">Resuelto</span>
                            @else
                                <span class="badge bg-secondary text-white badge-ios">Cerrado</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('owner.tickets.show', $ticket) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success">
                                <i class="bi bi-chat-dots-fill me-1"></i> Chat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-chat-left-text text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No tienes reclamos registrados en el sistema.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
