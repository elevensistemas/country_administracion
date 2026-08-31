@extends('layouts.owner')

@section('title', 'Nueva Autorización')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('owner.guests.store') }}">
            @csrf

            <!-- Guest Core Details -->
            <div class="ios-card bg-body-tertiary p-4 mb-4">
                <h5 class="fw-bold mb-4 text-success"><i class="bi bi-person-plus-fill me-2"></i>Nueva Autorización de Ingreso</h5>
                
                <div class="row g-3">
                    <!-- Lot Info (Read-only representation) -->
                    <div class="col-12 mb-2">
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 font-monospace" style="font-size: 0.85rem;">
                            Lote Autorizante: {{ $activeLot ? ($activeLot->name ?? 'Lote ' . $activeLot->number) : 'Sin Lote' }}
                        </span>
                    </div>

                    <!-- Type of Authorization -->
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="font-size: 0.85rem;">Tipo de Invitación</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="type" id="type-individual" value="individual" autocomplete="off" checked>
                            <label class="btn btn-outline-success flex-fill rounded-3" for="type-individual">
                                <i class="bi bi-person me-1"></i> Individual
                            </label>

                            <input type="radio" class="btn-check" name="type" id="type-frequent" value="frequent" autocomplete="off">
                            <label class="btn btn-outline-success flex-fill rounded-3" for="type-frequent">
                                <i class="bi bi-calendar-event me-1"></i> Frecuente
                            </label>

                            <input type="radio" class="btn-check" name="type" id="type-list" value="list" autocomplete="off">
                            <label class="btn btn-outline-success flex-fill rounded-3" for="type-list">
                                <i class="bi bi-list-ul me-1"></i> Lista (Eventos)
                            </label>
                        </div>
                        @error('type')
                            <span class="text-danger d-block mt-1" style="font-size: 0.8rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Individual / Frequent Guest Fields (Toggled) -->
                    <div id="fields-individual" class="row g-3 m-0 p-0 col-12">
                        <div class="col-md-6 mt-0">
                            <label for="name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej: Juan">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mt-0">
                            <label for="last_name" class="form-label fw-semibold" style="font-size: 0.85rem;">Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="last_name" class="form-control rounded-3 @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Ej: Pérez">
                            @error('last_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="dni" class="form-label fw-semibold" style="font-size: 0.85rem;">DNI <span class="text-danger">*</span></label>
                            <input type="text" name="dni" id="dni" class="form-control rounded-3 @error('dni') is-invalid @enderror" value="{{ old('dni') }}" placeholder="Ej: 12345678">
                            @error('dni')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- List Guest Fields (Toggled) -->
                    <div id="fields-list" class="row g-3 m-0 p-0 col-12 d-none">
                        <div class="col-12 mt-0">
                            <label for="guest_names_list" class="form-label fw-semibold" style="font-size: 0.85rem;">Listado de Invitados (Nombre, Apellido y DNI por línea) <span class="text-danger">*</span></label>
                            <textarea name="guest_names_list" id="guest_names_list" rows="5" class="form-control rounded-3 @error('guest_names_list') is-invalid @enderror" placeholder="Ej:&#10;Juan Pérez, 12345678&#10;María Gómez, 87654321">{{ old('guest_names_list') }}</textarea>
                            @error('guest_names_list')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Common Access Fields (Always Visible) -->
                    <div class="col-md-6">
                        <label for="visit_date" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha de Visita / Evento <span class="text-danger">*</span></label>
                        <input type="date" name="visit_date" id="visit_date" class="form-control rounded-3 @error('visit_date') is-invalid @enderror" value="{{ old('visit_date', date('Y-m-d')) }}" required>
                        @error('visit_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="visit_time" class="form-label fw-semibold" style="font-size: 0.85rem;">Hora Estimada (Opcional)</label>
                        <input type="time" name="visit_time" id="visit_time" class="form-control rounded-3 @error('visit_time') is-invalid @enderror" value="{{ old('visit_time') }}">
                        @error('visit_time')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="license_plate" class="form-label fw-semibold" style="font-size: 0.85rem;">Patente del Vehículo (Opcional)</label>
                        <input type="text" name="license_plate" id="license_plate" class="form-control rounded-3 @error('license_plate') is-invalid @enderror" value="{{ old('license_plate') }}" placeholder="Ej: AA123BB o ABC123">
                        @error('license_plate')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label fw-semibold" style="font-size: 0.85rem;">Notas / Indicaciones para la Garita</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control rounded-3 @error('notes') is-invalid @enderror" placeholder="Escribe aquí indicaciones opcionales, ej: 'Es jardinero, viene a trabajar', 'Familiar directo'...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Warning about Guardhouse connection -->
            <div class="alert alert-info border-0 rounded-4 shadow-sm p-3 mb-4" role="alert">
                <div class="d-flex">
                    <i class="bi bi-info-circle-fill fs-5 me-2 text-info flex-shrink-0"></i>
                    <div>
                        <strong class="d-block" style="font-size: 0.88rem;">Conectado con Garita de Acceso</strong>
                        <span style="font-size: 0.8rem; line-height: 1.3;">Esta invitación se enviará en tiempo real al sistema local de la garita. Cuando el invitado llegue, la guardia podrá verificar sus datos de DNI y otorgar acceso rápido.</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('owner.guests.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4">Autorizar Acceso</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeIndividual = document.getElementById('type-individual');
        const typeFrequent = document.getElementById('type-frequent');
        const typeList = document.getElementById('type-list');
        
        const fieldsIndividual = document.getElementById('fields-individual');
        const fieldsList = document.getElementById('fields-list');

        const nameInput = document.getElementById('name');
        const lastNameInput = document.getElementById('last_name');
        const dniInput = document.getElementById('dni');
        const listTextarea = document.getElementById('guest_names_list');

        function toggleFields() {
            if (typeList.checked) {
                fieldsIndividual.classList.add('d-none');
                fieldsList.classList.remove('d-none');
                
                // Clear validation attributes for individual
                nameInput.removeAttribute('required');
                lastNameInput.removeAttribute('required');
                dniInput.removeAttribute('required');
                listTextarea.setAttribute('required', 'required');
            } else {
                fieldsIndividual.classList.remove('d-none');
                fieldsList.classList.add('d-none');
                
                nameInput.setAttribute('required', 'required');
                lastNameInput.setAttribute('required', 'required');
                dniInput.setAttribute('required', 'required');
                listTextarea.removeAttribute('required');
            }
        }

        typeIndividual.addEventListener('change', toggleFields);
        typeFrequent.addEventListener('change', toggleFields);
        typeList.addEventListener('change', toggleFields);

        // Initialize state
        toggleFields();
    });
</script>
@endsection
