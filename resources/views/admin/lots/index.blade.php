@extends('layouts.app')

@section('title', 'Lotes')
@section('page_title', 'Administración de Lotes')

@section('content')
<!-- Search & Filters -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.lots.index') }}" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por número, código, propietario..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select form-control-ios">
                <option value="">Todos los Estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="under_construction" {{ request('status') === 'under_construction' ? 'selected' : '' }}>En Construcción</option>
                <option value="vacant" {{ request('status') === 'vacant' ? 'selected' : '' }}>Baldío / Vacante</option>
            </select>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-ios btn-ios-primary flex-fill">Buscar</button>
            <a href="{{ route('admin.lots.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Lots Table -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Lista de Lotes</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.functional-units.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-building me-2"></i>Ver Unidades Funcionales</a>
            <a href="{{ route('admin.lots.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-plus-circle-fill me-2"></i>Nuevo Lote</a>
        </div>
    </div>

    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">LOTE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">CÓDIGO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">PROPIETARIO ACTUAL</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">INQUILINO ACTUAL</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">SALDO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 20%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lots as $lot)
                    <tr class="border-bottom border-ios">
                        <td class="fw-bold" style="font-size: 1.05rem;">Lote {{ $lot->number }}</td>
                        <td class="text-muted" style="font-size: 0.9rem;">{{ $lot->code }}</td>
                        <td>
                            @if($lot->owner)
                                <a href="{{ route('admin.owners.show', $lot->owner) }}" class="text-decoration-none text-success fw-semibold">
                                    {{ $lot->owner->full_name }}
                                </a>
                            @else
                                <span class="text-muted" style="font-size: 0.85rem;">Sin propietario</span>
                            @endif
                        </td>
                        <td>
                            @if($lot->tenant)
                                <span class="fw-semibold">{{ $lot->tenant->full_name }}</span>
                            @else
                                <span class="text-muted" style="font-size: 0.85rem;">-</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $lot->balance > 0 ? 'text-danger' : ($lot->balance < 0 ? 'text-success' : '') }}">
                            ${{ number_format($lot->balance, 2, ',', '.') }}
                        </td>
                        <td>
                            @if($lot->status === 'active')
                                <span class="badge bg-success-subtle text-success badge-ios">Activo</span>
                            @elseif($lot->status === 'under_construction')
                                <span class="badge bg-warning-subtle text-warning badge-ios">En Obra</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary badge-ios">Baldío</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- History Timeline (Historia Clínica) -->
                                <a href="{{ route('admin.lots.history', $lot) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Ver Historia Clínica">
                                    <i class="bi bi-clock-history"></i> Historial
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('admin.lots.edit', $lot) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.lots.destroy', $lot) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este lote?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-house-door text-muted fs-1 d-block mb-3"></i>
                            @if(request('search') || request('status') || request('has_debt'))
                                <span class="text-muted">No se encontraron resultados para tu búsqueda o filtros.</span>
                                <div class="mt-3">
                                    <a href="{{ route('admin.lots.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Limpiar filtros</a>
                                </div>
                            @else
                                <span class="text-muted">Todavía no hay lotes registrados en el sistema.</span>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile view: Stacked Cards -->
    <div class="d-block d-md-none">
        @forelse($lots as $lot)
            <div class="p-3 border-bottom border-ios mb-3 rounded-4 bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold m-0" style="font-size: 1.1rem; color: var(--ios-primary);">Lote {{ $lot->number }}</h6>
                        <small class="text-muted">{{ $lot->code }}</small>
                    </div>
                    @if($lot->status === 'active')
                        <span class="badge bg-success-subtle text-success badge-ios">Activo</span>
                    @elseif($lot->status === 'under_construction')
                        <span class="badge bg-warning-subtle text-warning badge-ios">En Obra</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary badge-ios">Baldío</span>
                    @endif
                </div>

                <div class="my-2" style="font-size: 0.85rem; line-height: 1.5;">
                    <div class="mb-1">
                        <strong>Propietario:</strong> 
                        @if($lot->owner)
                            <a href="{{ route('admin.owners.show', $lot->owner) }}" class="text-decoration-none text-success fw-semibold">
                                {{ $lot->owner->full_name }}
                            </a>
                        @else
                            <span class="text-muted">Sin propietario</span>
                        @endif
                    </div>
                    <div class="mb-1">
                        <strong>Inquilino:</strong> 
                        @if($lot->tenant)
                            <span class="fw-semibold text-muted">{{ $lot->tenant->full_name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div>
                        <strong>Saldo Cta. Cte.:</strong>
                        <span class="fw-bold {{ $lot->balance > 0 ? 'text-danger' : ($lot->balance < 0 ? 'text-success' : '') }}">
                            ${{ number_format($lot->balance, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top border-ios">
                    <a href="{{ route('admin.lots.history', $lot) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success px-3 py-2">
                        <i class="bi bi-clock-history me-1"></i> Historial
                    </a>
                    <a href="{{ route('admin.lots.edit', $lot) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary px-3 py-2">
                        <i class="bi bi-pencil-fill me-1"></i> Editar
                    </a>
                    <form action="{{ route('admin.lots.destroy', $lot) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este lote?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger px-3 py-2">
                            <i class="bi bi-trash-fill me-1"></i> Borrar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-house-door text-muted fs-2 d-block mb-2"></i>
                @if(request('search') || request('status') || request('has_debt'))
                    <span>No se encontraron resultados para tu búsqueda o filtros.</span>
                    <div class="mt-3">
                        <a href="{{ route('admin.lots.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Limpiar filtros</a>
                    </div>
                @else
                    <span>Todavía no hay lotes registrados en el sistema.</span>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $lots->links() }}
    </div>
</div>
@endsection
