@extends('layouts.app')

@section('title', 'Propietarios')
@section('page_title', 'Administración de Propietarios')

@section('content')
<!-- Search & Filters -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.owners.index') }}" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por nombre, DNI, CUIT, email..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select form-control-ios">
                <option value="">Todos los Estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-ios btn-ios-primary flex-fill">Buscar</button>
            <a href="{{ route('admin.owners.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Owners Card -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Lista de Propietarios</h5>
        <a href="{{ route('admin.owners.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Propietario</a>
    </div>

    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">PROPIETARIO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DNI / CUIT</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">CONTACTO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">LOTES POSEÍDOS</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">VÍA PREFERIDA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 15%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($owners as $owner)
                    <tr class="border-bottom border-ios">
                        <td>
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">
                                <a href="{{ route('admin.owners.show', $owner) }}" class="text-decoration-none text-reset">
                                    {{ $owner->full_name }}
                                </a>
                            </h6>
                            @if($owner->business_name)
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Raz. Social: {{ $owner->business_name }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="d-block" style="font-size: 0.9rem;">DNI: {{ $owner->dni ?? 'N/C' }}</span>
                            <small class="text-muted" style="font-size: 0.8rem;">CUIT: {{ $owner->cuit ?? 'N/C' }}</small>
                        </td>
                        <td>
                            <span class="d-block" style="font-size: 0.9rem;">{{ $owner->email }}</span>
                            <small class="text-muted" style="font-size: 0.8rem;">{{ $owner->phone ?? 'Sin teléfono' }}</small>
                        </td>
                        <td>
                            @forelse($owner->lots as $lot)
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 mb-1 fw-bold" style="font-size: 0.75rem;">
                                    Lote {{ $lot->number }}
                                </span>
                            @empty
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1 mb-1" style="font-size: 0.75rem;">
                                    Sin Lotes
                                </span>
                            @endforelse
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary text-uppercase" style="font-size: 0.75rem;">
                                {{ $owner->preferred_channel }}
                            </span>
                        </td>
                        <td>
                            @if($owner->status === 'active')
                                <span class="badge bg-success text-white badge-ios">Activo</span>
                            @else
                                <span class="badge bg-danger text-white badge-ios">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- Details -->
                                <a href="{{ route('admin.owners.show', $owner) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Ver Ficha Completa">
                                    <i class="bi bi-eye-fill"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('admin.owners.edit', $owner) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.owners.destroy', $owner) }}" method="POST" class="d-inline form-delete" data-owner-name="{{ $owner->full_name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-people text-muted fs-1 d-block mb-3"></i>
                            @if(request('search') || request('status'))
                                <span class="text-muted">No se encontraron resultados para tu búsqueda o filtros.</span>
                                <div class="mt-3">
                                    <a href="{{ route('admin.owners.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Limpiar filtros</a>
                                </div>
                            @else
                                <span class="text-muted">Todavía no hay propietarios registrados en el sistema.</span>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile view: Stacked Cards -->
    <div class="d-block d-md-none">
        @forelse($owners as $owner)
            <div class="p-3 border-bottom border-ios mb-3 rounded-4 bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold m-0" style="font-size: 1.05rem;">
                            <a href="{{ route('admin.owners.show', $owner) }}" class="text-decoration-none text-success">
                                {{ $owner->full_name }}
                            </a>
                        </h6>
                        @if($owner->business_name)
                            <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $owner->business_name }}</small>
                        @endif
                    </div>
                    @if($owner->status === 'active')
                        <span class="badge bg-success text-white badge-ios">Activo</span>
                    @else
                        <span class="badge bg-danger text-white badge-ios">Inactivo</span>
                    @endif
                </div>

                <div class="my-2" style="font-size: 0.85rem; line-height: 1.5;">
                    <div class="mb-2">
                        <strong>Lotes:</strong>
                        @forelse($owner->lots as $lot)
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                Lote {{ $lot->number }}
                            </span>
                        @empty
                            <span class="text-muted">Sin Lotes</span>
                        @endforelse
                    </div>
                    <div class="mb-1"><strong>DNI:</strong> {{ $owner->dni ?? 'N/C' }} | <strong>CUIT:</strong> {{ $owner->cuit ?? 'N/C' }}</div>
                    <div class="mb-1"><strong>Email:</strong> {{ $owner->email }}</div>
                    <div class="mb-1"><strong>Teléfono:</strong> {{ $owner->phone ?? 'Sin teléfono' }}</div>
                    <div><strong>Vía Preferida:</strong> <span class="text-uppercase text-muted">{{ $owner->preferred_channel }}</span></div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top border-ios">
                    <a href="{{ route('admin.owners.show', $owner) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success px-3 py-2">
                        <i class="bi bi-eye-fill me-1"></i> Ver
                    </a>
                    <a href="{{ route('admin.owners.edit', $owner) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary px-3 py-2">
                        <i class="bi bi-pencil-fill me-1"></i> Editar
                    </a>
                    <form action="{{ route('admin.owners.destroy', $owner) }}" method="POST" class="d-inline form-delete" data-owner-name="{{ $owner->full_name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger px-3 py-2">
                            <i class="bi bi-trash-fill me-1"></i> Borrar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people text-muted fs-2 d-block mb-2"></i>
                @if(request('search') || request('status'))
                    <span>No se encontraron resultados para tu búsqueda o filtros.</span>
                    <div class="mt-3">
                        <a href="{{ route('admin.owners.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Limpiar filtros</a>
                    </div>
                @else
                    <span>Todavía no hay propietarios registrados en el sistema.</span>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $owners->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const ownerName = this.getAttribute('data-owner-name');
                if (confirm(`¿Estás seguro de que deseas eliminar al propietario "${ownerName}"? Esta acción no se puede deshacer.`)) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
