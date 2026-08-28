@extends('layouts.app')

@section('title', 'Auditoría')
@section('page_title', 'Bitácora de Auditoría del Sistema')

@section('content')
<!-- Search & Filters -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.audit.index') }}" class="row g-3 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-ios" placeholder="Buscar por IP, tabla, valores modificados..." value="{{ request('search') }}">
        </div>

        <div class="col-md-4">
            <select name="event" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todos los Eventos</option>
                <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Creado (Insert)</option>
                <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Actualizado (Update)</option>
                <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Eliminado (Delete)</option>
            </select>
        </div>

        <div class="col-md-3 d-grid">
            <a href="{{ route('admin.audit.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Logs list -->
<div class="ios-card">
    <h5 class="fw-bold mb-4">Registro General de Cambios</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">FECHA / HORA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">OPERADOR</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 10%;">ACCIÓN</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">TABLA AFECTADA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">VALORES ANTERIORES</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">VALORES NUEVOS</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 8%;">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-bottom border-ios">
                        <td style="font-size: 0.85rem;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="fw-bold">{{ $log->user ? $log->user->name : 'Sistema / Seeder' }}</td>
                        <td>
                            @if($log->event === 'created')
                                <span class="badge bg-success-subtle text-success badge-ios">CREADO</span>
                            @elseif($log->event === 'updated')
                                <span class="badge bg-warning-subtle text-warning badge-ios">ACTUALIZADO</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger badge-ios">ELIMINADO</span>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ class_basename($log->auditable_type) }} (ID: {{ $log->auditable_id }})</td>
                        <td style="font-size: 0.75rem;" class="text-muted text-truncate" style="max-width: 180px;" title="{{ $log->old_values }}">
                            <code>{{ $log->old_values }}</code>
                        </td>
                        <td style="font-size: 0.75rem;" class="text-muted text-truncate" style="max-width: 180px;" title="{{ $log->new_values }}">
                            <code>{{ $log->new_values }}</code>
                        </td>
                        <td class="text-end"><code>{{ $log->ip_address }}</code></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No hay logs de auditoría registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
