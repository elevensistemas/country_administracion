@extends('layouts.app')

@section('title', 'Métricas de Adopción')
@section('page_title', 'Adopción del Sistema y Accesos')

@section('content')
<!-- Top counters -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="ios-card bg-body-secondary border-0 text-center py-3">
            <span class="text-muted d-block" style="font-size: 0.8rem;">TASA DE ADOPCIÓN</span>
            <h2 class="fw-bold m-0 text-success">{{ $adoptionRate }}%</h2>
            <small class="text-muted">Usuarios Activos / Total</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ios-card bg-success-subtle text-success text-center py-3">
            <span class="text-success d-block" style="font-size: 0.8rem;">VECINOS ACTIVOS</span>
            <h2 class="fw-bold m-0">{{ $activeUsers }}</h2>
            <small class="text-muted">Ingresaron al portal</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ios-card bg-warning-subtle text-warning text-center py-3">
            <span class="text-warning d-block" style="font-size: 0.8rem;">INVITACIONES PENDIENTES</span>
            <h2 class="fw-bold m-0">{{ $pendingInvites }}</h2>
            <small class="text-muted">Aún no ingresaron</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ios-card bg-danger-subtle text-danger text-center py-3">
            <span class="text-danger d-block" style="font-size: 0.8rem;">BLOQUEADOS</span>
            <h2 class="fw-bold m-0">{{ $blockedUsers }}</h2>
            <small class="text-muted">Cuentas restringidas</small>
        </div>
    </div>
</div>

<div class="row">
    <!-- Log stream (Left) -->
    <div class="col-lg-8 mb-4">
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Registro de Conexiones</h5>
                <form method="GET" action="{{ route('admin.adoption.index') }}">
                    <input type="text" name="search" class="form-control form-control-ios form-control-sm" placeholder="Buscar por usuario, IP..." value="{{ request('search') }}">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA / HORA</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">VECINO</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">IP ADDRESS</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DISPOSITIVO / NAVEGADOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginLogs as $log)
                            <tr class="border-bottom border-ios">
                                <td style="font-size: 0.85rem;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold">
                                    @if($log->user)
                                        {{ $log->user->full_name }}
                                    @else
                                        <span class="text-danger" style="font-size: 0.8rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i> Intento Fallido (Desconocido)</span>
                                    @endif
                                </td>
                                <td><code>{{ $log->ip_address }}</code></td>
                                <td class="text-muted text-truncate" style="font-size: 0.8rem; max-width: 250px;">
                                    {{ $log->user_agent }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No se registran accesos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $loginLogs->links() }}
            </div>
        </div>
    </div>

    <!-- Top IPs (Right) -->
    <div class="col-lg-4">
        <div class="ios-card bg-body-secondary border-0">
            <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-success me-2"></i>Concentración de Conexiones por IP</h6>
            <p class="text-muted" style="font-size: 0.85rem;">Identifica las IPs con mayor cantidad de ingresos al portal para monitoreo de seguridad.</p>

            <ul class="list-group list-group-flush mt-2">
                @foreach($topIps as $ip)
                    <li class="list-group-item bg-transparent border-0 px-0 py-2 border-bottom border-ios d-flex justify-content-between align-items-center" style="font-size: 0.9rem;">
                        <code>{{ $ip->ip_address }}</code>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">{{ $ip->count }} accesos</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
