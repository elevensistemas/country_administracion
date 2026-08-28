@extends('layouts.app')

@section('title', 'Reclamos y Tickets')
@section('page_title', 'Administración de Reclamos')

@section('content')
<!-- Filter & Search Card -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-3 align-items-center">
        <!-- Search -->
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por título, lote..." value="{{ request('search') }}">
            </div>
        </div>

        <!-- Status Filter -->
        <div class="col-md-3">
            <select name="status" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Abiertos (Pendientes y En Curso)</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abierto (Nuevo)</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En Proceso</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resuelto</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Cerrado</option>
            </select>
        </div>

        <!-- Category Filter -->
        <div class="col-md-3">
            <select name="category_id" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todas las Categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->display_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Reset Button -->
        <div class="col-md-2 d-grid">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Tickets List -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Reclamos Registrados</h5>
        <a href="{{ route('admin.tickets.create') }}" class="btn btn-sm btn-ios btn-ios-primary text-success">
            <i class="bi bi-plus-lg me-1"></i> Registrar Reclamo / Incidente
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 8%;">ID</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">LOTE / UF</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">CATEGORÍA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ASUNTO / DETALLES</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">RESPONSABLE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 10%;">PRIORIDAD</th>
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
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-decoration-none text-success">
                                    {{ $ticket->title }}
                                </a>
                            </h6>
                            <p class="text-muted m-0 text-truncate" style="font-size: 0.85rem; max-width: 300px;">
                                {{ $ticket->description }}
                            </p>
                            <small class="text-muted" style="font-size: 0.75rem;">Iniciado por: {{ $ticket->user->full_name }} • {{ $ticket->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span style="font-size: 0.9rem;">
                                {{ $ticket->assignee ? $ticket->assignee->full_name : 'Sin asignar' }}
                            </span>
                        </td>
                        <td>
                            @if($ticket->priority === 'high' || $ticket->priority === 'urgent')
                                <span class="badge bg-danger text-white badge-ios text-uppercase" style="font-size: 0.65rem;">{{ $ticket->priority }}</span>
                            @elseif($ticket->priority === 'medium')
                                <span class="badge bg-warning text-dark badge-ios text-uppercase" style="font-size: 0.65rem;">media</span>
                            @else
                                <span class="badge bg-secondary text-white badge-ios text-uppercase" style="font-size: 0.65rem;">baja</span>
                            @endif
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
                            <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-ios btn-ios-primary">
                                <i class="bi bi-chat-dots-fill me-1"></i> Abrir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-chat-left-text text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No se encontraron tickets con los filtros actuales.</span>
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
