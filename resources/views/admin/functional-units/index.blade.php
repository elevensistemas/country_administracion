@extends('layouts.app')

@section('title', 'Unidades Funcionales')
@section('page_title', 'Administración de Unidades Funcionales')

@section('content')
<!-- Search & Filters -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.functional-units.index') }}" class="row g-3 align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por código, nombre, número de lote..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-ios btn-ios-primary flex-fill">Buscar</button>
            <a href="{{ route('admin.functional-units.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Units Table -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Lista de Unidades Funcionales</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.lots.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-house-fill me-2"></i>Ver Lotes</a>
            <a href="{{ route('admin.functional-units.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-plus-circle-fill me-2"></i>Nueva Unidad Funcional</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">CÓDIGO UF</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">NOMBRE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">LOTE ASOCIADO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">PROPIETARIOS CO-TITULARES</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">SALDO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 15%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                    <tr class="border-bottom border-ios">
                        <td class="fw-bold">{{ $unit->code }}</td>
                        <td>{{ $unit->name }}</td>
                        <td>
                            @if($unit->lot)
                                <a href="{{ route('admin.lots.history', $unit->lot) }}" class="text-decoration-none text-success fw-semibold">
                                    Lote {{ $unit->lot->number }}
                                </a>
                            @else
                                <span class="text-muted">Sin lote</span>
                            @endif
                        </td>
                        <td>
                            @forelse($unit->owners as $owner)
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 mb-1 d-inline-block" style="font-size: 0.75rem;">
                                    {{ $owner->full_name }}
                                </span>
                            @empty
                                <span class="text-muted" style="font-size: 0.85rem;">Sin propietarios</span>
                            @endforelse
                        </td>
                        <td class="text-end fw-bold {{ $unit->balance > 0 ? 'text-danger' : ($unit->balance < 0 ? 'text-success' : '') }}">
                            ${{ number_format($unit->balance, 2, ',', '.') }}
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- Edit -->
                                <a href="{{ route('admin.functional-units.edit', $unit) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.functional-units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta unidad funcional?');">
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
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-building-dash text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No se encontraron unidades funcionales registradas.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $units->links() }}
    </div>
</div>
@endsection
