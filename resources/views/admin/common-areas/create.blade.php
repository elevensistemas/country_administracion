@extends('layouts.app')

@section('title', 'Nueva Zona Común')
@section('page_title', 'Registrar Zona Común')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0"><i class="bi bi-plus-circle text-success me-2"></i>Nueva Zona Común</h5>
                <a href="{{ route('admin.common-areas.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Volver</a>
            </div>

            <form method="POST" action="{{ route('admin.common-areas.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-12">
                        <label for="name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre del Espacio</label>
                        <input type="text" name="name" id="name" class="form-control form-control-ios" placeholder="Ej: SUM, Quincho, Cancha de Tenis 1" value="{{ old('name') }}" required>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold" style="font-size: 0.85rem;">Descripción</label>
                        <textarea name="description" id="description" rows="3" class="form-control form-control-ios" placeholder="Información de ubicación, equipamiento..." style="border-radius: 12px;">{{ old('description') }}</textarea>
                    </div>

                    <!-- Capacity & Price -->
                    <div class="col-md-6">
                        <label for="capacity" class="form-label fw-semibold" style="font-size: 0.85rem;">Capacidad Máxima (personas)</label>
                        <input type="number" name="capacity" id="capacity" class="form-control form-control-ios" min="1" value="{{ old('capacity', 20) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label fw-semibold" style="font-size: 0.85rem;">Precio del Alquiler ($)</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control form-control-ios" min="0" value="{{ old('price', 0.00) }}" required>
                    </div>

                    <!-- Schedule Start & End -->
                    <div class="col-md-6">
                        <label for="schedule_start" class="form-label fw-semibold" style="font-size: 0.85rem;">Horario Apertura</label>
                        <input type="time" name="schedule_start" id="schedule_start" class="form-control form-control-ios" value="{{ old('schedule_start', '08:00') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="schedule_end" class="form-label fw-semibold" style="font-size: 0.85rem;">Horario Cierre</label>
                        <input type="time" name="schedule_end" id="schedule_end" class="form-control form-control-ios" value="{{ old('schedule_end', '22:00') }}" required>
                    </div>

                    <!-- Duration & Photos -->
                    <div class="col-md-6">
                        <label for="duration_minutes" class="form-label fw-semibold" style="font-size: 0.85rem;">Duración Bloque de Reserva (minutos)</label>
                        <select name="duration_minutes" id="duration_minutes" class="form-select form-control-ios" required>
                            <option value="60" {{ old('duration_minutes') == 60 ? 'selected' : '' }}>60 minutos (1 hora)</option>
                            <option value="120" {{ old('duration_minutes', 120) == 120 ? 'selected' : '' }}>120 minutos (2 horas)</option>
                            <option value="180" {{ old('duration_minutes') == 180 ? 'selected' : '' }}>180 minutos (3 horas)</option>
                            <option value="240" {{ old('duration_minutes') == 240 ? 'selected' : '' }}>240 minutos (4 horas)</option>
                            <option value="360" {{ old('duration_minutes') == 360 ? 'selected' : '' }}>360 minutos (6 horas)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="font-size: 0.85rem;">Fotografías (múltiples)</label>
                        <input type="file" name="photos[]" multiple class="form-control form-control-ios" accept="image/*">
                    </div>

                    <!-- Maintenance Blocked Days -->
                    <div class="col-12 mt-3">
                        <label class="form-label fw-semibold" style="font-size: 0.85rem;">Días no disponibles para reservar (Mantenimiento/Cierre)</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach([
                                'Monday' => 'Lunes',
                                'Tuesday' => 'Martes',
                                'Wednesday' => 'Miércoles',
                                'Thursday' => 'Jueves',
                                'Friday' => 'Viernes',
                                'Saturday' => 'Sábado',
                                'Sunday' => 'Domingo'
                            ] as $engDay => $espDay)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="maintenance_blocked_days[]" value="{{ $engDay }}" id="day_{{ $engDay }}" {{ is_array(old('maintenance_blocked_days')) && in_array($engDay, old('maintenance_blocked_days')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="day_{{ $engDay }}">
                                        {{ $espDay }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-1">Los días seleccionados se bloquearán para reservas automáticas de los propietarios.</small>
                    </div>

                    <!-- Requires Approval Checkbox -->
                    <div class="col-12 mt-3">
                        <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center">
                            <label class="form-check-label fw-bold" for="requires_approval">¿Requiere aprobación administrativa?</label>
                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="requires_approval" name="requires_approval" style="width: 45px; height: 24px;">
                        </div>
                        <small class="text-muted d-block mt-1">Si está activado, la reserva quedará pendiente hasta que la administración la confirme.</small>
                    </div>

                    <!-- Rules -->
                    <div class="col-12 mt-3">
                        <label for="rules" class="form-label fw-semibold" style="font-size: 0.85rem;">Reglamento / Normas del Espacio</label>
                        <textarea name="rules" id="rules" rows="4" class="form-control form-control-ios" placeholder="Reglas de limpieza, ruidos molestos, penalizaciones..." style="border-radius: 12px;">{{ old('rules') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.common-areas.index') }}" class="btn btn-ios btn-ios-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-ios btn-ios-primary px-4">Crear Zona Común</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
