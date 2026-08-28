@extends('layouts.app')

@section('title', 'Cuentas Corrientes')
@section('page_title', 'Estados de Cuenta Corriente')

@section('content')
<!-- Search & Filters -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.accounting.index') }}" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por código, lote, titular..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-4">
            <select name="balance_status" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todos los Saldos</option>
                <option value="debt" {{ request('balance_status') === 'debt' ? 'selected' : '' }}>Con Deuda (Saldos > 0)</option>
                <option value="surplus" {{ request('balance_status') === 'surplus' ? 'selected' : '' }}>Con Saldo a Favor (Saldos < 0)</option>
                <option value="zero" {{ request('balance_status') === 'zero' ? 'selected' : '' }}>Saldos en Cero</option>
            </select>
        </div>

        <div class="col-md-3 d-grid">
            <a href="{{ route('admin.accounting.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Ledger Accounts List -->
<div class="ios-card">
    <h5 class="fw-bold mb-4">Unidades Funcionales y Saldos</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">UNIDAD / LOTE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">CÓDIGO UF</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">TITULAR RESPONSABLE</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">INQUILINO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">SALDO ACTUAL</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 15%;">VER FICHA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($units as $unit)
                    <tr class="border-bottom border-ios">
                        <td class="fw-bold" style="font-size: 1.05rem;">Lote {{ $unit->lot->number }}</td>
                        <td class="text-muted" style="font-size: 0.9rem;">{{ $unit->code }}</td>
                        <td>
                            @if($unit->lot->owner)
                                <a href="{{ route('admin.owners.show', $unit->lot->owner) }}" class="text-decoration-none text-success fw-semibold">
                                    {{ $unit->lot->owner->full_name }}
                                </a>
                            @else
                                <span class="text-muted" style="font-size: 0.85rem;">Sin propietario</span>
                            @endif
                        </td>
                        <td>
                            @if($unit->lot->tenant)
                                <span style="font-size: 0.9rem;">{{ $unit->lot->tenant->full_name }}</span>
                            @else
                                <span class="text-muted" style="font-size: 0.85rem;">-</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $unit->balance > 0 ? 'text-danger' : ($unit->balance < 0 ? 'text-success' : '') }}" style="font-size: 1.05rem;">
                            ${{ number_format($unit->balance, 2, ',', '.') }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.accounting.show', $unit) }}" class="btn btn-sm btn-ios btn-ios-primary">
                                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Extracto
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-wallet2 text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No se encontraron cuentas corrientes con los filtros aplicados.</span>
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
