@extends('layouts.app')

@section('title', 'Inicio')
@section('page_title', 'Tablero Principal')

@section('content')
<!-- KPI Row -->
<div class="row">
    <!-- Total Debt Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card d-flex align-items-center">
            <div class="bg-danger-subtle text-danger rounded-4 p-3 me-3">
                <i class="bi bi-exclamation-octagon-fill" style="font-size: 1.8rem;"></i>
            </div>
            <div>
                <span class="text-muted d-block" style="font-size: 0.85rem; font-weight: 500;">Deuda Total</span>
                <h4 class="fw-bold m-0 text-danger">${{ number_format($totalDebt, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- Total Surplus Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card d-flex align-items-center">
            <div class="bg-success-subtle text-success rounded-4 p-3 me-3">
                <i class="bi bi-wallet2" style="font-size: 1.8rem;"></i>
            </div>
            <div>
                <span class="text-muted d-block" style="font-size: 0.85rem; font-weight: 500;">Saldos a Favor</span>
                <h4 class="fw-bold m-0 text-success">${{ number_format($totalSurplus, 2, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- Pending Payments Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card d-flex align-items-center">
            <div class="bg-primary-subtle text-primary rounded-4 p-3 me-3">
                <i class="bi bi-cash-stack" style="font-size: 1.8rem;"></i>
            </div>
            <div>
                <span class="text-muted d-block" style="font-size: 0.85rem; font-weight: 500;">Pagos a Conciliar</span>
                <h4 class="fw-bold m-0 text-primary">
                    {{ $paymentsPending }}
                    @if($paymentsPending > 0)
                        <span class="badge bg-danger rounded-circle p-1 ms-1" style="font-size: 0.5rem; vertical-align: super;"><span class="visually-hidden">pendientes</span></span>
                    @endif
                </h4>
            </div>
        </div>
    </div>

    <!-- Adoption Card -->
    <div class="col-sm-6 col-xl-3">
        <div class="ios-card d-flex align-items-center">
            <div class="bg-info-subtle text-info rounded-4 p-3 me-3">
                <i class="bi bi-graph-up-arrow" style="font-size: 1.8rem;"></i>
            </div>
            <div>
                <span class="text-muted d-block" style="font-size: 0.85rem; font-weight: 500;">Adopción Portal</span>
                <h4 class="fw-bold m-0 text-info">{{ $adoptionRate }}%</h4>
            </div>
        </div>
    </div>
</div>

<!-- Alert for pending actions -->
@if($paymentsPending > 0 || $ticketsNew > 0)
    <div class="ios-card border-warning bg-warning-subtle d-flex align-items-center p-3 mb-4">
        <i class="bi bi-exclamation-triangle-fill text-warning fs-3 me-3"></i>
        <div class="flex-grow-1 text-dark">
            <h6 class="fw-bold m-0">Acciones pendientes de revisión</h6>
            <span style="font-size: 0.85rem;">
                Tienes <strong>{{ $paymentsPending }} pagos pendientes</strong> de conciliar y <strong>{{ $ticketsNew }} nuevos reclamos</strong> sin asignar.
            </span>
        </div>
        <div class="d-flex gap-2">
            @if($paymentsPending > 0)
                <a href="{{ route('admin.payments.index') }}" class="btn btn-ios btn-ios-primary btn-sm btn-success">Conciliar Pagos</a>
            @endif
            @if($ticketsNew > 0)
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-ios btn-ios-secondary btn-sm bg-white">Ver Reclamos</a>
            @endif
        </div>
    </div>
@endif

<!-- Charts Section -->
<div class="row">
    <!-- Claims by Category Chart -->
    <div class="col-md-6 col-xl-4">
        <div class="ios-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-success me-2"></i>Reclamos por Categoría</h6>
            <div style="height: 240px; position: relative;">
                @if($claimsByCategory->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted bg-light-subtle rounded-3" style="border: 1px dashed var(--ios-border);">
                        <i class="bi bi-pie-chart fs-1 mb-2 text-muted opacity-50"></i>
                        <span style="font-size: 0.85rem;">No hay reclamos registrados</span>
                    </div>
                @else
                    <canvas id="categoriesChart"></canvas>
                @endif
            </div>
        </div>
    </div>

    <!-- Claims by Status Chart -->
    <div class="col-md-6 col-xl-4">
        <div class="ios-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill text-success me-2"></i>Estado de los Reclamos</h6>
            <div style="height: 240px; position: relative;">
                @if($claimsByStatus->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted bg-light-subtle rounded-3" style="border: 1px dashed var(--ios-border);">
                        <i class="bi bi-bar-chart fs-1 mb-2 text-muted opacity-50"></i>
                        <span style="font-size: 0.85rem;">No hay reclamos registrados</span>
                    </div>
                @else
                    <canvas id="statusChart"></canvas>
                @endif
            </div>
        </div>
    </div>

    <!-- Channels Usage Chart -->
    <div class="col-md-12 col-xl-4">
        <div class="ios-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-phone-fill text-success me-2"></i>Uso por Canal Operativo</h6>
            <div style="height: 240px; position: relative;">
                @if($channelUsage->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted bg-light-subtle rounded-3" style="border: 1px dashed var(--ios-border);">
                        <i class="bi bi-phone fs-1 mb-2 text-muted opacity-50"></i>
                        <span style="font-size: 0.85rem;">No hay reclamos registrados</span>
                    </div>
                @else
                    <canvas id="channelsChart"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Details and Activities -->
<div class="row">
    <!-- Recent Tickets -->
    <div class="col-lg-6">
        <div class="ios-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-chat-left-text-fill text-success me-2"></i>Reclamos Recientes</h6>
                <a href="{{ route('admin.tickets.index') }}" class="text-success text-decoration-none" style="font-size: 0.85rem; font-weight: 500;">Ver todos</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentTickets as $ticket)
                    <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-start border-bottom border-ios">
                        <div class="me-auto">
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-decoration-none text-reset">
                                    {{ $ticket->title }}
                                </a>
                            </h6>
                            <small class="text-muted">
                                Lote {{ $ticket->lot->number }} • por {{ $ticket->user->full_name }}
                            </small>
                        </div>
                        <div class="d-flex flex-column align-items-end">
                            <span class="badge rounded-pill bg-secondary text-capitalize mb-1" style="font-size: 0.75rem;">
                                {{ str_replace('_', ' ', $ticket->status) }}
                            </span>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                {{ $ticket->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-chat-left-dots text-muted fs-1 d-block mb-2"></i>
                        <span class="text-muted">No hay reclamos registrados</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="col-lg-6">
        <div class="ios-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-cash-stack text-success me-2"></i>Pagos Reportados</h6>
                <a href="{{ route('admin.payments.index') }}" class="text-success text-decoration-none" style="font-size: 0.85rem; font-weight: 500;">Ver todos</a>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless align-middle m-0">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.8rem; font-weight: 600;">LOTE</th>
                            <th class="text-muted" style="font-size: 0.8rem; font-weight: 600;">PROPIETARIO</th>
                            <th class="text-muted" style="font-size: 0.8rem; font-weight: 600;">IMPORTE</th>
                            <th class="text-muted" style="font-size: 0.8rem; font-weight: 600;">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr class="border-bottom border-ios">
                                <td class="fw-semibold">Lote {{ $payment->lot->number }}</td>
                                <td>{{ $payment->owner->full_name }}</td>
                                <td class="fw-bold text-success">${{ number_format($payment->amount, 2, ',', '.') }}</td>
                                <td>
                                    @if($payment->status === 'approved')
                                        <span class="badge bg-success-subtle text-success badge-ios">Aprobado</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning badge-ios">Pendiente</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger badge-ios">Rechazado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="bi bi-cash text-muted fs-1 d-block mb-2"></i>
                                    <span class="text-muted">No hay pagos informados recientemente</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Conflictive Lots -->
    <div class="col-md-6">
        <div class="ios-card h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-house-exclamation-fill text-success me-2"></i>Lotes con Mayor Cantidad de Reclamos</h6>
            <ul class="list-group list-group-flush">
                @forelse($topLotsWithClaims as $lot)
                    <li class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center border-bottom border-ios">
                        <div>
                            <span class="fw-bold" style="font-size: 1rem;">Lote {{ $lot->number }}</span>
                            <small class="text-muted d-block">Propietario: {{ $lot->owner ? $lot->owner->full_name : 'Sin propietario asignado' }}</small>
                        </div>
                        <span class="badge bg-danger rounded-pill px-3 py-2" style="font-size: 0.85rem;">
                            {{ $lot->tickets_count }} reclamos
                        </span>
                    </li>
                @empty
                    <li class="list-group-item bg-transparent border-0 px-0 py-4 text-center">
                        <span class="text-muted">Sin actividad registrada</span>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Adoption panel widget -->
    <div class="col-md-6">
        <div class="ios-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-graph-up-arrow text-success me-2"></i>Detalle de Adopción Digital</h6>
                <a href="{{ route('admin.adoption.index') }}" class="text-success text-decoration-none" style="font-size: 0.85rem; font-weight: 500;">Ver panel</a>
            </div>
            <div class="row align-items-center h-75">
                <div class="col-sm-6 text-center">
                    <h1 class="fw-bold text-success display-4">{{ $adoptionRate }}%</h1>
                    <span class="text-muted" style="font-size: 0.85rem;">Tasa general de adopción</span>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size: 0.85rem;"><i class="bi bi-circle-fill text-success me-2" style="font-size: 0.6rem;"></i>Activos</span>
                        <span class="fw-bold">{{ $activeUsers }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size: 0.85rem;"><i class="bi bi-circle-fill text-warning me-2" style="font-size: 0.6rem;"></i>Nunca ingresaron</span>
                        <span class="fw-bold">{{ $neverLoggedIn }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size: 0.85rem;"><i class="bi bi-circle-fill text-secondary me-2" style="font-size: 0.6rem;"></i>Pendiente invitación</span>
                        <span class="fw-bold">{{ $pendingInvite }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Colors palette iOS style
        const colors = {
            green: '#34c759',
            blue: '#007aff',
            orange: '#ff9500',
            red: '#ff3b30',
            teal: '#30b0c7',
            purple: '#af52de',
            gray: '#8e8e93',
            lightGray: '#e5e5ea'
        };

        // 1. Categories Chart
        const catCanvas = document.getElementById('categoriesChart');
        if (catCanvas) {
            const catData = {
                labels: {!! json_encode($claimsByCategory->pluck('category')) !!},
                datasets: [{
                    data: {!! json_encode($claimsByCategory->pluck('total')) !!},
                    backgroundColor: [colors.green, colors.blue, colors.orange, colors.red, colors.teal, colors.purple, colors.gray],
                    borderWidth: 0
                }]
            };
            new Chart(catCanvas, {
                type: 'doughnut',
                data: catData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                font: { family: 'Outfit', size: 11 }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // 2. Status Chart
        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            const statusData = {
                labels: {!! json_encode($claimsByStatus->pluck('status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s)))) !!},
                datasets: [{
                    label: 'Reclamos',
                    data: {!! json_encode($claimsByStatus->pluck('total')) !!},
                    backgroundColor: colors.blue,
                    borderRadius: 8,
                    barThickness: 24
                }]
            };
            new Chart(statusCanvas, {
                type: 'bar',
                data: statusData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // 3. Channels Chart
        const chanCanvas = document.getElementById('channelsChart');
        if (chanCanvas) {
            const chanData = {
                labels: {!! json_encode($channelUsage->pluck('channel')->map(fn($c) => ucfirst($c))) !!},
                datasets: [{
                    data: {!! json_encode($channelUsage->pluck('total')) !!},
                    backgroundColor: [colors.green, colors.orange, colors.blue, colors.purple],
                    borderWidth: 0
                }]
            };
            new Chart(chanCanvas, {
                type: 'pie',
                data: chanData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                font: { family: 'Outfit', size: 11 }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
