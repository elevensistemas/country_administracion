@extends('layouts.app')

@section('title', 'Historial General')
@section('page_title', 'Historial de Acontecimientos')

@section('content')
<!-- Filter Panel -->
<div class="ios-card mb-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-funnel-fill text-success me-2"></i>Filtros Avanzados</h6>
    
    <form method="GET" action="{{ route('admin.history.index') }}" class="row g-3">
        <!-- Search -->
        <div class="col-md-4">
            <label class="form-label fw-semibold" style="font-size: 0.8rem;">Buscar por palabra o lote</label>
            <input type="text" name="search" class="form-control form-control-ios" placeholder="Ej. Lote 34, multa..." value="{{ request('search') }}">
        </div>

        <!-- Lot Filter -->
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 0.8rem;">Lote</label>
            <select name="lot_id" class="form-select form-control-ios">
                <option value="">Todos los Lotes</option>
                @foreach($lots as $lot)
                    <option value="{{ $lot->id }}" {{ request('lot_id') == $lot->id ? 'selected' : '' }}>
                        Lote {{ $lot->number }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Category Filter -->
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 0.8rem;">Categoría</label>
            <select name="category_id" class="form-select form-control-ios">
                <option value="">Todas las Categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->display_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Order Filter -->
        <div class="col-md-2">
            <label class="form-label fw-semibold" style="font-size: 0.8rem;">Orden</label>
            <select name="order" class="form-select form-control-ios">
                <option value="desc" {{ request('order') !== 'asc' ? 'selected' : '' }}>Más Recientes</option>
                <option value="asc" {{ request('order') === 'asc' ? 'selected' : '' }}>Más Antiguos</option>
            </select>
        </div>

        <!-- Date Start -->
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 0.8rem;">Fecha Desde</label>
            <input type="date" name="date_start" class="form-control form-control-ios" value="{{ request('date_start') }}">
        </div>

        <!-- Date End -->
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 0.8rem;">Fecha Hasta</label>
            <input type="date" name="date_end" class="form-control form-control-ios" value="{{ request('date_end') }}">
        </div>

        <!-- Visibility Filter -->
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 0.8rem;">Visibilidad</label>
            <select name="visibility" class="form-select form-control-ios">
                <option value="">Cualquier Visibilidad</option>
                <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Público (Vecinos)</option>
                <option value="internal" {{ request('visibility') === 'internal' ? 'selected' : '' }}>Interno / Administrativo</option>
            </select>
        </div>

        <!-- Priority Filter -->
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-ios btn-ios-primary flex-grow-1">Aplicar Filtros</button>
            <a href="{{ route('admin.history.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- History Stream -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Acontecimientos del Barrio</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">FECHA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">LOTE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">CATEGORÍA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">EVENTO / DETALLES</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">VISIBILIDAD</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 10%;">VER</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr class="border-bottom border-ios">
                        <td style="font-size: 0.9rem;">
                            {{ $event->event_date->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.lots.history', $event->lot) }}" class="fw-bold text-success text-decoration-none">
                                Lote {{ $event->lot->number }}
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary badge-ios" style="font-size: 0.7rem;">
                                {{ $event->category->display_name }}
                            </span>
                        </td>
                        <td>
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $event->title }}</h6>
                            <p class="text-muted m-0 text-truncate" style="font-size: 0.85rem; max-width: 450px;">
                                {{ $event->description }}
                            </p>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Registrado por: {{ $event->user ? $event->user->full_name : 'Sistema' }}
                            </small>
                        </td>
                        <td>
                            @if($event->visibility === 'public')
                                <span class="badge bg-success-subtle text-success badge-ios" style="font-size: 0.7rem;"><i class="bi bi-globe me-1"></i>Público</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger badge-ios" style="font-size: 0.7rem;"><i class="bi bi-lock me-1"></i>Interno</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.lots.history', $event->lot) }}#event-{{ $event->id }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary">
                                <i class="bi bi-arrow-right-short fs-5"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-journal-x text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No se encontraron acontecimientos registrados con los filtros aplicados.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection
