@extends('layouts.app')

@section('title', 'Zonas Comunes')
@section('page_title', 'Gestión de Zonas Comunes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <p class="text-muted m-0">Registra y administra los espacios comunes disponibles para reservas por los propietarios.</p>
    </div>
    <a href="{{ route('admin.common-areas.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-plus-circle-fill me-2"></i>Nueva Zona Común</a>
</div>

<div class="row">
    @forelse($commonAreas as $area)
        <div class="col-sm-6 col-lg-4 mb-4">
            <div class="ios-card h-100 p-0 overflow-hidden d-flex flex-column justify-content-between">
                <div>
                    <!-- Image / Header -->
                    <div style="height: 180px; background-color: var(--ios-bg); position: relative; overflow: hidden;">
                        @if($area->photos && count($area->photos) > 0 && File::exists(public_path('storage/' . $area->photos[0])))
                            <img src="{{ asset('storage/' . $area->photos[0]) }}" class="w-100 h-100 object-fit-cover" alt="{{ $area->name }}">
                        @else
                            <!-- Generated Placeholder using SVGs -->
                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-success bg-success-subtle bg-opacity-25">
                                <i class="bi bi-building-fill" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        <!-- Price Badge -->
                        <span class="position-absolute top-0 end-0 bg-success text-white fw-bold px-3 py-1 m-3 rounded-pill" style="font-size: 0.9rem;">
                            {{ $area->price > 0 ? '$' . number_format($area->price, 0, ',', '.') : 'Gratuito' }}
                        </span>
                    </div>

                    <!-- Body -->
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold m-0">{{ $area->name }}</h5>
                            @if($area->is_active)
                                <span class="badge bg-success-subtle text-success badge-ios">Activo</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary badge-ios">Inactivo</span>
                            @endif
                        </div>
                        <p class="text-muted mb-3" style="font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $area->description ?? 'Sin descripción disponible.' }}
                        </p>

                        <div class="border-top border-ios pt-2" style="font-size: 0.85rem;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Capacidad:</span>
                                <strong class="text-dark">{{ $area->capacity }} personas</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Horario:</span>
                                <strong class="text-dark">{{ substr($area->schedule_start, 0, 5) }} a {{ substr($area->schedule_end, 0, 5) }} hs</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Duración:</span>
                                <strong class="text-dark">{{ $area->duration_minutes }} minutos</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-3 border-top border-ios bg-body-tertiary d-flex gap-2">
                    <a href="{{ route('admin.common-areas.edit', $area) }}" class="btn btn-sm btn-ios btn-ios-secondary flex-fill text-primary">
                        <i class="bi bi-pencil-fill me-1"></i> Editar
                    </a>
                    <form action="{{ route('admin.common-areas.destroy', $area) }}" method="POST" class="d-inline flex-fill" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta zona común? Se borrarán sus reservas.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary w-100 text-danger">
                            <i class="bi bi-trash-fill me-1"></i> Borrar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="ios-card">
                <i class="bi bi-building-fill-dash text-muted fs-1 d-block mb-3"></i>
                <h5 class="fw-bold">No hay zonas comunes registradas</h5>
                <p class="text-muted">Comienza registrando tu primer espacio común (ej: SUM, Cancha de Tenis, Parrillas).</p>
                <a href="{{ route('admin.common-areas.create') }}" class="btn btn-ios btn-ios-primary mt-2">Nueva Zona Común</a>
            </div>
        </div>
    @endforelse
</div>
@endsection
