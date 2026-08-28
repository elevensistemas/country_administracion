@extends('layouts.app')

@section('title', 'Reportes')
@section('page_title', 'Tablero de Control e Informes')

@section('content')
<div class="row">
    <!-- Left Column: Finances Summary -->
    <div class="col-lg-6 mb-4">
        <!-- Collection Report -->
        <div class="ios-card">
            <h5 class="fw-bold mb-4"><i class="bi bi-graph-up-arrow text-success me-2"></i>Recaudación Mensual (Últimos 6 meses)</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">PERÍODO</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">RECAUDADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyCollection as $col)
                            <tr class="border-bottom border-ios">
                                <td class="fw-semibold">Mes {{ $col->month }}</td>
                                <td class="text-end fw-bold text-success" style="font-size: 1rem;">
                                    ${{ number_format($col->total_collected, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4">No hay cobros registrados en los últimos meses.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Debt by Lot Status -->
        <div class="ios-card">
            <h5 class="fw-bold mb-4"><i class="bi bi-house-door-fill text-success me-2"></i>Distribución de Deuda por Estado de Lote</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO FÍSICO LOTE</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">DEUDA ACUMULADA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debtByLotStatus as $d)
                            <tr class="border-bottom border-ios">
                                <td class="text-capitalizefw-semibold">{{ str_replace('_', ' ', $d->status) }}</td>
                                <td class="text-end fw-bold text-danger" style="font-size: 1rem;">
                                    ${{ number_format($d->total_debt, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4">Sin deudas acumuladas en los lotes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Tickets and General balances -->
    <div class="col-lg-6 mb-4">
        <!-- Balance sheet indicators -->
        <div class="ios-card bg-body-secondary border-0 mb-4 py-4">
            <h5 class="fw-bold mb-4 px-3"><i class="bi bi-wallet2 text-success me-2"></i>Estado Contable Consolidado</h5>
            
            <div class="row text-center">
                <div class="col-6 border-end border-ios">
                    <span class="text-muted d-block" style="font-size: 0.85rem;">DEUDA ACTIVA VECINOS</span>
                    <h3 class="fw-bold text-danger mt-1">${{ number_format($overdueExpensesAmount, 2, ',', '.') }}</h3>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block" style="font-size: 0.85rem;">SALDOS A FAVOR VECINOS</span>
                    <h3 class="fw-bold text-success mt-1">${{ number_format(abs($surplusAmount), 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Tickets category distribution -->
        <div class="ios-card">
            <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text-fill text-success me-2"></i>Distribución de Reclamos por Categoría</h5>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">CATEGORÍA RECLAMO</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">CANTIDAD TICKETS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ticketsByCategory as $tc)
                            <tr class="border-bottom border-ios">
                                <td class="fw-semibold">{{ $tc->category->display_name }}</td>
                                <td class="text-end fw-bold text-dark" style="font-size: 1rem;">
                                    {{ $tc->count }} reclamos
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-4">No se registran reclamos en el sistema.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
