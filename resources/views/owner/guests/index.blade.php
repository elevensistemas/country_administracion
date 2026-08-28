@extends('layouts.owner')

@section('title', 'Invitados')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold m-0 text-success"><i class="bi bi-qr-code me-2"></i>Mis Invitados</h4>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Gestiona y autoriza el acceso a visitas y proveedores.</p>
        </div>
        <a href="{{ route('owner.guests.create') }}" class="btn btn-sm btn-ios btn-ios-primary"><i class="bi bi-person-plus me-1"></i>Nuevo Invitado</a>
    </div>

    @forelse($guests as $guest)
        <div class="col-12">
            <div class="ios-card bg-body-tertiary p-3 mb-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold m-0" style="font-size: 1.05rem;">{{ $guest->full_name }}</h6>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            @if($guest->type === 'individual')
                                <span class="badge bg-primary-subtle text-primary badge-ios" style="font-size: 0.65rem;">Individual</span>
                            @elseif($guest->type === 'frequent')
                                <span class="badge bg-info-subtle text-info badge-ios" style="font-size: 0.65rem;">Frecuente</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning badge-ios" style="font-size: 0.65rem;">Lista</span>
                            @endif
                        </small>
                    </div>
                    @if($guest->status === 'active')
                        <span class="badge bg-success-subtle text-success badge-ios">Activo</span>
                    @elseif($guest->status === 'used')
                        <span class="badge bg-secondary-subtle text-secondary badge-ios">Ingresado</span>
                    @else
                        <span class="badge bg-danger-subtle text-danger badge-ios">Vencido</span>
                    @endif
                </div>

                <div class="my-2 border-top border-ios pt-2" style="font-size: 0.85rem; line-height: 1.4;">
                    @if($guest->dni)
                        <div class="mb-1"><span class="text-muted">DNI:</span> <strong class="text-dark">{{ $guest->dni }}</strong></div>
                    @endif
                    @if($guest->license_plate)
                        <div class="mb-1"><span class="text-muted">Patente Auto:</span> <strong class="text-dark">{{ $guest->license_plate }}</strong></div>
                    @endif
                    @if($guest->visit_date)
                        <div class="mb-1"><span class="text-muted">Fecha Visita:</span> <strong class="text-dark">{{ $guest->visit_date->format('d/m/Y') }}</strong></div>
                    @endif
                    @if($guest->type === 'list' && $guest->notes)
                        <div class="bg-body-secondary p-2 rounded-3 mt-1 text-muted" style="font-size: 0.8rem; white-space: pre-wrap;">
                            <i class="bi bi-people-fill me-1"></i> <strong>Invitados de la lista:</strong><br>{{ $guest->notes }}
                        </div>
                    @elseif($guest->notes)
                        <div class="bg-body-secondary p-2 rounded-3 mt-1 text-muted" style="font-size: 0.8rem;">
                            <i class="bi bi-chat-left-text me-1"></i> {{ $guest->notes }}
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top border-ios">
                    @if($guest->type !== 'list')
                        <a href="{{ route('owner.guests.qr', $guest) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success px-3 py-2">
                            <i class="bi bi-qr-code me-1"></i> Compartir QR
                        </a>
                    @endif

                    <form action="{{ route('owner.guests.destroy', $guest) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta autorización?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger px-3 py-2">
                            <i class="bi bi-trash me-1"></i> Cancelar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="ios-card">
                <i class="bi bi-person-badge text-muted fs-1 d-block mb-3"></i>
                <h6 class="fw-bold">No tienes invitados autorizados</h6>
                <p class="text-muted" style="font-size: 0.85rem;">Autoriza de forma ágil el acceso para visitas directas, cumpleaños o proveedores.</p>
                <a href="{{ route('owner.guests.create') }}" class="btn btn-ios btn-ios-primary mt-2">Nueva Autorización</a>
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="col-12 mt-3">
        {{ $guests->links() }}
    </div>
</div>
@endsection
