@extends('layouts.app')

@section('title', 'Confirmar Conciliación Masiva')
@section('page_title', 'Simulación de Conciliación Automática')

@section('content')
<div class="ios-card mb-4 bg-light">
    <h5 class="fw-bold mb-3"><i class="bi bi-magic text-success me-2"></i>Resultado de la Simulación</h5>
    <p class="text-muted m-0" style="font-size: 0.95rem;">
        Se escanearon todos los pagos pendientes de conciliación utilizando nuestro motor de matching de confianza estructurada (Score >= 95%, sin ambigüedad y con identificadores verificados).
        A continuación puedes ver cuáles son seguros para conciliar automáticamente y cuáles requieren tu revisión.
    </p>
</div>

<form method="POST" action="{{ route('admin.payments.auto-reconcile.apply') }}">
    @csrf

    <!-- Auto Reconcilable Panel -->
    <div class="ios-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-ios pb-3">
            <h5 class="fw-bold text-success m-0">
                <i class="bi bi-shield-check-fill me-2"></i>Coincidencias Fuertes para Conciliar ({{ count($simulation['auto_reconcilable']) }})
            </h5>
            <div>
                <button type="button" class="btn btn-sm btn-ios btn-ios-secondary" onclick="toggleAllCheckboxes(true)">Seleccionar Todos</button>
                <button type="button" class="btn btn-sm btn-ios btn-ios-secondary" onclick="toggleAllCheckboxes(false)">Deseleccionar Todos</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="border-bottom border-ios" style="font-size: 0.85rem;">
                        <th style="width: 5%;"></th>
                        <th class="text-muted" style="font-weight: 600;">FECHA</th>
                        <th class="text-muted" style="font-weight: 600;">LOTE / UF</th>
                        <th class="text-muted" style="font-weight: 600;">PROPIETARIO</th>
                        <th class="text-muted text-end" style="font-weight: 600;">IMPORTE</th>
                        <th class="text-muted text-center" style="font-weight: 600;">SCORE</th>
                        <th class="text-muted" style="font-weight: 600;">DEUDA SUGERIDA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($simulation['auto_reconcilable'] as $pay)
                        <tr class="border-bottom border-ios">
                            <td class="text-center">
                                <input type="checkbox" name="payment_ids[]" value="{{ $pay->id }}" class="form-check-input payment-chk" checked onchange="updateSubmitButton()">
                            </td>
                            <td style="font-size: 0.85rem;">{{ $pay->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold">Lote {{ $pay->lot->number }}</td>
                            <td>{{ $pay->owner->full_name }}</td>
                            <td class="text-end fw-bold text-success">${{ number_format($pay->amount, 2, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success badge-ios">{{ $pay->suggested_score }}%</span>
                            </td>
                            <td style="font-size: 0.85rem;">
                                @if($pay->suggested_debit)
                                    <span class="fw-bold">{{ $pay->suggested_debit->description }}</span>
                                    <span class="text-muted d-block" style="font-size: 0.75rem;">Saldo pendiente: ${{ number_format($pay->suggested_debit->remaining_amount, 2, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">Crédito libre (Saldo a Favor)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>
                                <span>No hay coincidencias automáticas de confianza extrema en este lote de pagos.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Requires Review Panel -->
    <div class="ios-card mb-4">
        <h5 class="fw-bold text-warning mb-4 border-bottom border-ios pb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Pagos que Requieren Revisión Manual ({{ count($simulation['requires_review']) }})
        </h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="border-bottom border-ios" style="font-size: 0.85rem;">
                        <th class="text-muted" style="font-weight: 600;">FECHA</th>
                        <th class="text-muted" style="font-weight: 600;">LOTE / UF</th>
                        <th class="text-muted" style="font-weight: 600;">PROPIETARIO</th>
                        <th class="text-muted text-end" style="font-weight: 600;">IMPORTE</th>
                        <th class="text-muted text-center" style="font-weight: 600;">MEJOR SCORE</th>
                        <th class="text-muted" style="font-weight: 600;">MOTIVO DE REVISIÓN</th>
                        <th class="text-muted text-end" style="font-weight: 600; width: 10%;">REVISAR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($simulation['requires_review'] as $pay)
                        <tr class="border-bottom border-ios">
                            <td style="font-size: 0.85rem;">{{ $pay->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold">
                                @if($pay->lot)
                                    Lote {{ $pay->lot->number }}
                                @else
                                    <span class="text-danger">Sin Identificar</span>
                                @endif
                            </td>
                            <td>{{ $pay->owner ? $pay->owner->full_name : 'No identificado' }}</td>
                            <td class="text-end fw-bold text-muted">${{ number_format($pay->amount, 2, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $pay->suggested_score >= 50 ? 'warning' : 'danger' }}-subtle text-{{ $pay->suggested_score >= 50 ? 'warning' : 'danger' }} badge-ios">{{ $pay->suggested_score }}%</span>
                            </td>
                            <td style="font-size: 0.85rem;" class="text-muted">
                                @if(!$pay->functional_unit_id)
                                    Falta identificar lote/propietario.
                                @elseif($pay->suggested_score < 95)
                                    Puntaje insuficiente ({{ $pay->suggested_score }}% < 95%).
                                @else
                                    Ambigüedad detectada con otras deudas.
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.payments.show', $pay) }}" target="_blank" class="btn btn-sm btn-ios btn-ios-secondary">
                                    <i class="bi bi-shield-check"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Todos los pagos pendientes fueron clasificados como seguros de conciliar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions Footer -->
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Cancelar y Volver</a>
        <button type="submit" id="submitAutoBtn" class="btn btn-ios btn-ios-primary btn-success" {{ count($simulation['auto_reconcilable']) > 0 ? '' : 'disabled' }}>
            <i class="bi bi-check-circle-fill me-1"></i> Aplicar Conciliaciones Seleccionadas
        </button>
    </div>
</form>

<script>
function toggleAllCheckboxes(val) {
    document.querySelectorAll('.payment-chk').forEach(chk => {
        chk.checked = val;
    });
    updateSubmitButton();
}

function updateSubmitButton() {
    const selectedCount = document.querySelectorAll('.payment-chk:checked').length;
    const btn = document.getElementById('submitAutoBtn');
    if (selectedCount > 0) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Aplicar Conciliaciones Seleccionadas (' + selectedCount + ')';
    } else {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Aplicar Conciliaciones Seleccionadas';
    }
}
</script>
@endsection
