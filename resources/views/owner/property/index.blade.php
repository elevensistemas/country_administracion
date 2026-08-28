@extends('layouts.owner')

@section('title', 'Mi Propiedad')

@section('content')
<div class="row g-3">
    <!-- Lot Title -->
    <div class="col-12 mb-2">
        <h4 class="fw-bold m-0 text-success"><i class="bi bi-house-fill me-2"></i>Mi Propiedad</h4>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Consulta los datos técnicos, vehículos y residentes registrados en tu lote.</p>
    </div>

    @if($activeLot)
        <!-- Property Info Card -->
        <div class="col-12">
            <div class="ios-card">
                <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Ficha del Lote</h6>
                <div class="row g-2" style="font-size: 0.9rem;">
                    <div class="col-6">
                        <span class="text-muted d-block">Número de Lote:</span>
                        <strong class="text-dark">Lote {{ $activeLot->number }}</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block">Código de Lote:</span>
                        <strong class="text-dark">{{ $activeLot->code }}</strong>
                    </div>
                    <div class="col-12 mt-2">
                        <span class="text-muted d-block">Dirección Interna:</span>
                        <strong class="text-dark">{{ $activeLot->internal_address ?? 'Calle Principal La Ranita' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Propietarios -->
        <div class="col-12">
            <div class="ios-card">
                <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Propietario Registrado</h6>
                @if($activeLot->owner)
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: 600;">
                            {{ substr($activeLot->owner->name, 0, 1) }}{{ substr($activeLot->owner->last_name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $activeLot->owner->full_name }}</h6>
                            <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $activeLot->owner->email }} | {{ $activeLot->owner->phone ?? 'Sin teléfono' }}</small>
                        </div>
                    </div>
                @else
                    <span class="text-muted" style="font-size: 0.85rem;">No se registra propietario asignado.</span>
                @endif
            </div>
        </div>

        <!-- Residents Card -->
        <div class="col-12">
            <div class="ios-card">
                <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Co-Residentes Declarados</h6>
                <div class="d-flex flex-column gap-2">
                    @forelse($activeLot->residents as $res)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-ios last-border-none">
                            <div>
                                <span class="fw-bold d-block" style="font-size: 0.9rem;">{{ $res->full_name }}</span>
                                <small class="text-muted" style="font-size: 0.75rem;">DNI: {{ $res->dni ?? 'N/C' }} | Rol: {{ $res->relationship ?? 'Familiar' }}</small>
                            </div>
                            @if($res->phone)
                                <a href="tel:{{ $res->phone }}" class="btn btn-sm btn-ios btn-ios-secondary text-success"><i class="bi bi-telephone-fill"></i></a>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted" style="font-size: 0.85rem;">
                            No tienes co-residentes registrados para este lote.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Vehicles Card -->
        <div class="col-12">
            <div class="ios-card">
                <h6 class="fw-bold mb-3 text-muted" style="font-size: 0.8rem; letter-spacing: 0.5px; text-uppercase: true;">Vehículos y Patentes</h6>
                <div class="d-flex flex-column gap-2">
                    @forelse($activeLot->vehicles as $veh)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-ios last-border-none">
                            <div>
                                <span class="fw-bold d-block" style="font-size: 0.9rem;">{{ $veh->brand }} {{ $veh->model }}</span>
                                <small class="text-muted" style="font-size: 0.75rem;">Color: {{ $veh->color ?? 'N/C' }}</small>
                            </div>
                            <span class="badge bg-secondary text-uppercase font-monospace px-3 py-1.5" style="letter-spacing: 0.5px; font-size: 0.8rem;">
                                {{ $veh->license_plate }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted" style="font-size: 0.85rem;">
                            No tienes vehículos registrados para este lote.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Action / Request changes Button -->
        <div class="col-12 mt-2">
            <button type="button" class="btn btn-ios btn-ios-primary w-100 py-3" data-bs-toggle="modal" data-bs-target="#requestChangeModal">
                <i class="bi bi-pencil-square me-2"></i> Solicitar Cambio de Datos
            </button>
            <small class="text-muted text-center d-block mt-2" style="font-size: 0.75rem;">
                * Por seguridad, los cambios en vehículos o personas residentes deben ser aprobados por la administración.
            </small>
        </div>

        <!-- Request Change Modal -->
        <div class="modal fade" id="requestChangeModal" tabindex="-1" aria-labelledby="requestChangeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4">
                    <div class="modal-header border-bottom border-ios p-4">
                        <h5 class="modal-title fw-bold" id="requestChangeModalLabel"><i class="bi bi-pencil-square text-success me-2"></i>Solicitar Cambio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('owner.property.request-change') }}">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="text-muted mb-3" style="font-size: 0.85rem;">Describe a continuación los cambios que deseas solicitar (ej: agregar/quitar vehículos, registrar co-residentes, modificar patentes, teléfonos, etc.):</p>
                            
                            <div class="mb-3">
                                <label for="change_details" class="form-label fw-bold" style="font-size: 0.8rem;">Detalle de la solicitud</label>
                                <textarea name="change_details" id="change_details" rows="5" class="form-control form-control-ios" placeholder="Ej: Solicito dar de alta un nuevo automóvil Ford Focus blanco, patente AA 345 CC. También registrar a mi esposa María Gómez, DNI 12.345.678 como residente..." required style="border-radius: 12px;"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-ios p-3 bg-body-tertiary">
                            <button type="button" class="btn btn-ios btn-ios-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-ios btn-ios-primary btn-sm">Enviar Solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="col-12 text-center py-5">
            <div class="ios-card">
                <p class="text-muted m-0">No se encontraron lotes asociados a tu usuario.</p>
            </div>
        </div>
    @endif
</div>

<style>
    .last-border-none:last-child {
        border-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
</style>
@endsection
