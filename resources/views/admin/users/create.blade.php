@extends('layouts.app')

@section('title', 'Nuevo Usuario')
@section('page_title', 'Crear Nuevo Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <!-- Block 1: Personal Data -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-person-fill text-success me-2"></i>Datos Personales</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control form-control-ios @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Ej. Juan">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label fw-semibold" style="font-size: 0.85rem;">Apellido</label>
                        <input type="text" name="last_name" id="last_name" class="form-control form-control-ios @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required placeholder="Ej. Pérez">
                        @error('last_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="dni" class="form-label fw-semibold" style="font-size: 0.85rem;">DNI (Opcional)</label>
                        <input type="text" name="dni" id="dni" class="form-control form-control-ios @error('dni') is-invalid @enderror" value="{{ old('dni') }}" placeholder="Ej. 12345678">
                        @error('dni')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold" style="font-size: 0.85rem;">Teléfono de Contacto</label>
                        <input type="text" name="phone" id="phone" class="form-control form-control-ios @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Ej. +54911...">
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold" style="font-size: 0.85rem;">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control form-control-ios @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="juan.perez@example.com">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Se enviará una invitación a este correo para configurar la contraseña.</small>
                    </div>
                </div>
            </div>

            <!-- Block 2: Role and Relationship -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-shield-lock-fill text-success me-2"></i>Rol y Permisos</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="relationship_type" class="form-label fw-semibold" style="font-size: 0.85rem;">Tipo de Relación</label>
                        <select name="relationship_type" id="relationship_type" class="form-select form-control-ios @error('relationship_type') is-invalid @enderror" required>
                            <option value="">Selecciona una opción</option>
                            <option value="owner" {{ old('relationship_type') === 'owner' ? 'selected' : '' }}>Propietario</option>
                            <option value="tenant" {{ old('relationship_type') === 'tenant' ? 'selected' : '' }}>Inquilino Autorizado</option>
                            <option value="board" {{ old('relationship_type') === 'board' ? 'selected' : '' }}>Consejo o Directorio</option>
                            <option value="family" {{ old('relationship_type') === 'family' ? 'selected' : '' }}>Familiar Autorizado</option>
                            <option value="operator" {{ old('relationship_type') === 'operator' ? 'selected' : '' }}>Personal Operador Administrativo</option>
                            <option value="accounting" {{ old('relationship_type') === 'accounting' ? 'selected' : '' }}>Personal de Contabilidad</option>
                            <option value="admin" {{ old('relationship_type') === 'admin' ? 'selected' : '' }}>Administrador General</option>
                        </select>
                        @error('relationship_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="role_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Rol de Sistema</label>
                        <select name="role_id" id="role_id" class="form-select form-control-ios @error('role_id') is-invalid @enderror" required>
                            <option value="">Selecciona el rol asignado</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Block 3: Association with Functional Units -->
            <div class="ios-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-house-door-fill text-success me-2"></i>Relación con Lotes y Unidades</h5>
                <p class="text-muted mb-4" style="font-size: 0.85rem;">Asocia este usuario a uno o más lotes del barrio para permitirle visualizar las expensas, cuenta corriente y crear reclamos vinculados.</p>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold d-block mb-3" style="font-size: 0.85rem;">Unidades Funcionales</label>
                        
                        <div class="row border-ios p-3 rounded-4 bg-body-secondary" style="max-height: 200px; overflow-y: auto;">
                            @foreach($functionalUnits as $unit)
                                <div class="col-sm-6 col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input text-success" type="checkbox" name="functional_unit_ids[]" value="{{ $unit->id }}" id="unit_{{ $unit->id }}" {{ is_array(old('functional_unit_ids')) && in_array($unit->id, old('functional_unit_ids')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="unit_{{ $unit->id }}">
                                            UF Lote {{ $unit->lot->number }} <span class="text-muted" style="font-size: 0.75rem;">({{ $unit->code }})</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Block 4: Notes -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-chat-text-fill text-success me-2"></i>Observaciones</h5>
                <div class="col-12">
                    <textarea name="notes" id="notes" rows="4" class="form-control form-control-ios" placeholder="Notas o aclaraciones sobre el usuario...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Crear Usuario</button>
            </div>
        </form>
    </div>
</div>
@endsection
