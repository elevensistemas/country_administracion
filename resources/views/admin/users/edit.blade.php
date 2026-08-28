@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('page_title', 'Modificar Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- Block 1: Personal Data -->
            <div class="ios-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0"><i class="bi bi-person-fill text-success me-2"></i>Datos Personales</h5>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios">ID: {{ $user->id }}</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control form-control-ios @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label fw-semibold" style="font-size: 0.85rem;">Apellido</label>
                        <input type="text" name="last_name" id="last_name" class="form-control form-control-ios @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="dni" class="form-label fw-semibold" style="font-size: 0.85rem;">DNI (Opcional)</label>
                        <input type="text" name="dni" id="dni" class="form-control form-control-ios @error('dni') is-invalid @enderror" value="{{ old('dni', $user->dni) }}">
                        @error('dni')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold" style="font-size: 0.85rem;">Teléfono de Contacto</label>
                        <input type="text" name="phone" id="phone" class="form-control form-control-ios @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold" style="font-size: 0.85rem;">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control form-control-ios @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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
                            <option value="owner" {{ old('relationship_type', $user->relationship_type) === 'owner' ? 'selected' : '' }}>Propietario</option>
                            <option value="tenant" {{ old('relationship_type', $user->relationship_type) === 'tenant' ? 'selected' : '' }}>Inquilino Autorizado</option>
                            <option value="board" {{ old('relationship_type', $user->relationship_type) === 'board' ? 'selected' : '' }}>Consejo o Directorio</option>
                            <option value="family" {{ old('relationship_type', $user->relationship_type) === 'family' ? 'selected' : '' }}>Familiar Autorizado</option>
                            <option value="operator" {{ old('relationship_type', $user->relationship_type) === 'operator' ? 'selected' : '' }}>Personal Operador Administrativo</option>
                            <option value="accounting" {{ old('relationship_type', $user->relationship_type) === 'accounting' ? 'selected' : '' }}>Personal de Contabilidad</option>
                            <option value="admin" {{ old('relationship_type', $user->relationship_type) === 'admin' ? 'selected' : '' }}>Administrador General</option>
                            <option value="superadmin" {{ old('relationship_type', $user->relationship_type) === 'superadmin' ? 'selected' : '' }}>Super Administrador</option>
                        </select>
                        @error('relationship_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="role_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Rol de Sistema</label>
                        <select name="role_id" id="role_id" class="form-select form-control-ios @error('role_id') is-invalid @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $userRoleId) == $role->id ? 'selected' : '' }}>
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
                <p class="text-muted mb-4" style="font-size: 0.85rem;">Vincula este usuario a sus unidades funcionales asociadas.</p>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold d-block mb-3" style="font-size: 0.85rem;">Unidades Funcionales</label>
                        
                        <div class="row border-ios p-3 rounded-4 bg-body-secondary" style="max-height: 200px; overflow-y: auto;">
                            @foreach($functionalUnits as $unit)
                                <div class="col-sm-6 col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input text-success" type="checkbox" name="functional_unit_ids[]" value="{{ $unit->id }}" id="unit_{{ $unit->id }}" {{ in_array($unit->id, old('functional_unit_ids', $associatedUnits)) ? 'checked' : '' }}>
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

            <!-- Block 4: Adoption Metadata (Read-Only) -->
            <div class="ios-card bg-body-secondary border-0">
                <h5 class="fw-bold mb-4"><i class="bi bi-graph-up-arrow text-success me-2"></i>Métricas de Adopción y Conexión</h5>
                
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Primer Acceso</span>
                        <strong class="d-block" style="font-size: 0.9rem;">
                            {{ $user->first_login_at ? $user->first_login_at->isoFormat('D MMM Y, HH:mm') : 'Nunca ingresó' }}
                        </strong>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Último Acceso</span>
                        <strong class="d-block" style="font-size: 0.9rem;">
                            {{ $user->last_login_at ? $user->last_login_at->isoFormat('D MMM Y, HH:mm') : 'Nunca ingresó' }}
                        </strong>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Cantidad de Accesos</span>
                        <strong class="d-block" style="font-size: 0.9rem;">
                            {{ $user->login_count }} ingresos
                        </strong>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">Estado Términos</span>
                        <strong class="d-block text-capitalize" style="font-size: 0.9rem;">
                            @if($user->terms_accepted_at)
                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Aceptados</span>
                            @else
                                <span class="text-warning"><i class="bi bi-x-circle-fill"></i> Pendientes</span>
                            @endif
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Block 5: Notes -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-chat-text-fill text-success me-2"></i>Observaciones</h5>
                <div class="col-12">
                    <textarea name="notes" id="notes" rows="4" class="form-control form-control-ios" placeholder="Notas o aclaraciones sobre el usuario...">{{ old('notes', $user->notes) }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
