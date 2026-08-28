@extends('layouts.app')

@section('title', 'Extracto Lote ' . $functionalUnit->lot->number)
@section('page_title', 'Extracto de Cuenta Corriente')

@section('content')
<div class="row">
    <!-- Ledger Ledger (Left) -->
    <div class="col-lg-8 mb-4">
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-bold m-0">Movimientos Registrados</h5>
                    <small class="text-muted">Lote {{ $functionalUnit->lot->number }} (UF: {{ $functionalUnit->code }})</small>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Saldo de Cuenta</small>
                    <h3 class="fw-bold m-0 {{ $functionalUnit->balance > 0 ? 'text-danger' : ($functionalUnit->balance < 0 ? 'text-success' : '') }}">
                        ${{ number_format($functionalUnit->balance, 2, ',', '.') }}
                    </h3>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DETALLE / CONCEPTO</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">DEBITO</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">CREDITO</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">SALDO ACUM.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $mov)
                            <tr class="border-bottom border-ios">
                                <td style="font-size: 0.9rem;">{{ $mov->date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="d-block fw-semibold" style="font-size: 0.9rem;">{{ $mov->description }}</span>
                                    @if($mov->related_model_type)
                                        <small class="text-muted" style="font-size: 0.75rem;">Enlace: {{ class_basename($mov->related_model_type) }} #{{ $mov->related_model_id }}</small>
                                    @endif
                                </td>
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
                                <td colspan="5" class="text-center py-4">
                                    <i class="bi bi-journal-x text-muted fs-2 d-block mb-2"></i>
                                    <span class="text-muted">No se registran movimientos contables en esta cuenta corriente.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $movements->links() }}
            </div>
        </div>
    </div>

    <!-- Administrative Adjustments & Info (Right) -->
    <div class="col-lg-4">
        <!-- Lot Summary Card -->
        <div class="ios-card bg-body-secondary border-0 mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-house-door-fill text-success me-2"></i>Datos de Cuenta</h6>
            
            <div class="mb-3">
                <span class="text-muted d-block" style="font-size: 0.75rem;">Propietario Titular</span>
                <span class="fw-bold">
                    @if($functionalUnit->lot->owner)
                        <a href="{{ route('admin.owners.show', $functionalUnit->lot->owner) }}" class="text-success text-decoration-none">
                            {{ $functionalUnit->lot->owner->full_name }}
                        </a>
                    @else
                        <span class="text-muted">Sin asignar</span>
                    @endif
                </span>
            </div>
            
            <div class="mb-3">
                <span class="text-muted d-block" style="font-size: 0.75rem;">Inquilino Ocupante</span>
                <span class="fw-semibold">{{ $functionalUnit->lot->tenant ? $functionalUnit->lot->tenant->full_name : 'Sin inquilino' }}</span>
            </div>

            <div class="mb-1">
                <span class="text-muted d-block" style="font-size: 0.75rem;">Domicilio del Lote</span>
                <span class="fw-semibold">{{ $functionalUnit->lot->internal_address ?? 'Sin dirección declarada' }}</span>
            </div>
        </div>

        <!-- Adjustment Form -->
        <div class="ios-card">
            <h6 class="fw-bold mb-4"><i class="bi bi-calculator text-success me-2"></i>Registrar Ajuste Contable</h6>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">Utiliza este formulario para ingresar débitos administrativos (multas, cargos especiales) o créditos manuales.</p>

            <form method="POST" action="{{ route('admin.accounting.adjustment', $functionalUnit) }}">
                @csrf
                
                <div class="mb-3">
                    <label for="type" class="form-label fw-semibold" style="font-size: 0.85rem;">Tipo de Ajuste</label>
                    <select name="type" id="type" class="form-select form-control-ios" required>
                        <option value="debit">Débito (Aumentar deuda / Generar cargo)</option>
                        <option value="credit">Crédito (Descontar saldo / Saldo a favor)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label fw-semibold" style="font-size: 0.85rem;">Importe ($)</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control form-control-ios" required placeholder="Ej: 2500.00">
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold" style="font-size: 0.85rem;">Concepto / Detalle del Ajuste</label>
                    <input type="text" name="description" id="description" class="form-control form-control-ios" required placeholder="Ej. Multa por ruidos molestos acta #24">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-ios btn-ios-primary"><i class="bi bi-check-lg me-1"></i>Aplicar Ajuste</button>
                    <a href="{{ route('admin.accounting.index') }}" class="btn btn-ios btn-ios-secondary btn-sm">Volver</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
