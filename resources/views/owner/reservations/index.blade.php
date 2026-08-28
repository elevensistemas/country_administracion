@extends('layouts.owner')

@section('title', 'Mis Reservas')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12 mb-2">
        <h4 class="fw-bold m-0 text-success"><i class="bi bi-calendar-event-fill me-2"></i>Espacios Comunes</h4>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Reserva de forma ágil las canchas, parrillas y salones del barrio.</p>
    </div>

    <!-- Available spaces grid -->
    <div class="col-12">
        <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Espacios Disponibles</h6>
        <div class="row g-3">
            @forelse($commonAreas as $area)
                <div class="col-12 col-md-6">
                    <div class="ios-card bg-body-tertiary p-3 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="fw-bold text-dark m-0" style="font-size: 1.05rem;">{{ $area->name }}</h6>
                                <span class="badge bg-success-subtle text-success badge-ios">
                                    {{ $area->price > 0 ? '$' . number_format($area->price, 0) : 'Sin Costo' }}
                                </span>
                            </div>
                            <p class="text-muted mt-2 mb-3" style="font-size: 0.82rem; line-height: 1.4;">
                                {{ $area->description ?? 'Ideal para eventos familiares y deportivos.' }}
                            </p>

                            <div class="d-flex gap-3 mb-3 text-muted" style="font-size: 0.78rem;">
                                <span><i class="bi bi-people-fill me-1"></i> Máx. {{ $area->capacity }} pers.</span>
                                <span><i class="bi bi-clock-fill me-1"></i> Duración: {{ $area->duration_minutes }} min.</span>
                            </div>
                        </div>

                        <a href="{{ route('owner.reservations.create', $area) }}" class="btn btn-sm btn-ios btn-ios-primary w-100 py-2.5">
                            <i class="bi bi-calendar-plus me-1"></i> Reservar Espacio
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">
                    No hay espacios comunes disponibles actualmente.
                </div>
            @endforelse
        </div>
    </div>

    <!-- My Reservations Title -->
    <div class="col-12 mt-4">
        <h6 class="fw-bold text-muted mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Mis Reservas en Lote {{ $activeLot ? $activeLot->number : 'N/C' }}</h6>
    </div>

    <!-- Reservations list loop -->
    @forelse($reservations as $res)
        <div class="col-12">
            <div class="ios-card bg-body-tertiary p-3 mb-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold m-0" style="font-size: 1rem;">{{ $res->commonArea->name }}</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Fecha: {{ $res->reservation_date->format('d/m/Y') }}</small>
                    </div>
                    @if($res->status === 'pending')
                        <span class="badge bg-warning text-dark badge-ios">Pendiente</span>
                    @elseif($res->status === 'confirmed')
                        <span class="badge bg-success text-white badge-ios">Confirmada</span>
                    @elseif($res->status === 'rejected')
                        <span class="badge bg-danger text-white badge-ios">Rechazada</span>
                    @elseif($res->status === 'canceled')
                        <span class="badge bg-secondary text-white badge-ios">Cancelada</span>
                    @else
                        <span class="badge bg-info text-dark badge-ios">Finalizada</span>
                    @endif
                </div>

                <div class="my-2 border-top border-ios pt-2 text-muted" style="font-size: 0.85rem; line-height: 1.4;">
                    <div class="mb-1"><i class="bi bi-clock me-1"></i> Horario: <strong>{{ substr($res->start_time, 0, 5) }} a {{ substr($res->end_time, 0, 5) }} hs</strong></div>
                    <div class="mb-1"><i class="bi bi-cash me-1"></i> Costo: <strong>${{ number_format($res->price, 2, ',', '.') }}</strong></div>
                    @if($res->charge_to_expenses)
                        <div class="mb-1 text-success"><i class="bi bi-file-earmark-plus-fill me-1"></i> Imputado a cuenta corriente (próxima expensa)</div>
                    @endif
                </div>

                <!-- Cancel action if future -->
                @if($res->reservation_date->isAfter(now()->subDay()) && in_array($res->status, ['pending', 'confirmed']))
                    <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top border-ios">
                        <form action="{{ route('owner.reservations.cancel', $res) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta reserva?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger px-3 py-2">
                                <i class="bi bi-slash-circle me-1"></i> Cancelar Reserva
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="ios-card">
                <i class="bi bi-calendar-x text-muted fs-1 d-block mb-3"></i>
                <span class="text-muted" style="font-size: 0.85rem;">Aún no posees reservas registradas para este lote.</span>
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="col-12 mt-3">
        {{ $reservations->links() }}
    </div>
</div>
@endsection
