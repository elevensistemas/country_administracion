@extends('layouts.app')

@section('title', 'Comunicaciones')
@section('page_title', 'Centro de Comunicaciones')

@section('content')
<div class="row">
    <!-- Sent History (Left) -->
    <div class="col-lg-8 mb-4">
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Comunicaciones Enviadas</h5>
                <a href="{{ route('admin.comms.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-send-plus-fill me-2"></i>Nueva Comunicación</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">FECHA</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ASUNTO / DETALLES</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">ALCANCE</th>
                            <th class="text-muted text-center" style="font-size: 0.85rem; font-weight: 600; width: 12%;">DESTINATARIOS</th>
                            <th class="text-muted text-center" style="font-size: 0.85rem; font-weight: 600; width: 10%;">APERTURAS</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 10%;">VER</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comms as $comm)
                            <tr class="border-bottom border-ios">
                                <td style="font-size: 0.9rem;">
                                    {{ $comm->sent_at ? $comm->sent_at->format('d/m/Y H:i') : '' }}
                                </td>
                                <td>
                                    <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $comm->subject }}</h6>
                                    <small class="text-muted" style="font-size: 0.8rem;">Título interno: {{ $comm->title }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary badge-ios text-uppercase" style="font-size: 0.65rem;">
                                        {{ str_replace('_', ' ', $comm->target_type) }}
                                    </span>
                                </td>
                                <td class="text-center fw-semibold">{{ $comm->recipients_count }}</td>
                                <td class="text-center text-success fw-bold">
                                    @php
                                        $openRate = $comm->recipients_count > 0 ? round(($comm->opened_count / $comm->recipients_count) * 100) : 0;
                                    @endphp
                                    {{ $openRate }}%
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.comms.show', $comm) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary">
                                        <i class="bi bi-graph-up"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-envelope-open text-muted fs-1 d-block mb-3"></i>
                                    <span class="text-muted">No se registran envíos masivos.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $comms->links() }}
            </div>
        </div>
    </div>

    <!-- Configuration & Templates (Right) -->
    <div class="col-lg-4">
        <!-- SMTP Status Card -->
        <div class="ios-card bg-body-secondary border-0 mb-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-shield-check text-success me-2"></i>Estado SMTP de Envíos</h6>
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-success text-white rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill"></i> Activo</span>
                <span class="text-muted" style="font-size: 0.8rem;">mail.laranita.com:587</span>
            </div>
            <p class="text-muted m-0" style="font-size: 0.85rem;">La cola de envíos masivos y las notificaciones por correo están operando con normalidad.</p>
        </div>

        <!-- Templates Card -->
        <div class="ios-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-code-fill text-success me-2"></i>Plantillas HTML</h6>
            <p class="text-muted" style="font-size: 0.85rem;">Plantillas base cargadas para envíos de alertas, recordatorios y boletines.</p>
            
            <div class="list-group list-group-flush">
                @foreach($templates as $temp)
                    <div class="list-group-item bg-transparent border-0 px-0 py-2 border-bottom border-ios" style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-success">{{ $temp->name }}</strong>
                            <span class="badge bg-secondary-subtle text-secondary badge-ios" style="font-size: 0.6rem; text-transform: uppercase;">{{ $temp->type }}</span>
                        </div>
                        <span class="text-muted" style="font-size: 0.8rem;">Asunto: {{ $temp->subject_template }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
