@extends('layouts.app')

@section('title', 'Reservas')
@section('page_title', 'Administración de Reservas')

@section('content')
<!-- Search & Filters -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.reservations.index') }}" class="row g-3 align-items-center">
        <div class="col-md-4">
            <label for="common_area_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Filtrar por Espacio</label>
            <select name="common_area_id" id="common_area_id" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todos los Espacios</option>
                @foreach($commonAreas as $area)
                    <option value="{{ $area->id }}" {{ request('common_area_id') == $area->id ? 'selected' : '' }}>
                        {{ $area->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="status" class="form-label fw-semibold" style="font-size: 0.85rem;">Filtrar por Estado</label>
            <select name="status" id="status" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todos los Estados</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazada</option>
                <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Cancelada</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Finalizada</option>
            </select>
        </div>

        <div class="col-md-4 d-grid mt-md-4 pt-md-2">
            <a href="{{ route('admin.reservations.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar Filtros</a>
        </div>
    </form>
</div>

<!-- Reservations List Card -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Registro de Reservas</h5>
        <span class="badge bg-secondary-subtle text-secondary badge-ios">Total: {{ $reservations->total() }}</span>
    </div>

    <!-- Desktop View Table -->
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">LOTE / UF</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESPACIO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">HORARIO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">COSTO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 25%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $res)
                    <tr class="border-bottom border-ios">
                        <td class="fw-bold">
                            Lote {{ $res->lot->number }}
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Vecino: {{ $res->lot->owner ? $res->lot->owner->full_name : 'N/C' }}</small>
                        </td>
                        <td>
                            {{ $res->commonArea->name }}
                            @if($res->is_exclusive)
                                <span class="badge bg-danger-subtle text-danger badge-ios d-block mt-1" style="font-size: 0.7rem; width: fit-content;"><i class="bi bi-star-fill me-1"></i>Exclusivo</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary badge-ios d-block mt-1" style="font-size: 0.7rem; width: fit-content;">Común</span>
                            @endif
                        </td>
                        <td>{{ $res->reservation_date->format('d/m/Y') }}</td>
                        <td>{{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }} hs</td>
                        <td class="text-end fw-semibold">
                            @if($res->is_exclusive && $res->price == 0 && $res->status == 'pending')
                                <span class="text-danger" style="font-size: 0.85rem;">A Presupuestar</span>
                            @else
                                ${{ number_format($res->price, 2, ',', '.') }}
                            @endif
                            @if($res->charge_to_expenses && $res->price > 0)
                                <small class="text-success d-block" style="font-size: 0.7rem;"><i class="bi bi-file-earmark-plus"></i> Carga Expensas</small>
                            @elseif($res->price > 0)
                                <small class="text-muted d-block" style="font-size: 0.7rem;">Pago Directo</small>
                            @endif
                        </td>
                        <td>
                            @if($res->status === 'pending')
                                <span class="badge bg-warning text-dark badge-ios"><i class="bi bi-clock-fill me-1"></i>Pendiente</span>
                            @elseif($res->status === 'confirmed')
                                <span class="badge bg-success text-white badge-ios"><i class="bi bi-check-circle-fill me-1"></i>Confirmada</span>
                            @elseif($res->status === 'rejected')
                                <span class="badge bg-danger text-white badge-ios"><i class="bi bi-x-circle-fill me-1"></i>Rechazada</span>
                            @elseif($res->status === 'canceled')
                                <span class="badge bg-secondary text-white badge-ios"><i class="bi bi-slash-circle-fill me-1"></i>Cancelada</span>
                            @else
                                <span class="badge bg-info text-dark badge-ios"><i class="bi bi-flag-fill me-1"></i>Finalizada</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 align-items-center">
                                @if($res->status === 'pending')
                                    <form action="{{ route('admin.reservations.status', $res) }}" method="POST" class="d-inline-flex gap-1 align-items-center m-0">
                                        @csrf
                                        <input type="hidden" name="status" value="confirmed">
                                        @if($res->is_exclusive)
                                            <div class="input-group input-group-sm" style="width: 110px;">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="price" class="form-control form-control-ios" step="0.01" min="0" required placeholder="Precio">
                                            </div>
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Confirmar Reserva">
                                            <i class="bi bi-check-lg"></i> Confirmar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reservations.status', $res) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger" title="Rechazar Reserva">
                                            <i class="bi bi-x-lg"></i> Rechazar
                                        </button>
                                    </form>
                                @elseif($res->status === 'confirmed')
                                    <form action="{{ route('admin.reservations.status', $res) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-info" title="Finalizar Reserva">
                                            <i class="bi bi-flag"></i> Completar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reservations.status', $res) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="canceled">
                                        <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-secondary" title="Cancelar Reserva">
                                            <i class="bi bi-slash-circle"></i> Cancelar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted" style="font-size: 0.85rem;">Sin acciones</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No se encontraron reservas registradas.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile View stacked list cards -->
    <div class="d-block d-md-none">
        @forelse($reservations as $res)
            <div class="p-3 border-bottom border-ios mb-3 rounded-4 bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold m-0" style="font-size: 1.05rem;">Lote {{ $res->lot->number }}</h6>
                        <small class="text-muted">
                            {{ $res->commonArea->name }}
                            @if($res->is_exclusive)
                                <span class="badge bg-danger-subtle text-danger badge-ios ms-1" style="font-size: 0.65rem;"><i class="bi bi-star-fill me-1"></i>Exclusivo</span>
                            @endif
                        </small>
                    </div>
                    @if($res->status === 'pending')
                        <span class="badge bg-warning text-dark badge-ios">Pendiente</span>
                    @elseif($res->status === 'confirmed')
                        <span class="badge bg-success text-white badge-ios">Confirmada</span>
                    @elseif($res->status === 'rejected')
                        <span class="badge bg-danger text-white badge-ios">Rechazada</span>
                    @elseif($res->status === 'canceled')
                        <span class="badge bg-secondary text-white badge-ios">Cancelada</span>
                    @else
                        <span class="badge bg-info text-dark badge-ios">Finalizada</span>
                    @endif
                </div>

                <div class="my-2" style="font-size: 0.85rem; line-height: 1.5;">
                    <div class="mb-1"><strong>Vecino:</strong> {{ $res->lot->owner ? $res->lot->owner->full_name : 'N/C' }}</div>
                    <div class="mb-1"><strong>Fecha:</strong> {{ $res->reservation_date->format('d/m/Y') }}</div>
                    <div class="mb-1"><strong>Horario:</strong> {{ substr($res->start_time, 0, 5) }} - {{ substr($res->end_time, 0, 5) }} hs</div>
                    <div>
                        <strong>Precio:</strong> 
                        @if($res->is_exclusive && $res->price == 0 && $res->status == 'pending')
                            <span class="text-danger fw-bold">A Presupuestar</span>
                        @else
                            <span class="fw-bold text-dark">${{ number_format($res->price, 2, ',', '.') }}</span>
                        @endif
                        @if($res->charge_to_expenses && $res->price > 0)
                            <span class="badge bg-success-subtle text-success ms-1" style="font-size: 0.7rem;"><i class="bi bi-file-earmark-plus"></i> Cargado a Expensas</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top border-ios align-items-center">
                    @if($res->status === 'pending')
                        <form action="{{ route('admin.reservations.status', $res) }}" method="POST" class="d-inline-flex gap-1 align-items-center m-0">
                            @csrf
                            <input type="hidden" name="status" value="confirmed">
                            @if($res->is_exclusive)
                                <div class="input-group input-group-sm" style="width: 100px;">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" class="form-control form-control-ios" step="0.01" min="0" required placeholder="Precio">
                                </div>
                            @endif
                            <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-success px-3 py-2">
                                <i class="bi bi-check-lg me-1"></i> Confirmar
                            </button>
                        </form>
                        <form action="{{ route('admin.reservations.status', $res) }}" method="POST" class="d-inline m-0">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger px-3 py-2">
                                <i class="bi bi-x-lg me-1"></i> Rechazar
                            </button>
                        </form>
                    @elseif($res->status === 'confirmed')
                        <form action="{{ route('admin.reservations.status', $res) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-info px-3 py-2">
                                <i class="bi bi-flag me-1"></i> Completar
                            </button>
                        </form>
                        <form action="{{ route('admin.reservations.status', $res) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="canceled">
                            <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-secondary px-3 py-2">
                                <i class="bi bi-slash-circle me-1"></i> Cancelar
                            </button>
                        </form>
                    @else
                        <span class="text-muted" style="font-size: 0.85rem;">No hay acciones disponibles</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                No se encontraron reservas registradas.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $reservations->links() }}
    </div>
</div>
@endsection
