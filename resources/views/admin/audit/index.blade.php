@extends('layouts.app')

@section('title', 'Auditoría y Registro de Eventos')
@section('page_title', 'Bitácora de Eventos y Control de Accesos')

@section('content')
<div class="container-fluid px-0">

    <!-- Metrics Overview -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ios-card p-3 h-100 d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Eventos Totales</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($metrics['total_audits']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ios-card p-3 h-100 d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-pencil-square fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Cambios Hoy</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($metrics['today_audits']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ios-card p-3 h-100 d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-box-arrow-in-right fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Logins Exitosos Hoy</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($metrics['today_logins']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ios-card p-3 h-100 d-flex align-items-center">
                <div class="rounded-circle p-3 me-3 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-shield-x fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Accesos Fallidos Hoy</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($metrics['today_failed_log']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="d-flex mb-4 gap-2 border-bottom pb-2">
        <a href="{{ route('admin.audit.index', ['tab' => 'audits']) }}" class="btn btn-sm {{ $tab === 'audits' ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill px-4 py-2 fw-medium">
            <i class="bi bi-database-check me-1"></i> Auditoría de Cambios y Cargas
        </a>
        <a href="{{ route('admin.audit.index', ['tab' => 'logins']) }}" class="btn btn-sm {{ $tab === 'logins' ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill px-4 py-2 fw-medium">
            <i class="bi bi-person-check me-1"></i> Registro de Inicios de Sesión (Logins)
        </a>
    </div>

    @if($tab === 'audits')
        <!-- AUDITS TAB CONTENT -->
        <!-- Search & Filters -->
        <div class="ios-card mb-4 p-3">
            <form method="GET" action="{{ route('admin.audit.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="tab" value="audits">
                
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm form-control-ios" placeholder="Buscar texto, IP..." value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Acción</label>
                    <select name="action" class="form-select form-select-sm form-control-ios">
                        <option value="">Todas</option>
                        <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Creación</option>
                        <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Modificación</option>
                        <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Eliminación</option>
                        <option value="restore" {{ request('action') === 'restore' ? 'selected' : '' }}>Restauración</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Módulo / Entidad</label>
                    <select name="model_type" class="form-select form-select-sm form-control-ios">
                        <option value="">Todos los Módulos</option>
                        @foreach($modelOptions as $class => $label)
                            <option value="{{ $class }}" {{ request('model_type') === $class ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Operador / Usuario</label>
                    <select name="user_id" class="form-select form-select-sm form-control-ios">
                        <option value="">Todos los Operadores</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} {{ $u->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Rango de Fechas</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="date_from" class="form-control form-control-ios" value="{{ request('date_from') }}" title="Desde">
                        <input type="date" name="date_to" class="form-control form-control-ios" value="{{ request('date_to') }}" title="Hasta">
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.audit.index', ['tab' => 'audits']) }}" class="btn btn-sm btn-outline-secondary px-3">Limpiar</a>
                    <button type="submit" class="btn btn-sm btn-primary px-4">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Audit Table -->
        <div class="ios-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="border-bottom text-muted" style="font-size: 0.8rem; font-weight: 600;">
                            <th class="ps-3 py-3" style="width: 14%;">FECHA / HORA</th>
                            <th style="width: 18%;">OPERADOR</th>
                            <th style="width: 12%;">ACCIÓN</th>
                            <th style="width: 18%;">MÓDULO AFECTADO</th>
                            <th>DETALLE DEL CAMBIO</th>
                            <th class="text-end pe-3" style="width: 10%;">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditLogs as $log)
                            <tr class="border-bottom">
                                <td class="ps-3" style="font-size: 0.85rem;">
                                    <span class="fw-semibold text-dark">{{ $log->created_at ? $log->created_at->format('d/m/Y') : '-' }}</span>
                                    <br>
                                    <small class="text-muted">{{ $log->created_at ? $log->created_at->format('H:i:s') : '' }}</small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($log->user->name, 0, 1) . substr($log->user->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $log->user->name }} {{ $log->user->last_name }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $log->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary badge-ios">Sistema / Seeder / Proceso</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->action === 'create')
                                        <span class="badge bg-success-subtle text-success badge-ios"><i class="bi bi-plus-circle me-1"></i> Creación</span>
                                    @elseif($log->action === 'update')
                                        <span class="badge bg-primary-subtle text-primary badge-ios"><i class="bi bi-pencil me-1"></i> Modificación</span>
                                    @elseif($log->action === 'delete')
                                        <span class="badge bg-danger-subtle text-danger badge-ios"><i class="bi bi-trash me-1"></i> Eliminación</span>
                                    @elseif($log->action === 'restore')
                                        <span class="badge bg-info-subtle text-info badge-ios"><i class="bi bi-arrow-counterclockwise me-1"></i> Restauración</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary badge-ios">{{ ucfirst($log->action) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $log->human_model_name }}</span>
                                    <br>
                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">ID: #{{ $log->model_id }}</small>
                                </td>
                                <td>
                                    @php
                                        $oldVal = is_array($log->old_values) ? $log->old_values : json_decode($log->old_values, true);
                                        $newVal = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true);
                                        $changedKeys = array_unique(array_merge(array_keys($oldVal ?? []), array_keys($newVal ?? [])));
                                    @endphp

                                    @if($log->action === 'update' && !empty($newVal))
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-light text-dark border" style="font-size: 0.75rem;">
                                                {{ count($changedKeys) }} {{ count($changedKeys) === 1 ? 'campo modificado' : 'campos modificados' }}
                                                ({{ implode(', ', array_slice($changedKeys, 0, 3)) }}{{ count($changedKeys) > 3 ? '...' : '' }})
                                            </span>
                                            <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.75rem;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#auditDiffModal"
                                                data-title="Modificación en {{ $log->human_model_name }} #{{ $log->model_id }}"
                                                data-operator="{{ $log->user ? $log->user->name . ' ' . $log->user->last_name : 'Sistema' }}"
                                                data-date="{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '' }}"
                                                data-action="{{ $log->action }}"
                                                data-old="{{ json_encode($oldVal) }}"
                                                data-new="{{ json_encode($newVal) }}">
                                                <i class="bi bi-eye me-1"></i> Ver Cambios
                                            </button>
                                        </div>
                                    @elseif($log->action === 'create' && !empty($newVal))
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.75rem;">
                                                Nuevo registro creado
                                            </span>
                                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#auditDiffModal"
                                                data-title="Alta de {{ $log->human_model_name }} #{{ $log->model_id }}"
                                                data-operator="{{ $log->user ? $log->user->name . ' ' . $log->user->last_name : 'Sistema' }}"
                                                data-date="{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '' }}"
                                                data-action="{{ $log->action }}"
                                                data-old="{}"
                                                data-new="{{ json_encode($newVal) }}">
                                                <i class="bi bi-eye me-1"></i> Ver Datos Cargados
                                            </button>
                                        </div>
                                    @elseif($log->action === 'delete')
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.75rem;">
                                                Registro eliminado
                                            </span>
                                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 0.75rem;" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#auditDiffModal"
                                                data-title="Eliminación de {{ $log->human_model_name }} #{{ $log->model_id }}"
                                                data-operator="{{ $log->user ? $log->user->name . ' ' . $log->user->last_name : 'Sistema' }}"
                                                data-date="{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '' }}"
                                                data-action="{{ $log->action }}"
                                                data-old="{{ json_encode($oldVal) }}"
                                                data-new="{}">
                                                <i class="bi bi-eye me-1"></i> Ver Datos Previos
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <code class="small text-muted">{{ $log->ip_address ?? '-' }}</code>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    No se encontraron eventos de auditoría con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($auditLogs->hasPages())
                <div class="p-3 border-top">
                    {{ $auditLogs->links() }}
                </div>
            @endif
        </div>

    @else
        <!-- LOGINS TAB CONTENT -->
        <!-- Search & Filters -->
        <div class="ios-card mb-4 p-3">
            <form method="GET" action="{{ route('admin.audit.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="tab" value="logins">
                
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Buscar</label>
                    <input type="text" name="login_search" class="form-control form-control-sm form-control-ios" placeholder="Buscar IP, navegador..." value="{{ request('login_search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Estado de Acceso</label>
                    <select name="login_status" class="form-select form-select-sm form-control-ios">
                        <option value="">Todos</option>
                        <option value="success" {{ request('login_status') === 'success' ? 'selected' : '' }}>Exitoso</option>
                        <option value="failed" {{ request('login_status') === 'failed' ? 'selected' : '' }}>Fallido (Credenciales)</option>
                        <option value="blocked" {{ request('login_status') === 'blocked' ? 'selected' : '' }}>Bloqueado (Intentos reiterados)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Usuario</label>
                    <select name="login_user_id" class="form-select form-select-sm form-control-ios">
                        <option value="">Todos los Usuarios</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('login_user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} {{ $u->last_name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Rango de Fechas</label>
                    <div class="input-group input-group-sm">
                        <input type="date" name="login_date_from" class="form-control form-control-ios" value="{{ request('login_date_from') }}" title="Desde">
                        <input type="date" name="login_date_to" class="form-control form-control-ios" value="{{ request('login_date_to') }}" title="Hasta">
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.audit.index', ['tab' => 'logins']) }}" class="btn btn-sm btn-outline-secondary px-3">Limpiar</a>
                    <button type="submit" class="btn btn-sm btn-primary px-4">
                        <i class="bi bi-funnel-fill me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Login Table -->
        <div class="ios-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="border-bottom text-muted" style="font-size: 0.8rem; font-weight: 600;">
                            <th class="ps-3 py-3" style="width: 15%;">FECHA / HORA</th>
                            <th style="width: 25%;">USUARIO</th>
                            <th style="width: 15%;">ESTADO DE ACCESO</th>
                            <th style="width: 25%;">DISPOSITIVO / NAVEGADOR</th>
                            <th class="text-end pe-3" style="width: 15%;">DIRECCIÓN IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginLogs as $login)
                            <tr class="border-bottom">
                                <td class="ps-3" style="font-size: 0.85rem;">
                                    <span class="fw-semibold text-dark">{{ $login->created_at ? $login->created_at->format('d/m/Y') : '-' }}</span>
                                    <br>
                                    <small class="text-muted">{{ $login->created_at ? $login->created_at->format('H:i:s') : '' }}</small>
                                </td>
                                <td>
                                    @if($login->user)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-dark bg-opacity-10 text-dark fw-bold d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($login->user->name, 0, 1) . substr($login->user->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $login->user->name }} {{ $login->user->last_name }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $login->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <span class="badge bg-secondary-subtle text-secondary badge-ios">Usuario Desconocido / No Registrado</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($login->status === 'success')
                                        <span class="badge bg-success-subtle text-success badge-ios">
                                            <i class="bi bi-check-circle-fill me-1"></i> Exitoso
                                        </span>
                                    @elseif($login->status === 'failed')
                                        <span class="badge bg-warning-subtle text-warning badge-ios">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Fallido
                                        </span>
                                    @elseif($login->status === 'blocked')
                                        <span class="badge bg-danger-subtle text-danger badge-ios">
                                            <i class="bi bi-shield-x me-1"></i> Bloqueado
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary badge-ios">{{ ucfirst($login->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-laptop me-2 text-muted"></i>
                                        <div>
                                            <span class="text-dark small fw-medium">{{ $login->simplified_agent }}</span>
                                            <br>
                                            <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;" title="{{ $login->user_agent }}">
                                                {{ $login->user_agent }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <span class="badge bg-light text-dark font-monospace border">{{ $login->ip_address ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x fs-2 d-block mb-2 text-secondary"></i>
                                    No se encontraron registros de accesos con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($loginLogs->hasPages())
                <div class="p-3 border-top">
                    {{ $loginLogs->links() }}
                </div>
            @endif
        </div>
    @endif

</div>

<!-- Modal for Inspecting Detailed Audit Diff -->
<div class="modal fade" id="auditDiffModal" tabindex="-1" aria-labelledby="auditDiffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalDiffTitle">Detalle de Modificación</h5>
                    <small class="text-muted" id="modalDiffMeta">Operador y Fecha</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="modalDiffTable">
                        <thead class="bg-light">
                            <tr class="text-muted" style="font-size: 0.8rem;">
                                <th style="width: 30%;">CAMPO / ATRIBUTO</th>
                                <th style="width: 35%;" class="text-danger bg-danger bg-opacity-10">VALOR ANTERIOR</th>
                                <th style="width: 35%;" class="text-success bg-success bg-opacity-10">VALOR NUEVO</th>
                            </tr>
                        </thead>
                        <tbody id="modalDiffBody">
                            <!-- Rows rendered dynamically by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const diffModal = document.getElementById('auditDiffModal');
    if (!diffModal) return;

    diffModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (!button) return;

        const title = button.getAttribute('data-title') || 'Detalle del Evento';
        const operator = button.getAttribute('data-operator') || 'Desconocido';
        const date = button.getAttribute('data-date') || '';
        const action = button.getAttribute('data-action') || '';
        
        let oldVal = {};
        let newVal = {};
        
        try {
            oldVal = JSON.parse(button.getAttribute('data-old') || '{}');
        } catch(e) { oldVal = {}; }
        
        try {
            newVal = JSON.parse(button.getAttribute('data-new') || '{}');
        } catch(e) { newVal = {}; }

        document.getElementById('modalDiffTitle').innerText = title;
        document.getElementById('modalDiffMeta').innerText = `Realizado por: ${operator} — Fecha: ${date}`;

        const tbody = document.getElementById('modalDiffBody');
        tbody.innerHTML = '';

        const allKeys = Array.from(new Set([...Object.keys(oldVal || {}), ...Object.keys(newVal || {})]));

        if (allKeys.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">No hay valores detallados disponibles.</td></tr>';
            return;
        }

        allKeys.forEach(key => {
            const tr = document.createElement('tr');
            
            const beforeVal = oldVal && oldVal[key] !== undefined ? (typeof oldVal[key] === 'object' ? JSON.stringify(oldVal[key]) : String(oldVal[key])) : '<span class="text-muted fst-italic">[vacío]</span>';
            const afterVal = newVal && newVal[key] !== undefined ? (typeof newVal[key] === 'object' ? JSON.stringify(newVal[key]) : String(newVal[key])) : '<span class="text-muted fst-italic">[vacío]</span>';
            
            tr.innerHTML = `
                <td class="fw-semibold font-monospace small text-dark">${key}</td>
                <td class="small font-monospace text-break bg-danger bg-opacity-10">${beforeVal}</td>
                <td class="small font-monospace text-break bg-success bg-opacity-10">${afterVal}</td>
            `;
            tbody.appendChild(tr);
        });
    });
});
</script>
@endpush
@endsection
