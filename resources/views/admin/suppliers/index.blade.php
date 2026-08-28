@extends('layouts.app')

@section('title', 'Proveedores')
@section('page_title', 'Administración de Proveedores')

@section('content')
<!-- Search & Filters -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.suppliers.index') }}" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por Razón Social, CUIT, Rubro..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-4">
            <select name="status" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todos los Estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="col-md-3 d-grid">
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Suppliers Card List -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Lista de Proveedores</h5>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-plus-circle me-2"></i>Nuevo Proveedor</a>
    </div>

    <!-- Desktop View Table -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">PROVEEDOR</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">CUIT</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">RUBRO / CATEGORIA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DATOS BANCARIOS</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 15%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr class="border-bottom border-ios">
                        <td>
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $supplier->business_name }}</h6>
                            @if($supplier->email)
                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $supplier->email }}</small>
                            @endif
                        </td>
                        <td style="font-size: 0.9rem;">{{ $supplier->cuit }}</td>
                        <td style="font-size: 0.9rem;"><span class="badge bg-secondary-subtle text-secondary badge-ios">{{ $supplier->category }}</span></td>
                        <td>
                            @if($supplier->bank_name || $supplier->cbu_alias)
                                <span class="d-block" style="font-size: 0.85rem;">{{ $supplier->bank_name ?? 'Banco sin especificar' }}</span>
                                <small class="text-muted" style="font-size: 0.75rem;">CBU/Alias: {{ $supplier->cbu_alias ?? '-' }}</small>
                            @else
                                <span class="text-muted" style="font-size: 0.85rem;">No registrado</span>
                            @endif
                        </td>
                        <td>
                            @if($supplier->status === 'active')
                                <span class="badge bg-success text-white badge-ios">Activo</span>
                            @else
                                <span class="badge bg-danger text-white badge-ios">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- View -->
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Ver Ficha">
                                    <i class="bi bi-eye-fill"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline form-delete" data-supplier-name="{{ $supplier->business_name }}">
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
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-person-badge text-muted fs-1 d-block mb-3"></i>
                            @if(request('search') || request('status'))
                                <span class="text-muted">No se encontraron proveedores para tu búsqueda.</span>
                                <div class="mt-3">
                                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Limpiar filtros</a>
                                </div>
                            @else
                                <span class="text-muted">Todavía no hay proveedores registrados en el sistema.</span>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile View: Stacked Cards -->
    <div class="d-block d-md-none">
        @forelse($suppliers as $supplier)
            <div class="p-3 border-bottom border-ios mb-3 rounded-4 bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold m-0" style="font-size: 1.05rem;">{{ $supplier->business_name }}</h6>
                        <small class="text-muted d-block">{{ $supplier->cuit }}</small>
                    </div>
                    @if($supplier->status === 'active')
                        <span class="badge bg-success text-white badge-ios">Activo</span>
                    @else
                        <span class="badge bg-danger text-white badge-ios">Inactivo</span>
                    @endif
                </div>

                <div class="my-2" style="font-size: 0.85rem; line-height: 1.5;">
                    <div class="mb-1"><strong>Rubro:</strong> {{ $supplier->category }}</div>
                    <div class="mb-1"><strong>Banco:</strong> {{ $supplier->bank_name ?? '-' }}</div>
                    <div class="mb-1"><strong>CBU/Alias:</strong> {{ $supplier->cbu_alias ?? '-' }}</div>
                    @if($supplier->email || $supplier->phone)
                        <div class="mb-1"><strong>Contacto:</strong> {{ $supplier->email ?? '' }} {{ $supplier->phone ? '('.$supplier->phone.')' : '' }}</div>
                    @endif
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top border-ios">
                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success px-3 py-2">
                        <i class="bi bi-eye-fill me-1"></i> Ver Ficha
                    </a>
                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary px-3 py-2">
                        <i class="bi bi-pencil-fill me-1"></i> Editar
                    </a>
                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline form-delete" data-supplier-name="{{ $supplier->business_name }}">
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
                <i class="bi bi-person-badge text-muted fs-2 d-block mb-2"></i>
                @if(request('search') || request('status'))
                    <span>No se encontraron proveedores para tu búsqueda.</span>
                    <div class="mt-3">
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Limpiar filtros</a>
                    </div>
                @else
                    <span>Todavía no hay proveedores registrados en el sistema.</span>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = this.getAttribute('data-supplier-name');
                if (confirm(`¿Estás seguro de que deseas eliminar al proveedor "${name}"? Esta acción no se puede deshacer.`)) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
