@extends('layouts.app')

@section('title', 'Conciliar Pago #' . $payment->id)
@section('page_title', 'Revisar Pago Reportado')

@section('content')
<!-- Duplicate warning banner -->
@if(isset($potentialDuplicates) && count($potentialDuplicates) > 0)
    <div class="ios-card bg-danger-subtle border-danger text-danger p-3 mb-4 d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
        <div>
            <h6 class="fw-bold m-0">¡Alerta de Posible Pago Duplicado!</h6>
            <span style="font-size: 0.85rem;">
                Se detectaron {{ count($potentialDuplicates) }} otros pagos informados con el mismo número de operación (<strong>{{ $payment->operation_number }}</strong>).
                Por favor, verifica minuciosamente antes de aprobar.
            </span>
        </div>
    </div>
@endif

<div class="row">
    <!-- Payment Details & Receipt (Left) -->
    <div class="col-lg-6 mb-4">
        <div class="ios-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0"><i class="bi bi-info-circle-fill text-success me-2"></i>Detalles del Reporte</h5>
                @if($payment->status === 'pending')
                    <form method="POST" action="{{ route('admin.payments.mark-review', $payment) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-info"><i class="bi bi-exclamation-octagon me-1"></i>Enviar a Revisión</button>
                    </form>
                @endif
            </div>
            
            <table class="table table-borderless align-middle m-0" style="font-size: 0.95rem;">
                <tr>
                    <td class="text-muted py-2" style="width: 35%;">ID Reporte:</td>
                    <td class="fw-semibold py-2">#{{ $payment->id }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Lote / UF:</td>
                    <td class="fw-semibold py-2">
                        @if($payment->lot)
                            <a href="{{ route('admin.lots.history', $payment->lot) }}" class="text-success text-decoration-none fw-bold">
                                Lote {{ $payment->lot->number }}
                            </a>
                        @else
                            <span class="text-danger fw-bold"><i class="bi bi-question-circle me-1"></i>Sin Lote</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Propietario:</td>
                    <td class="fw-semibold py-2">
                        @if($payment->owner)
                            <a href="{{ route('admin.owners.show', $payment->owner) }}" class="text-success text-decoration-none">
                                {{ $payment->owner->full_name }}
                            </a>
                        @else
                            <span class="text-muted">No Identificado</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Importe Declarado:</td>
                    <td class="fw-bold py-2 text-success fs-5">${{ number_format($payment->amount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Fecha del Depósito:</td>
                    <td class="fw-semibold py-2">{{ $payment->payment_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Medio de Pago:</td>
                    <td class="fw-semibold py-2 text-capitalize">{{ $payment->payment_method }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Banco de Destino:</td>
                    <td class="fw-semibold py-2">{{ $payment->bank ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">N° de Operación:</td>
                    <td class="fw-bold py-2 text-primary">{{ $payment->operation_number ?? 'S/N' }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Canal de Origen:</td>
                    <td class="fw-semibold py-2 text-uppercase">{{ $payment->source_channel }}</td>
                </tr>
                @if($payment->notes)
                    <tr>
                        <td class="text-muted py-2" colspan="2">Notas del Propietario/Concepto:</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="bg-body-secondary p-3 rounded-4" style="font-size: 0.9rem; font-family: monospace;">
                            {{ $payment->notes }}
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Receipt (Visual Rendering) -->
        <div class="ios-card">
            <h5 class="fw-bold mb-4"><i class="bi bi-paperclip text-success me-2"></i>Comprobante Adjunto</h5>
            
            @forelse($payment->receipts as $receipt)
                <div class="bg-body-secondary p-3 rounded-4 d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold m-0" style="font-size: 0.9rem;">{{ $receipt->file_name }}</h6>
                        <small class="text-muted">{{ number_format($receipt->file_size / 1024, 1) }} KB</small>
                    </div>
                    <a href="{{ asset('storage/' . $receipt->file_path) }}" target="_blank" class="btn btn-sm btn-ios btn-ios-secondary">
                        <i class="bi bi-box-arrow-up-right"></i> Abrir
                    </a>
                </div>
                
                <div class="border-ios p-2 rounded-4 text-center bg-white" style="max-height: 400px; overflow: hidden;">
                    @if(in_array(strtolower(pathinfo($receipt->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ asset('storage/' . $receipt->file_path) }}" class="img-fluid rounded-3" style="max-height: 380px; object-fit: contain;" alt="Comprobante">
                    @else
                        <div class="py-5 text-muted">
                            <i class="bi bi-file-earmark-pdf fs-1 d-block mb-2 text-danger"></i>
                            <span>Este archivo no es una imagen. Abre el archivo en una pestaña nueva para visualizarlo.</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                    <span>No se adjuntó comprobante físico.</span>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Reconciliation Imputation and Deudas (Right) -->
    <div class="col-lg-6 mb-4">
        @if($payment->status === 'pending' || $payment->status === 'review')
            <div class="ios-card border-warning mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-cpu-fill text-warning me-2"></i>Sugerencia de Matching</h5>
                
                @if(isset($matchingScore))
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <h2 class="fw-bold m-0 {{ $matchingScore >= 95 ? 'text-success' : ($matchingScore >= 50 ? 'text-warning' : 'text-danger') }}">{{ $matchingScore }}%</h2>
                            <small class="text-muted">Nivel de Coincidencia</small>
                        </div>
                        <div>
                            @if($matchingScore >= 95)
                                <span class="badge bg-success-subtle text-success badge-ios"><i class="bi bi-shield-check-fill me-1"></i>Coincidencia Fuerte y Segura</span>
                            @elseif($matchingScore >= 50)
                                <span class="badge bg-warning-subtle text-warning badge-ios"><i class="bi bi-exclamation-triangle-fill me-1"></i>Requiere Revisión Manual</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger badge-ios"><i class="bi bi-x-circle-fill me-1"></i>Sin Coincidencia Clara</span>
                            @endif
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2 text-muted" style="font-size: 0.8rem;">MOTIVOS Y AUDITORÍA DE MATCHING:</h6>
                    <ul class="list-group list-group-flush mb-3" style="font-size: 0.85rem;">
                        @forelse($matchingReasons as $reason)
                            <li class="list-group-item bg-transparent px-0 py-1 border-0 d-flex align-items-center"><i class="bi bi-check2 text-success me-2"></i>{{ $reason }}</li>
                        @empty
                            <li class="list-group-item bg-transparent px-0 py-1 border-0 text-muted">No se pudieron encontrar coincidencias en datos estructurados o conceptos.</li>
                        @endforelse
                    </ul>

                    @if(isset($suggestedDebit))
                        <div class="bg-body-secondary p-3 rounded-4">
                            <h6 class="fw-bold mb-2 text-muted" style="font-size: 0.75rem;">DEUDA SUGERIDA POR EL MOTOR:</h6>
                            <span class="d-block fw-bold">{{ $suggestedDebit->description }}</span>
                            <span class="d-block text-muted" style="font-size: 0.85rem;">Periodo/Lote: Lote {{ $suggestedDebit->functionalUnit->lot->number }} • UF: {{ $suggestedDebit->functionalUnit->code }}</span>
                            <span class="d-block text-success fw-bold mt-1" style="font-size: 0.95rem;">Saldo pendiente: ${{ number_format($suggestedDebit->remaining_amount, 2, ',', '.') }}</span>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Manual Selection and Allocations Form -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-calculator-fill text-success me-2"></i>Imputar Pago Recibido</h5>
                
                <form id="reconciliationForm" method="POST" action="{{ route('admin.payments.reconcile', $payment) }}">
                    @csrf

                    @if(!$payment->functional_unit_id)
                        <!-- Select Unit Manually if Unmatched -->
                        <div class="mb-4">
                            <label for="functional_unit_id" class="form-label fw-bold" style="font-size: 0.85rem;"><i class="bi bi-house-fill me-1"></i>Vincular Lote / Unidad Funcional</label>
                            <select name="functional_unit_id" id="functional_unit_id" class="form-select form-control-ios" required onchange="reloadPageWithUnit(this.value)">
                                <option value="">-- Seleccionar Lote / UF --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>Lote {{ $unit->lot->number }} - UF {{ $unit->code }} ({{ $unit->lot->owner ? $unit->lot->owner->full_name : 'S/P' }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Selecciona la unidad para cargar las deudas pendientes.</small>
                        </div>
                    @else
                        <input type="hidden" name="payment_functional_unit_id" value="{{ $payment->functional_unit_id }}">
                    @endif

                    @if($payment->functional_unit_id || request('unit_id'))
                        <div class="mb-4">
                            <h6 class="fw-bold text-muted mb-3" style="font-size: 0.85rem;">DEUDAS PENDIENTES DEL LOTE:</h6>
                            
                            @forelse($debits as $debit)
                                <div class="bg-body-secondary p-3 rounded-4 mb-3 border border-ios">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="fw-bold d-block">{{ $debit->description }}</span>
                                            <small class="text-muted">Fecha: {{ $debit->date->format('d/m/Y') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="text-muted d-block" style="font-size: 0.8rem;">Pendiente:</span>
                                            <span class="fw-bold text-danger">${{ number_format($debit->remaining_amount, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-7">
                                            <div class="form-check">
                                                <input class="form-check-input debit-checkbox" type="checkbox" id="check_{{ $debit->id }}" data-debit-id="{{ $debit->id }}" data-remaining="{{ $debit->remaining_amount }}" onchange="toggleDebit(this)">
                                                <label class="form-check-label text-muted" for="check_{{ $debit->id }}" style="font-size: 0.85rem;">Imputar esta deuda</label>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="allocations[{{ $debit->id }}]" id="alloc_{{ $debit->id }}" class="form-control form-control-ios allocation-input" step="0.01" min="0.01" max="{{ $debit->remaining_amount }}" disabled oninput="calculateExcedente()">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted bg-body-secondary rounded-4">
                                    <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                                    <span>Este lote no tiene deudas pendientes en este momento.</span>
                                </div>
                            @endforelse
                        </div>

                        <!-- Calculations and Surplus -->
                        <div class="bg-light p-3 rounded-4 mb-4 border border-ios" style="font-size: 0.9rem;">
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Total del Pago:</span>
                                <span class="fw-bold">${{ number_format($payment->amount, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Total Imputado:</span>
                                <span class="fw-bold text-success" id="totalImputadoLabel">$0,00</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-top mt-2 pt-2">
                                <span class="fw-bold" id="excedenteTitle">Excedente / Saldo a Favor:</span>
                                <span class="fw-bold text-primary" id="excedenteLabel">${{ number_format($payment->amount, 2, ',', '.') }}</span>
                            </div>
                            <small class="text-muted d-block mt-2" id="surplusNote"><i class="bi bi-info-circle me-1"></i>El excedente se acreditará automáticamente como saldo a favor en la cuenta corriente de la unidad.</small>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold" style="font-size: 0.85rem;">Observaciones de Conciliación (Interno)</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control form-control-ios" placeholder="Mensaje administrativo para documentar la conciliación..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" id="submitBtn" class="btn btn-ios btn-ios-primary btn-success" {{ ($payment->functional_unit_id || request('unit_id')) ? '' : 'disabled' }}>Confirmar Conciliación</button>
                    </div>
                </form>

                <!-- Reject form -->
                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="mt-3">
                    @csrf
                    <div class="border-top pt-3 mt-3">
                        <h6 class="fw-bold text-danger mb-3"><i class="bi bi-x-circle-fill me-1"></i>Rechazar Pago</h6>
                        <div class="mb-3">
                            <label for="reject_notes" class="form-label fw-semibold" style="font-size: 0.85rem;">Observaciones de Rechazo (Propietario)</label>
                            <textarea name="notes" id="reject_notes" rows="2" class="form-control form-control-ios" required placeholder="Ingresa el motivo del rechazo. El propietario podrá leer esto..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-ios btn-ios-secondary text-danger w-100"><i class="bi bi-trash-fill me-1"></i>Rechazar Reporte de Pago</button>
                    </div>
                </form>
            </div>
        @else
            <!-- Reconciled Detail view with Reversion option -->
            <div class="ios-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0"><i class="bi bi-shield-lock-fill text-success me-2"></i>Estado de Conciliación</h5>
                    @if($payment->status === 'approved')
                        <span class="badge bg-success-subtle text-success badge-ios">Conciliado</span>
                    @elseif($payment->status === 'rejected')
                        <span class="badge bg-danger-subtle text-danger badge-ios">Rechazado</span>
                    @endif
                </div>

                <div class="bg-body-secondary p-3 rounded-4 mb-4">
                    <table class="table table-borderless align-middle m-0" style="font-size: 0.9rem;">
                        <tr>
                            <td class="text-muted py-1" style="width: 40%;">Conciliado por:</td>
                            <td class="fw-semibold py-1">{{ $payment->user ? $payment->user->name . ' ' . $payment->user->last_name : 'Sistema (Automático)' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Fecha / Hora:</td>
                            <td class="fw-semibold py-1">{{ $payment->reconciled_at ? $payment->reconciled_at->format('d/m/Y H:i') : $payment->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Método:</td>
                            <td class="fw-semibold py-1 text-uppercase">{{ $payment->reconciliation_method ?? 'N/A' }}</td>
                        </tr>
                        @if($payment->notes)
                            <tr>
                                <td class="text-muted py-1">Observaciones:</td>
                                <td class="fw-semibold py-1">{{ $payment->notes }}</td>
                            </tr>
                        @endif
                    </table>
                </div>

                @if($payment->status === 'approved')
                    <h6 class="fw-bold mb-3"><i class="bi bi-list-check me-1"></i>Desglose de Imputaciones</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-weight: 600;">CONCEPTO</th>
                                    <th class="text-muted text-end" style="font-weight: 600;">IMPORTE</th>
                                    <th class="text-muted" style="font-weight: 600;">ESTADO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allocations as $alloc)
                                    <tr class="border-bottom border-ios">
                                        <td>
                                            <span class="d-block fw-bold">{{ $alloc->accountMovement ? $alloc->accountMovement->description : 'Imputación de cuenta corriente' }}</span>
                                            <small class="text-muted">Deuda original: ${{ number_format($alloc->previous_balance, 2, ',', '.') }} • Posterior: ${{ number_format($alloc->posterior_balance, 2, ',', '.') }}</small>
                                        </td>
                                        <td class="text-end fw-bold text-success">${{ number_format($alloc->allocated_amount, 2, ',', '.') }}</td>
                                        <td>
                                            @if($alloc->status === 'active')
                                                <span class="badge bg-success-subtle text-success badge-ios">Activo</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger badge-ios" title="Revertido por: {{ $alloc->revertedBy ? $alloc->revertedBy->full_name : 'Admin' }}">Revertido</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">No se registran imputaciones individuales (acreditado completo como saldo general).</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Reversion Section -->
                    <div class="border-top pt-4 mt-4">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-arrow-counterclockwise me-1"></i>Deshacer Conciliación</h6>
                        <p class="text-muted" style="font-size: 0.85rem;">Si este pago se concilió por error o se imputó a deudas incorrectas, puedes revertir la acción de manera segura. Se anularán las imputaciones y se generará un contra-asiento contable restaurando el saldo de las deudas sin eliminar el historial de auditoría.</p>
                        
                        <button type="button" class="btn btn-ios btn-ios-secondary text-danger w-100" data-bs-toggle="modal" data-bs-target="#revertModal">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Deshacer e Imputar Nuevamente
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Reversion Modal -->
<div class="modal fade" id="revertModal" tabindex="-1" aria-labelledby="revertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-ios py-3">
                <h5 class="modal-title fw-bold text-danger" id="revertModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Deshacer Conciliación</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.payments.revert', $payment) }}">
                @csrf
                <div class="modal-body py-4">
                    <p class="text-muted" style="font-size: 0.9rem;">Por favor, ingresa el motivo por el cual estás deshaciendo esta conciliación. Esto quedará registrado para siempre en los logs de auditoría.</p>
                    <div class="mb-3">
                        <label for="reversion_reason" class="form-label fw-semibold" style="font-size: 0.85rem;">Motivo de la Reversión</label>
                        <textarea name="reversion_reason" id="reversion_reason" rows="3" class="form-control form-control-ios" required placeholder="Ej: Error al seleccionar el lote. El pago pertenece al lote 42..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-ios btn-ios-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-ios btn-ios-primary btn-danger">Confirmar Reversión</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script to handle calculation of allocations and manual redirection -->
<script>
const paymentAmount = {{ $payment->amount }};

function reloadPageWithUnit(unitId) {
    if (unitId) {
        window.location.search = '?unit_id=' + unitId;
    } else {
        window.location.search = '';
    }
}

function toggleDebit(chk) {
    const debitId = chk.getAttribute('data-debit-id');
    const remaining = parseFloat(chk.getAttribute('data-remaining'));
    const input = document.getElementById('alloc_' + debitId);

    if (chk.checked) {
        input.disabled = false;
        // Pre-fill with the best fit
        let currentAllocated = 0.00;
        document.querySelectorAll('.allocation-input').forEach(inp => {
            if (!inp.disabled && inp.id !== 'alloc_' + debitId) {
                currentAllocated += parseFloat(inp.value || 0);
            }
        });
        const remainingPayment = paymentAmount - currentAllocated;
        input.value = Math.max(0, Math.min(remaining, remainingPayment)).toFixed(2);
    } else {
        input.disabled = true;
        input.value = '';
    }
    calculateExcedente();
}

function calculateExcedente() {
    let totalAllocated = 0.00;
    document.querySelectorAll('.allocation-input').forEach(inp => {
        if (!inp.disabled) {
            totalAllocated += parseFloat(inp.value || 0);
        }
    });

    const labelTotal = document.getElementById('totalImputadoLabel');
    const labelExcedente = document.getElementById('excedenteLabel');
    const titleExcedente = document.getElementById('excedenteTitle');
    const noteSurplus = document.getElementById('surplusNote');
    const submitBtn = document.getElementById('submitBtn');

    labelTotal.innerText = '$' + totalAllocated.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    const excedente = paymentAmount - totalAllocated;
    
    // Formatting and validation
    if (excedente < -0.005) {
        // Validation error
        labelExcedente.innerText = '-$' + Math.abs(excedente).toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        labelExcedente.className = "fw-bold text-danger";
        titleExcedente.innerText = "Error: Monto Excedido:";
        submitBtn.disabled = true;
        noteSurplus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>El monto imputado supera el importe del pago. Corrige las cantidades.</span>';
    } else {
        labelExcedente.innerText = '$' + excedente.toLocaleString('es-AR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        labelExcedente.className = "fw-bold text-primary";
        titleExcedente.innerText = "Excedente / Saldo a Favor:";
        submitBtn.disabled = false;
        noteSurplus.innerHTML = '<i class="bi bi-info-circle me-1"></i>El excedente se acreditará automáticamente como saldo a favor en la cuenta corriente de la unidad.';
    }
}

// Pre-fill suggested match on load if present
window.addEventListener('DOMContentLoaded', () => {
    @if(isset($suggestedDebit) && count($debits) > 0)
        const suggestedId = {{ $suggestedDebit->id }};
        const chk = document.getElementById('check_' + suggestedId);
        if (chk) {
            chk.checked = true;
            toggleDebit(chk);
        }
    @endif
});
</script>
@endsection
