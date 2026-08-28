@extends('layouts.app')

@section('title', 'Mi Cuenta Corriente')
@section('page_title', 'Movimientos Contables')

@section('content')
<!-- Filter bar -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('owner.accounting.index') }}" class="row g-3 align-items-center">
        <div class="col-md-8">
            <select name="functional_unit_id" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todas mis unidades funcionales</option>
                @foreach($user->functionalUnits as $unit)
                    <option value="{{ $unit->id }}" {{ request('functional_unit_id') == $unit->id ? 'selected' : '' }}>
                        Lote {{ $unit->lot->number }} (UF: {{ $unit->code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-grid">
            <a href="{{ route('owner.accounting.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Ledger Movements -->
<div class="ios-card">
    <h5 class="fw-bold mb-4">Extracto de Cuenta Corriente</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">UNIDAD</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DETALLE</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">DEBITO (CARGO)</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">CREDITO (PAGO)</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">SALDO ACUM.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $mov)
                    <tr class="border-bottom border-ios">
                        <td style="font-size: 0.9rem;">{{ $mov->date->format('d/m/Y') }}</td>
                        <td class="fw-semibold">Lote {{ $mov->functionalUnit->lot->number }}</td>
                        <td style="font-size: 0.9rem;">{{ $mov->description }}</td>
                        <td class="text-end fw-bold text-danger" style="font-size: 0.9rem;">
                            {{ $mov->type === 'debit' ? '$' . number_format($mov->amount, 2, ',', '.') : '-' }}
                        </td>
                        <td class="text-end fw-bold text-success" style="font-size: 0.9rem;">
                            {{ $mov->type === 'credit' ? '$' . number_format($mov->amount, 2, ',', '.') : '-' }}
                        </td>
                        <td class="text-end fw-bold {{ $mov->balance_after > 0 ? 'text-danger' : ($mov->balance_after < 0 ? 'text-success' : '') }}" style="font-size: 0.9rem;">
                            ${{ number_format($mov->balance_after, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-wallet2 text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No se registran movimientos en tu cuenta corriente.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $movements->links() }}
    </div>
</div>
@endsection
