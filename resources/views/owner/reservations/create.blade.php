@extends('layouts.owner')

@section('title', 'Reservar Espacio')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-12">
        <!-- Header -->
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold m-0 text-success"><i class="bi bi-calendar-plus me-2"></i>Nueva Reserva</h4>
                <p class="text-muted m-0" style="font-size: 0.85rem;">Reservando: <strong>{{ $commonArea->name }}</strong></p>
            </div>
            <a href="{{ route('owner.reservations.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Volver</a>
        </div>

        <form method="POST" action="{{ route('owner.reservations.store') }}">
            @csrf
            <input type="hidden" name="common_area_id" value="{{ $commonArea->id }}">

            <!-- STEP 1: SELECT DATE -->
            <div class="ios-card">
                <h6 class="fw-bold mb-3 text-success"><i class="bi bi-1-circle-fill me-1"></i> Paso 1: Selecciona la Fecha</h6>
                
                <div class="mb-2">
                    <label for="date_picker" class="form-label text-muted" style="font-size: 0.8rem;">Elige el día de tu evento:</label>
                    <input type="date" id="date_picker" class="form-control form-control-ios" value="{{ $selectedDate->toDateString() }}" min="{{ date('Y-m-d') }}" onchange="changeDate(this.value)">
                    <input type="hidden" name="reservation_date" value="{{ $selectedDate->toDateString() }}">
                </div>

                @if($isMaintenanceDay)
                    <div class="alert alert-warning border-0 rounded-4 mt-3 mb-0 d-flex align-items-center" style="font-size: 0.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>El espacio se encuentra cerrado por mantenimiento los días <strong>{{ $selectedDate->isoFormat('dddd') }}</strong>. Por favor selecciona otra fecha.</div>
                    </div>
                @endif
            </div>

            <!-- STEP 2: CHOOSE TIME RANGE -->
            @if(!$isMaintenanceDay)
                <div class="ios-card">
                    <h6 class="fw-bold mb-3 text-success"><i class="bi bi-2-circle-fill me-1"></i> Paso 2: Elige el Horario</h6>
                    <p class="text-muted mb-3" style="font-size: 0.8rem; line-height: 1.4;">
                        El espacio está abierto desde las <strong>{{ substr($commonArea->schedule_start, 0, 5) }} hs</strong> hasta las <strong>{{ substr($commonArea->schedule_end, 0, 5) }} hs</strong>.
                    </p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="start_time" class="form-label text-muted fw-semibold" style="font-size: 0.8rem;">Hora de Entrada:</label>
                            <input type="time" name="start_time" id="start_time" class="form-control form-control-ios" required min="{{ substr($commonArea->schedule_start, 0, 5) }}" max="{{ substr($commonArea->schedule_end, 0, 5) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label text-muted fw-semibold" style="font-size: 0.8rem;">Hora de Salida:</label>
                            <input type="time" name="end_time" id="end_time" class="form-control form-control-ios" required min="{{ substr($commonArea->schedule_start, 0, 5) }}" max="{{ substr($commonArea->schedule_end, 0, 5) }}">
                        </div>
                    </div>

                    @if(count($existingBookings) > 0)
                        <div class="alert alert-info border-0 rounded-4 mt-3 mb-0" style="font-size: 0.82rem;">
                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem;"><i class="bi bi-info-circle-fill me-1"></i> Horarios ya reservados hoy:</h6>
                            <ul class="m-0 ps-3">
                                @foreach($existingBookings as $booking)
                                    <li>De <strong>{{ substr($booking->start_time, 0, 5) }}</strong> a <strong>{{ substr($booking->end_time, 0, 5) }}</strong> hs (Lote {{ $booking->lot->number }})</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-success border-0 rounded-4 mt-3 mb-0" style="font-size: 0.82rem;">
                            <i class="bi bi-check-circle-fill me-1"></i> Este espacio no registra reservas para esta fecha. ¡Toda la franja está disponible!
                        </div>
                    @endif
                </div>

                <!-- STEP 3: CONFIRMATION, EXCLUSIVITY & RULES -->
                <div class="ios-card">
                    <h6 class="fw-bold mb-3 text-success"><i class="bi bi-3-circle-fill me-1"></i> Paso 3: Tipo de Reserva y Costo</h6>
                    
                    <!-- Exclusivity Option -->
                    <div class="p-3 bg-body-tertiary rounded-4 mb-3 border border-ios">
                        <label class="form-label fw-bold text-success" style="font-size: 0.85rem;"><i class="bi bi-star-fill me-1"></i> Tipo de Reserva / Exclusividad</label>
                        <div class="d-flex gap-4 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_exclusive" id="is_exclusive_no" value="0" checked onchange="toggleExclusivity(false)">
                                <label class="form-check-label fw-semibold" for="is_exclusive_no" style="font-size: 0.85rem;">
                                    Sin Exclusividad (Precio base)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="is_exclusive" id="is_exclusive_yes" value="1" onchange="toggleExclusivity(true)">
                                <label class="form-check-label fw-semibold text-danger" for="is_exclusive_yes" style="font-size: 0.85rem;">
                                    Con Exclusividad (Requiere Presupuesto)
                                </label>
                            </div>
                        </div>
                        <div id="excl_note" class="text-muted mt-2 d-none" style="font-size: 0.78rem;">
                            <i class="bi bi-info-circle-fill text-danger me-1"></i>
                            <strong>Nota:</strong> Al reservar con exclusividad, el costo final será cotizado por la administración. La reserva se enviará como pendiente y te notificaremos el valor presupuestado.
                        </div>
                    </div>

                    <!-- Cost Box -->
                    <div class="p-3 bg-body-tertiary rounded-4 mb-3 border border-ios d-flex justify-content-between align-items-center" id="cost_box">
                        <div>
                            <span class="text-muted d-block" style="font-size: 0.8rem;" id="cost_label_title">Costo de Alquiler:</span>
                            <strong class="text-dark fs-5" id="cost_amount">{{ $commonArea->price > 0 ? '$' . number_format($commonArea->price, 2, ',', '.') : 'Gratuito' }}</strong>
                        </div>
                        @if($commonArea->price > 0)
                            <div class="form-check form-switch p-0" id="expenses_switch_container">
                                <label class="form-check-label fw-bold d-block text-end me-5" for="charge_to_expenses" style="font-size: 0.8rem;">Cargar a mi Expensa</label>
                                <input class="form-check-input ms-0 float-end" type="checkbox" role="switch" id="charge_to_expenses" name="charge_to_expenses" value="1" checked style="width: 45px; height: 22px;">
                            </div>
                        @endif
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label text-muted" style="font-size: 0.8rem;">Comentarios adicionales (Opcional):</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control form-control-ios" placeholder="Aclaraciones para la administración..."></textarea>
                    </div>

                    <!-- Rules Regulations Warning -->
                    @if($commonArea->rules)
                        <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 mb-3" style="max-height: 150px; overflow-y: auto;">
                            <h6 class="fw-bold text-warning mb-1" style="font-size: 0.8rem;"><i class="bi bi-journal-text me-1"></i> Reglamento del Espacio</h6>
                            <p class="text-muted m-0" style="font-size: 0.78rem; line-height: 1.4; white-space: pre-wrap;">{{ $commonArea->rules }}</p>
                        </div>
                    @endif

                    <!-- Accept Rules checkbox -->
                    <div class="form-check d-flex align-items-start gap-2">
                        <input class="form-check-input mt-1" type="checkbox" value="1" name="accept_rules" id="accept_rules" required>
                        <label class="form-check-label text-muted" for="accept_rules" style="font-size: 0.82rem; line-height: 1.3;">
                            Acepto el reglamento general de uso, comprometiéndome a entregar el espacio en las mismas condiciones higiénicas y de conservación que me fue entregado.
                        </label>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <a href="{{ route('owner.reservations.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-1"></i> Cancelar</a>
                    <button type="submit" class="btn btn-ios btn-ios-primary px-4">Confirmar Reserva</button>
                </div>
            @endif
        </form>
    </div>
</div>

<script>
    const basePriceFormatted = "{{ $commonArea->price > 0 ? '$' . number_format($commonArea->price, 2, ',', '.') : 'Gratuito' }}";

    function changeDate(newDate) {
        window.location.href = "?date=" + newDate;
    }

    function toggleExclusivity(isExclusive) {
        const exclNote = document.getElementById('excl_note');
        const costAmount = document.getElementById('cost_amount');
        const costTitle = document.getElementById('cost_label_title');
        const swContainer = document.getElementById('expenses_switch_container');

        if (isExclusive) {
            exclNote.classList.remove('d-none');
            costAmount.innerText = 'A Presupuestar';
            costAmount.className = 'text-danger fs-5';
            costTitle.innerText = 'Presupuesto:';
            if (swContainer) {
                swContainer.classList.add('d-none');
            }
        } else {
            exclNote.classList.add('d-none');
            costAmount.innerText = basePriceFormatted;
            costAmount.className = 'text-dark fs-5';
            costTitle.innerText = 'Costo de Alquiler:';
            if (swContainer) {
                swContainer.classList.remove('d-none');
            }
        }
    }
</script>
@endsection
