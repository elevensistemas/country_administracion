@extends('layouts.app')

@section('title', 'Bitácora del Lote')
@section('page_title', 'Bitácora del Lote')

@section('content')
<!-- Filter bar -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('owner.history') }}" class="row g-3 align-items-center">
        <div class="col-md-8">
            <select name="lot_id" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todos mis lotes</option>
                @foreach($lots as $lot)
                    <option value="{{ $lot->id }}" {{ request('lot_id') == $lot->id ? 'selected' : '' }}>
                        Lote {{ $lot->number }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-grid">
            <a href="{{ route('owner.history') }}" class="btn btn-ios btn-ios-secondary">Limpiar Filtro</a>
        </div>
    </form>
</div>

<!-- History Stream -->
<div class="col-12">
    <div class="timeline-ios">
        @forelse($events as $event)
            <div class="timeline-item-ios">
                <div class="timeline-badge-ios border-success text-success">
                    <i class="bi bi-info-circle-fill text-success" style="font-size: 0.65rem;"></i>
                </div>

                <div class="ios-card shadow-none border-1 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-secondary-subtle text-secondary badge-ios me-2" style="font-size: 0.65rem;">LOTE {{ $event->lot->number }}</span>
                            <h6 class="fw-bold m-0 mt-1" style="font-size: 1rem;">{{ $event->title }}</h6>
                        </div>
                        <span class="text-muted" style="font-size: 0.8rem;">{{ $event->event_date->format('d/m/Y H:i') }}</span>
                    </div>

                    <p class="m-0 mt-2 text-muted" style="font-size: 0.9rem; line-height: 1.4;">
                        {{ $event->description }}
                    </p>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-journal-x text-muted fs-1 d-block mb-3"></i>
                <span class="text-muted">No se registran acontecimientos públicos en tus lotes.</span>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection
