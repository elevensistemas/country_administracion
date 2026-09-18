@extends('layouts.app')

@section('title', 'Comunicaciones')
@section('page_title', 'Centro de Comunicaciones y Envíos Masivos')

@section('content')
<div class="row">
    <!-- Sent History (Left) -->
    <div class="col-lg-8 mb-4">
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold m-0 text-body">Comunicaciones Enviadas</h5>
                    <small class="text-body-secondary">Historial de campañas y notificaciones emitidas</small>
                </div>
                <a href="{{ route('admin.comms.create') }}" class="btn btn-primary rounded-pill px-3">
                    <i class="bi bi-send-plus-fill me-2"></i>Nueva Comunicación
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-tertiary">
                        <tr class="border-bottom text-muted" style="font-size: 0.8rem; font-weight: 600;">
                            <th class="ps-3 py-3" style="width: 18%;">FECHA / HORA</th>
                            <th>ASUNTO / DETALLES</th>
                            <th style="width: 15%;">ALCANCE</th>
                            <th class="text-center" style="width: 12%;">DESTINATARIOS</th>
                            <th class="text-center" style="width: 12%;">APERTURAS</th>
                            <th class="text-end pe-3" style="width: 10%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comms as $comm)
                            @php
                                $dtComm = $comm->sent_at ? $comm->sent_at->timezone('America/Argentina/Buenos_Aires') : ($comm->created_at ? $comm->created_at->timezone('America/Argentina/Buenos_Aires') : null);
                            @endphp
                            <tr class="border-bottom">
                                <td class="ps-3 py-2" style="font-size: 0.85rem;">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-body">
                                            <i class="bi bi-calendar3 me-1 text-primary opacity-75"></i>{{ $dtComm ? $dtComm->format('d/m/Y') : '-' }}
                                        </span>
                                        <span class="text-body-secondary font-monospace" style="font-size: 0.8rem;">
                                            <i class="bi bi-clock me-1 text-info opacity-75"></i>{{ $dtComm ? $dtComm->format('H:i:s \h\s') : '' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="fw-bold m-0 text-body" style="font-size: 0.95rem;">{{ $comm->title }}</h6>
                                    <small class="text-body-secondary" style="font-size: 0.8rem;">
                                        <i class="bi bi-person me-1"></i>Enviado por: {{ $comm->sender?->full_name ?? 'Administración' }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary badge-ios text-uppercase" style="font-size: 0.68rem;">
                                        {{ str_replace('_', ' ', $comm->target_type) }}
                                    </span>
                                </td>
                                <td class="text-center fw-semibold text-body">{{ $comm->recipients_count }}</td>
                                <td class="text-center">
                                    @php
                                        $openRate = $comm->recipients_count > 0 ? round(($comm->opened_count / $comm->recipients_count) * 100) : 0;
                                    @endphp
                                    <span class="badge bg-success-subtle text-success badge-ios font-monospace fw-bold">
                                        {{ $openRate }}%
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.comms.show', $comm) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Ver Analítica y Registro">
                                        <i class="bi bi-graph-up me-1"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-envelope-open fs-1 d-block mb-3 text-secondary"></i>
                                    <span>No se registran envíos masivos de comunicaciones.</span>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.comms.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                            <i class="bi bi-send-fill me-1"></i> Redactar Primera Comunicación
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($comms->hasPages())
                <div class="p-3 border-top">
                    {{ $comms->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Configuration & Templates (Right) -->
    <div class="col-lg-4">
        <!-- SMTP Status Card -->
        <div class="ios-card mb-4">
            <h6 class="fw-bold text-body mb-3"><i class="bi bi-shield-check text-success me-2"></i>Estado SMTP de Envíos</h6>
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-success text-white rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill"></i> Activo</span>
                <span class="text-body-secondary font-monospace" style="font-size: 0.8rem;">mail.laranita.com:587</span>
            </div>
            <p class="text-body-secondary m-0" style="font-size: 0.85rem;">
                La cola de envíos masivos y las notificaciones por correo electrónico están operando correctamente para todos los residentes.
            </p>
        </div>

        <!-- Templates Card -->
        <div class="ios-card">
            <h6 class="fw-bold text-body mb-3"><i class="bi bi-file-earmark-code-fill text-success me-2"></i>Plantillas Disponibles</h6>
            <p class="text-body-secondary" style="font-size: 0.85rem;">Formatos base precargados para avisos, asambleas y recordatorios.</p>
            
            <div class="list-group list-group-flush">
                @forelse($templates as $temp)
                    <div class="list-group-item bg-transparent px-0 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-success">{{ $temp->name }}</strong>
                            <span class="badge bg-secondary-subtle text-secondary badge-ios font-monospace" style="font-size: 0.65rem; text-transform: uppercase;">{{ $temp->channels ?? 'Email' }}</span>
                        </div>
                        <span class="text-body-secondary small d-block">{{ $temp->subject ?? 'Sin asunto definido' }}</span>
                    </div>
                @empty
                    <div class="text-muted small py-3 text-center">
                        No hay plantillas HTML adicionales cargadas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
