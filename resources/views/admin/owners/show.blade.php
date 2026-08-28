@extends('layouts.app')

@section('title', 'Detalle de Propietario')
@section('page_title', 'Ficha del Propietario')

@section('content')
<div class="row">
    <!-- Owner Overview Left Card -->
    <div class="col-lg-4 mb-4">
        <div class="ios-card text-center py-4">
            <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-weight: 600; font-size: 2rem;">
                {{ substr($owner->name, 0, 1) }}{{ substr($owner->last_name, 0, 1) }}
            </div>
            <h4 class="fw-bold mb-1">{{ $owner->full_name }}</h4>
            @if($owner->business_name)
                <span class="text-muted d-block mb-2" style="font-size: 0.85rem;">{{ $owner->business_name }}</span>
            @endif
            
            <div class="d-flex justify-content-center gap-2 mb-3">
                @if($owner->status === 'active')
                    <span class="badge bg-success-subtle text-success badge-ios">Activo</span>
                @else
                    <span class="badge bg-danger-subtle text-danger badge-ios">Inactivo</span>
                @endif
                <span class="badge bg-secondary-subtle text-secondary badge-ios text-uppercase">{{ $owner->preferred_channel }}</span>
            </div>
            
            <hr class="border-ios my-3">
            
            <!-- Quick Contact info -->
            <div class="text-start px-2">
                <div class="mb-2">
                    <span class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-envelope-fill me-1"></i> Email Principal</span>
                    <span class="fw-semibold" style="font-size: 0.9rem;">{{ $owner->email }}</span>
                </div>
                @if($owner->phone)
                    <div class="mb-2">
                        <span class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-whatsapp me-1"></i> WhatsApp</span>
                        <span class="fw-semibold" style="font-size: 0.9rem;">{{ $owner->phone }}</span>
                    </div>
                @endif
                @if($owner->dni)
                    <div class="mb-2">
                        <span class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-card-text me-1"></i> DNI</span>
                        <span class="fw-semibold" style="font-size: 0.9rem;">{{ $owner->dni }}</span>
                    </div>
                @endif
                @if($owner->cuit)
                    <div class="mb-2">
                        <span class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-briefcase-fill me-1"></i> CUIT</span>
                        <span class="fw-semibold" style="font-size: 0.9rem;">{{ $owner->cuit }}</span>
                    </div>
                @endif
            </div>

            <div class="d-grid gap-2 mt-4 px-2">
                <a href="{{ route('admin.owners.edit', $owner) }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-pencil-fill me-2"></i>Editar Ficha</a>
                <a href="{{ route('admin.owners.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver a Lista</a>
            </div>
        </div>
    </div>

    <!-- Multi-tab Details Right Section -->
    <div class="col-lg-8">
        <!-- Navigation Tabs -->
        <ul class="nav nav-pills bg-body-secondary p-1 rounded-3 mb-4 d-inline-flex w-100" id="ownerTabs" role="tablist">
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 active border-0 rounded-2 text-capitalize py-2" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Ficha</button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 border-0 rounded-2 text-capitalize py-2" id="lots-tab" data-bs-toggle="tab" data-bs-target="#lots" type="button" role="tab" aria-controls="lots" aria-selected="false">Lotes ({{ $owner->lots->count() }})</button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 border-0 rounded-2 text-capitalize py-2" id="accounting-tab" data-bs-toggle="tab" data-bs-target="#accounting" type="button" role="tab" aria-controls="accounting" aria-selected="false">Cta. Cte.</button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 border-0 rounded-2 text-capitalize py-2" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">Pagos</button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100 border-0 rounded-2 text-capitalize py-2" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets" type="button" role="tab" aria-controls="tickets" aria-selected="false">Reclamos</button>
            </li>
        </ul>

        <div class="tab-content" id="ownerTabsContent">
            <!-- TAB 1: Profile & System Users -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="ios-card">
                    <h5 class="fw-bold mb-4">Información de Contacto Adicional</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted d-block" style="font-size: 0.8rem;">Email Alternativo</span>
                            <span class="fw-semibold">{{ $owner->email_alternate ?? 'No registrado' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block" style="font-size: 0.8rem;">Teléfono Alternativo</span>
                            <span class="fw-semibold">{{ $owner->phone_alternate ?? 'No registrado' }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block" style="font-size: 0.8rem;">Domicilio Fuera del Barrio</span>
                            <span class="fw-semibold">{{ $owner->address ?? 'No registrado' }}</span>
                        </div>
                        @if($owner->notes)
                            <div class="col-12">
                                <span class="text-muted d-block" style="font-size: 0.8rem;">Notas Administrativas</span>
                                <div class="bg-body-secondary p-3 rounded-4 mt-2" style="font-size: 0.9rem;">
                                    {{ $owner->notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ios-card">
                    <h5 class="fw-bold mb-4">Usuarios del Sistema Vinculados</h5>
                    <p class="text-muted" style="font-size: 0.85rem;">Cuentas de usuario que ingresan al portal asociadas a este propietario.</p>
                    
                    <div class="list-group list-group-flush">
                        @forelse($associatedUsers as $u)
                            <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center border-bottom border-ios">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: 600;">
                                        {{ substr($u->name, 0, 1) }}{{ substr($u->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold m-0" style="font-size: 0.95rem;">
                                            <a href="{{ route('admin.users.edit', $u) }}" class="text-decoration-none text-success">
                                                {{ $u->full_name }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $u->email }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-secondary-subtle text-secondary badge-ios mb-1">{{ $u->relationship_type }}</span>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Último ingreso: {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Nunca ingresó' }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-person-slash text-muted fs-1 d-block mb-2"></i>
                                <span class="text-muted">No existen usuarios del sistema creados para este propietario.</span>
                                <div class="mt-3">
                                    <a href="{{ route('admin.users.create') }}?email={{ $owner->email }}&name={{ $owner->name }}&last_name={{ $owner->last_name }}" class="btn btn-sm btn-ios btn-ios-primary">Crear Usuario del Sistema</a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- TAB 2: Lots and Tenants -->
            <div class="tab-pane fade" id="lots" role="tabpanel" aria-labelledby="lots-tab">
                <div class="ios-card">
                    <h5 class="fw-bold mb-4">Lotes y Unidades Funcionales Relacionadas</h5>
                    
                    @forelse($owner->lots as $lot)
                        <div class="border-ios p-3 rounded-4 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold m-0" style="font-size: 1.1rem;">Lote {{ $lot->number }}</h6>
                                    <small class="text-muted">{{ $lot->internal_address }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success-subtle text-success badge-ios text-capitalize">{{ str_replace('_', ' ', $lot->status) }}</span>
                                    <a href="{{ route('admin.lots.history', $lot) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success ms-2"><i class="bi bi-clock-history"></i> Ver Historia Clínica</a>
                                </div>
                            </div>

                            <div class="row g-3 bg-body-secondary p-3 rounded-3 mt-1">
                                <div class="col-sm-6 col-md-3">
                                    <span class="text-muted d-block" style="font-size: 0.8rem;">Código Lote</span>
                                    <span class="fw-semibold">{{ $lot->code }}</span>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <span class="text-muted d-block" style="font-size: 0.8rem;">Saldo Actual Lote</span>
                                    <span class="fw-bold {{ $lot->balance > 0 ? 'text-danger' : ($lot->balance < 0 ? 'text-success' : '') }}">
                                        ${{ number_format($lot->balance, 2, ',', '.') }}
                                    </span>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <span class="text-muted d-block" style="font-size: 0.8rem;">Inquilino Ocupante</span>
                                    <span class="fw-semibold">
                                        @if($lot->tenant)
                                            <i class="bi bi-person-workspace text-primary me-1"></i> {{ $lot->tenant->full_name }} <small class="text-muted">({{ $lot->tenant->phone }})</small>
                                        @else
                                            <span class="text-muted">Sin inquilino (Habitado por propietario)</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-house-dash text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">Este propietario no tiene lotes asociados actualmente.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB 3: Cuenta Corriente -->
            <div class="tab-pane fade" id="accounting" role="tabpanel" aria-labelledby="accounting-tab">
                <div class="ios-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0">Movimientos de Cuenta Corriente</h5>
                        <div class="text-end">
                            <small class="text-muted d-block">Saldo Consolidado</small>
                            <h4 class="fw-bold m-0 {{ $owner->lots->sum('balance') > 0 ? 'text-danger' : ($owner->lots->sum('balance') < 0 ? 'text-success' : '') }}">
                                ${{ number_format($owner->lots->sum('balance'), 2, ',', '.') }}
                            </h4>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA</th>
                                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">UNIDAD</th>
                                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DETALLE</th>
                                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">DEBITO</th>
                                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">CREDITO</th>
                                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">SALDO ACUM.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $mov)
                                    <tr class="border-bottom border-ios">
                                        <td style="font-size: 0.9rem;">{{ $mov->date->format('d/m/Y') }}</td>
                                        <td class="fw-semibold">UF Lote {{ $mov->functionalUnit->lot->number }}</td>
                                        <td style="font-size: 0.9rem;">
                                            {{ $mov->description }}
                                            @if($mov->related_model_type)
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">Polimórfico: {{ class_basename($mov->related_model_type) }} #{{ $mov->related_model_id }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-danger" style="font-size: 0.9rem;">
                                            {{ $mov->type === 'debit' ? '$' . number_format($mov->amount, 2, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-end fw-bold text-success" style="font-size: 0.9rem;">
                                            {{ $mov->type === 'credit' ? '$' . number_format($mov->amount, 2, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-end fw-bold {{ $mov->balance_after > 0 ? 'text-danger' : ($mov->balance_after < 0 ? 'text-success' : '') }}" style="font-size: 0.9rem;">
                                            ${{ number_format($mov->balance_after, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-wallet2 text-muted fs-2 d-block mb-2"></i>
                                            <span class="text-muted">No se registran movimientos en la cuenta corriente.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $movements->links() }}
                    </div>
                </div>
            </div>

            <!-- TAB 4: Pagos -->
            <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                <div class="ios-card">
                    <h5 class="fw-bold mb-4">Pagos Informados</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="border-bottom border-ios">
                                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA DEP.</th>
                                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">LOTE</th>
                                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DETALLES</th>
                                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">IMPORTE</th>
                                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600;">FICHA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $pay)
                                    <tr class="border-bottom border-ios">
                                        <td style="font-size: 0.9rem;">{{ $pay->payment_date->format('d/m/Y') }}</td>
                                        <td class="fw-semibold">Lote {{ $pay->lot->number }}</td>
                                        <td style="font-size: 0.85rem;">
                                            <span class="d-block text-capitalize">{{ $pay->payment_method }} • {{ $pay->bank ?? 'S/B' }}</span>
                                            <small class="text-muted">Operación: {{ $pay->operation_number ?? 'S/N' }}</small>
                                        </td>
                                        <td class="text-end fw-bold text-success" style="font-size: 0.9rem;">
                                            ${{ number_format($pay->amount, 2, ',', '.') }}
                                        </td>
                                        <td>
                                            @if($pay->status === 'approved')
                                                <span class="badge bg-success-subtle text-success badge-ios">Aprobado</span>
                                            @elseif($pay->status === 'pending')
                                                <span class="badge bg-warning-subtle text-warning badge-ios">Pendiente</span>
                                            @elseif($pay->status === 'rejected')
                                                <span class="badge bg-danger-subtle text-danger badge-ios">Rechazado</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary badge-ios">{{ $pay->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.payments.show', $pay) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-cash text-muted fs-2 d-block mb-2"></i>
                                            <span class="text-muted">No se registran pagos informados.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>

            <!-- TAB 5: Reclamos -->
            <div class="tab-pane fade" id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
                <div class="ios-card">
                    <h5 class="fw-bold mb-4">Historial de Reclamos y Sugerencias</h5>
                    
                    <div class="list-group list-group-flush">
                        @forelse($tickets as $ticket)
                            <div class="list-group-item bg-transparent border-0 px-0 py-3 border-bottom border-ios">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="fw-bold m-0" style="font-size: 0.95rem;">
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-decoration-none text-success">
                                            {{ $ticket->title }}
                                        </a>
                                    </h6>
                                    <span class="badge bg-secondary text-capitalize" style="font-size: 0.75rem;">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                </div>
                                <p class="text-muted m-0 text-truncate" style="font-size: 0.85rem; max-width: 90%;">
                                    {{ $ticket->description }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted" style="font-size: 0.8rem;">
                                        Lote {{ $ticket->lot->number }} • Categoría: {{ $ticket->category->display_name }}
                                    </small>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-chat-left-text text-muted fs-2 d-block mb-2"></i>
                                <span class="text-muted">No existen reclamos registrados.</span>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
