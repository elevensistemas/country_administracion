@extends('layouts.owner')

@section('title', 'Inicio')

@section('content')
<div class="row g-3">
    <!-- Balance Card -->
    <div class="col-12">
        <div class="ios-card bg-body-secondary border-0 text-center py-4 mb-2">
            <span class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 1px;">Saldo Pendiente</span>
            <h1 class="display-4 fw-bold m-0 {{ $activeLotBalance > 0 ? 'text-danger' : ($activeLotBalance < 0 ? 'text-success' : '') }}" style="font-size: 2.5rem;">
                ${{ number_format(abs($activeLotBalance), 2, ',', '.') }}
                @if($activeLotBalance < 0)
                    <small class="fs-6 fw-normal text-success d-block mt-1">Saldo a Favor</small>
                @endif
            </h1>
            
            <p class="text-muted mt-2 mb-3" style="font-size: 0.8rem;">
                @if($activeLotBalance > 0)
                    Próximo vencimiento: <strong>{{ $nextDue->format('d/m/Y') }}</strong>
                @else
                    ¡Tu cuenta está al día!
                @endif
            </p>

            <div class="row g-2 px-3">
                <div class="col-6">
                    <a href="{{ route('owner.payments.report') }}" class="btn btn-ios btn-ios-primary w-100 py-2.5">
                        <i class="bi bi-wallet2 me-1"></i> Informar Pago
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('owner.expenses.index') }}" class="btn btn-ios btn-ios-secondary w-100 py-2.5">
                        <i class="bi bi-receipt me-1"></i> Ver Expensas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Icon Grid -->
    <div class="col-12">
        <div class="ios-card p-3">
            <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Accesos Rápidos</h6>
            <div class="row g-3 text-center">
                <div class="col-3">
                    <a href="{{ route('owner.expenses.index') }}" class="text-decoration-none text-reset d-flex flex-column align-items-center">
                        <div class="bg-success-subtle text-success rounded-4 d-flex align-items-center justify-content-center mb-1" style="width: 50px; height: 50px;">
                            <i class="bi bi-receipt-cutoff fs-3"></i>
                        </div>
                        <small class="fw-semibold" style="font-size: 0.72rem;">Expensas</small>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('owner.guests.index') }}" class="text-decoration-none text-reset d-flex flex-column align-items-center">
                        <div class="bg-primary-subtle text-primary rounded-4 d-flex align-items-center justify-content-center mb-1" style="width: 50px; height: 50px;">
                            <i class="bi bi-qr-code fs-3"></i>
                        </div>
                        <small class="fw-semibold" style="font-size: 0.72rem;">Invitados</small>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('owner.reservations.index') }}" class="text-decoration-none text-reset d-flex flex-column align-items-center">
                        <div class="bg-info-subtle text-info rounded-4 d-flex align-items-center justify-content-center mb-1" style="width: 50px; height: 50px;">
                            <i class="bi bi-calendar-event fs-3"></i>
                        </div>
                        <small class="fw-semibold" style="font-size: 0.72rem;">Reservas</small>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('owner.tickets.index') }}" class="text-decoration-none text-reset d-flex flex-column align-items-center">
                        <div class="bg-warning-subtle text-warning rounded-4 d-flex align-items-center justify-content-center mb-1" style="width: 50px; height: 50px;">
                            <i class="bi bi-chat-left-text fs-3"></i>
                        </div>
                        <small class="fw-semibold" style="font-size: 0.72rem;">Reclamos</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Widgets (Reservations & Payments) -->
    @if($upcomingReservation || $latestPayment)
        <div class="col-12">
            <div class="row g-2">
                @if($upcomingReservation)
                    <div class="col-12">
                        <div class="ios-card bg-info-subtle bg-opacity-25 border-info-subtle p-3 mb-0">
                            <h6 class="fw-bold text-info mb-2" style="font-size: 0.85rem;"><i class="bi bi-calendar-check-fill me-1"></i> Próxima Reserva</h6>
                            <span class="d-block fw-semibold" style="font-size: 0.9rem;">{{ $upcomingReservation->commonArea->name }}</span>
                            <small class="text-muted" style="font-size: 0.8rem;">
                                {{ $upcomingReservation->reservation_date->format('d/m/Y') }} de {{ substr($upcomingReservation->start_time, 0, 5) }} a {{ substr($upcomingReservation->end_time, 0, 5) }} hs
                            </small>
                        </div>
                    </div>
                @endif

                @if($latestPayment)
                    <div class="col-12">
                        <div class="ios-card bg-body-tertiary p-3 mb-0">
                            <h6 class="fw-bold text-muted mb-2" style="font-size: 0.85rem;"><i class="bi bi-credit-card-2-back-fill me-1"></i> Último Pago Informado</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="d-block fw-semibold" style="font-size: 0.9rem;">${{ number_format($latestPayment->amount, 2, ',', '.') }}</span>
                                    <small class="text-muted" style="font-size: 0.8rem;">{{ $latestPayment->payment_date->format('d/m/Y') }}</small>
                                </div>
                                @if($latestPayment->status === 'pending')
                                    <span class="badge bg-warning text-dark badge-ios">Pendiente de Aprobación</span>
                                @elseif($latestPayment->status === 'approved')
                                    <span class="badge bg-success text-white badge-ios">Aprobado</span>
                                @else
                                    <span class="badge bg-danger text-white badge-ios">Rechazado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Announcements / Novedades -->
    <div class="col-12">
        <div class="ios-card mb-2">
            <h6 class="fw-bold mb-3 text-success"><i class="bi bi-megaphone-fill me-2"></i>Avisos y Novedades</h6>
            
            <div class="d-flex flex-column gap-3">
                @forelse($announcements as $ann)
                    <div class="border-bottom border-ios pb-2 last-border-none">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-success-subtle text-success badge-ios" style="font-size: 0.6rem;">COMUNICADO</span>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $ann->publish_date->format('d/m/Y') }}</small>
                        </div>
                        <h6 class="fw-bold m-0" style="font-size: 0.95rem;">
                            <a href="{{ route('owner.news.show', $ann) }}" class="text-decoration-none text-reset">
                                {{ $ann->title }}
                            </a>
                        </h6>
                        <p class="text-muted m-0 mt-1" style="font-size: 0.82rem; line-height: 1.4;">{{ $ann->summary ?? Str::limit(strip_tags($ann->content), 80) }}</p>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted" style="font-size: 0.85rem;">
                        No hay avisos publicados recientemente.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .last-border-none:last-child {
        border-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
@endsection
