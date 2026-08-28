@extends('layouts.app')

@section('title', 'Usuarios')
@section('page_title', 'Administración de Usuarios')

@section('content')
<!-- Filter & Search Card -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-center">
        <!-- Search Input -->
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por nombre, email, DNI..." value="{{ request('search') }}">
            </div>
        </div>

        <!-- Role Filter -->
        <div class="col-md-3">
            <select name="role" class="form-select form-control-ios">
                <option value="">Todos los Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                        {{ $role->display_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="col-md-3">
            <select name="status" class="form-select form-control-ios">
                <option value="">Todos los Estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloqueado</option>
                <option value="pending_invite" {{ request('status') === 'pending_invite' ? 'selected' : '' }}>Invitación Pendiente</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-ios btn-ios-primary flex-fill">Buscar</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Users List -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Lista de Usuarios</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario</a>
    </div>

    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 25%;">USUARIO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DNI / TELÉFONO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ROL / RELACIÓN</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">UNIDADES</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 20%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-bottom border-ios">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: 600;">
                                    {{ substr($user->name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $user->full_name }}</h6>
                                    <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="d-block" style="font-size: 0.9rem;">DNI: {{ $user->dni ?? 'N/C' }}</span>
                            <small class="text-muted">{{ $user->phone ?? 'Sin teléfono' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary badge-ios">
                                {{ $user->roles->first()?->display_name ?? 'Sin Rol' }}
                            </span>
                            <small class="text-muted d-block text-capitalize mt-1" style="font-size: 0.75rem;">Relación: {{ str_replace('_', ' ', $user->relationship_type) }}</small>
                        </td>
                        <td>
                            @forelse($user->functionalUnits as $unit)
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 mb-1 d-inline-block" style="font-size: 0.75rem;">
                                    UF {{ $unit->lot->number }}
                                </span>
                            @empty
                                <span class="text-muted" style="font-size: 0.85rem;">Ninguna</span>
                            @endforelse
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge bg-success text-white badge-ios"><i class="bi bi-check-circle me-1"></i>Activo</span>
                            @elseif($user->status === 'blocked')
                                <span class="badge bg-danger text-white badge-ios"><i class="bi bi-slash-circle me-1"></i>Bloqueado</span>
                            @else
                                <span class="badge bg-warning text-dark badge-ios"><i class="bi bi-clock me-1"></i>Pendiente</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- Resend Invite (Simulated) -->
                                @if($user->status === 'pending_invite')
                                    <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-warning" title="Reenviar Invitación">
                                            <i class="bi bi-send-fill"></i> Invitar
                                        </button>
                                    </form>
                                @endif

                                <!-- Block / Activate -->
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary {{ $user->status === 'active' ? 'text-danger' : 'text-success' }}" title="{{ $user->status === 'active' ? 'Bloquear Usuario' : 'Desbloquear Usuario' }}">
                                        <i class="bi {{ $user->status === 'active' ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
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
                            <i class="bi bi-people text-muted fs-1 d-block mb-3"></i>
                            @if(request('search') || request('role') || request('status'))
                                <span class="text-muted">No se encontraron resultados para tu búsqueda o filtros.</span>
                                <div class="mt-3">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Limpiar filtros</a>
                                </div>
                            @else
                                <span class="text-muted">Todavía no hay usuarios registrados en el sistema.</span>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile view: Stacked Cards -->
    <div class="d-block d-md-none">
        @forelse($users as $user)
            <div class="p-3 border-bottom border-ios mb-3 rounded-4 bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-weight: 600; font-size: 0.85rem;">
                            {{ substr($user->name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="fw-bold m-0" style="font-size: 1rem;">{{ $user->full_name }}</h6>
                            <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $user->email }}</small>
                        </div>
                    </div>
                    @if($user->status === 'active')
                        <span class="badge bg-success text-white badge-ios">Activo</span>
                    @elseif($user->status === 'blocked')
                        <span class="badge bg-danger text-white badge-ios">Bloqueado</span>
                    @else
                        <span class="badge bg-warning text-dark badge-ios">Pendiente</span>
                    @endif
                </div>

                <div class="my-2" style="font-size: 0.85rem; line-height: 1.5;">
                    <div class="mb-1"><strong>Rol:</strong> {{ $user->roles->first()?->display_name ?? 'Sin Rol' }} | <strong>Relación:</strong> <span class="text-capitalize text-muted">{{ str_replace('_', ' ', $user->relationship_type) }}</span></div>
                    <div class="mb-1"><strong>DNI:</strong> {{ $user->dni ?? 'N/C' }} | <strong>Teléfono:</strong> {{ $user->phone ?? 'Sin teléfono' }}</div>
                    <div>
                        <strong>Unidades:</strong>
                        @forelse($user->functionalUnits as $unit)
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                UF {{ $unit->lot->number }}
                            </span>
                        @empty
                            <span class="text-muted">Ninguna</span>
                        @endforelse
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top border-ios">
                    @if($user->status === 'pending_invite')
                        <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-warning px-2 py-2">
                                <i class="bi bi-send-fill me-1"></i> Invitar
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary {{ $user->status === 'active' ? 'text-danger' : 'text-success' }} px-2 py-2">
                            <i class="bi {{ $user->status === 'active' ? 'bi-lock-fill' : 'bi-unlock-fill' }} me-1"></i> {{ $user->status === 'active' ? 'Bloquear' : 'Activar' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary px-2 py-2">
                        <i class="bi bi-pencil-fill me-1"></i> Editar
                    </a>

                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger px-2 py-2">
                            <i class="bi bi-trash-fill me-1"></i> Borrar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people text-muted fs-2 d-block mb-2"></i>
                @if(request('search') || request('role') || request('status'))
                    <span>No se encontraron resultados para tu búsqueda o filtros.</span>
                    <div class="mt-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Limpiar filtros</a>
                    </div>
                @else
                    <span>Todavía no hay usuarios registrados en el sistema.</span>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
