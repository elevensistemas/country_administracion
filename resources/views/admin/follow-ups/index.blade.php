@extends('layouts.app')

@section('title', 'Seguimientos')
@section('page_title', 'Tareas de Seguimiento')

@section('content')
<!-- Filter & Search Card -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.follow-ups.index') }}" class="row g-3 align-items-center">
        <!-- Search -->
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por tarea o número de lote..." value="{{ request('search') }}">
            </div>
        </div>

        <!-- Status Filter -->
        <div class="col-md-3">
            <select name="status" class="form-select form-control-ios">
                <option value="">Activos (Pendiente, En Proceso, Espera)</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>En Proceso</option>
                <option value="waiting_response" {{ request('status') === 'waiting_response' ? 'selected' : '' }}>Esperando Respuesta</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completados</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelados</option>
            </select>
        </div>

        <!-- Assignee Filter -->
        <div class="col-md-3">
            <select name="assignee_id" class="form-select form-control-ios">
                <option value="">Todos los Responsables</option>
                @foreach($operators as $op)
                    <option value="{{ $op->id }}" {{ request('assignee_id') == $op->id ? 'selected' : '' }}>
                        {{ $op->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Buttons -->
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-ios btn-ios-primary flex-fill">Filtrar</button>
            <a href="{{ route('admin.follow-ups.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Follow ups List -->
<div class="ios-card">
    <h5 class="fw-bold mb-4">Lista de Seguimientos</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 10%;">VENCIMIENTO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">LOTE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">TAREA / DESCRIPCIÓN</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">RESPONSABLE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 10%;">PRIORIDAD</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 10%;">ACCION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($followUps as $fu)
                    <tr class="border-bottom border-ios {{ $fu->due_date->isPast() && $fu->status !== 'completed' && $fu->status !== 'cancelled' ? 'table-danger-subtle' : '' }}">
                        <td class="fw-semibold" style="font-size: 0.9rem;">
                            {{ $fu->due_date->format('d/m/Y') }}
                            @if($fu->due_date->isPast() && $fu->status !== 'completed' && $fu->status !== 'cancelled')
                                <small class="text-danger d-block fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> VENCIDO</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.lots.history', $fu->lot) }}" class="fw-bold text-success text-decoration-none">
                                Lote {{ $fu->lot->number }}
                            </a>
                        </td>
                        <td>
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $fu->reason }}</h6>
                            @if($fu->event)
                                <small class="text-muted d-block" style="font-size: 0.8rem;">Origen: {{ $fu->event->title }}</small>
                            @endif
                            @if($fu->notes)
                                <small class="text-muted d-block text-truncate" style="max-width: 300px; font-size: 0.8rem;">Notas: {{ $fu->notes }}</small>
                            @endif
                        </td>
                        <td>
                            <span style="font-size: 0.9rem;">{{ $fu->assignee ? $fu->assignee->full_name : 'No asignado' }}</span>
                        </td>
                        <td>
                            @if($fu->priority === 'high' || $fu->priority === 'urgent')
                                <span class="badge bg-danger text-white badge-ios">{{ $fu->priority }}</span>
                            @elseif($fu->priority === 'medium')
                                <span class="badge bg-warning text-dark badge-ios">media</span>
                            @else
                                <span class="badge bg-secondary text-white badge-ios">baja</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.follow-ups.status', $fu) }}" method="POST">
                                @csrf
                                <select name="status" class="form-select form-select-sm form-control-ios py-1" style="font-size: 0.8rem;" onchange="this.form.submit()">
                                    <option value="pending" {{ $fu->status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="in_progress" {{ $fu->status === 'in_progress' ? 'selected' : '' }}>En Proceso</option>
                                    <option value="waiting_response" {{ $fu->status === 'waiting_response' ? 'selected' : '' }}>Espera de Rta.</option>
                                    <option value="completed" {{ $fu->status === 'completed' ? 'selected' : '' }}>Completado</option>
                                    <option value="cancelled" {{ $fu->status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.lots.history', $fu->lot) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Ir al Historial del Lote">
                                <i class="bi bi-clock-history"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-calendar-check text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No tienes tareas de seguimiento activas.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $followUps->links() }}
    </div>
</div>
@endsection
