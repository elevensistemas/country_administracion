@extends('layouts.app')

@section('title', 'Detalle Campaña')
@section('page_title', 'Reporte y Métrica de Envíos')

@section('content')
<!-- Analytical Counters -->
<div class="row g-3 mb-4">
    <!-- Total Recipients -->
    <div class="col-6 col-md-3">
        <div class="ios-card text-center py-3">
            <span class="text-body-secondary d-block small fw-medium">TOTAL DESTINATARIOS</span>
            <h2 class="fw-bold m-0 text-body">{{ $totalRecipients }}</h2>
        </div>
    </div>
    <!-- Delivered -->
    <div class="col-6 col-md-3">
        <div class="ios-card bg-success-subtle text-center py-3">
            <span class="text-success d-block small fw-medium">ENTREGADOS</span>
            <h2 class="fw-bold m-0 text-success">{{ $delivered }}</h2>
        </div>
    </div>
    <!-- Opened -->
    <div class="col-6 col-md-3">
        <div class="ios-card bg-info-subtle text-center py-3">
            <span class="text-info d-block small fw-medium">APERTURAS</span>
            <h2 class="fw-bold m-0 text-info">{{ $opened }}</h2>
        </div>
    </div>
    <!-- Bounces / Failed -->
    <div class="col-6 col-md-3">
        <div class="ios-card bg-danger-subtle text-center py-3">
            <span class="text-danger d-block small fw-medium">FALLIDOS</span>
            <h2 class="fw-bold m-0 text-danger">{{ $failed }}</h2>
        </div>
    </div>
</div>

<div class="row">
    <!-- Campaign Details Card -->
    <div class="col-lg-4 mb-4">
        <div class="ios-card">
            <h5 class="fw-bold mb-4 text-body">Detalle de la Comunicación</h5>
            <table class="table table-borderless align-middle m-0" style="font-size: 0.9rem;">
                <tr>
                    <td class="text-body-secondary py-2" style="width: 40%;">Título / Asunto:</td>
                    <td class="fw-bold text-body py-2">{{ $communication->title }}</td>
                </tr>
                <tr>
                    <td class="text-body-secondary py-2">Canal:</td>
                    <td class="fw-semibold text-body py-2 text-uppercase">{{ $communication->channels ?? 'Email (SMTP)' }}</td>
                </tr>
                <tr>
                    <td class="text-body-secondary py-2">Fecha Envío:</td>
                    <td class="fw-semibold text-body py-2">{{ $communication->sent_at ? $communication->sent_at->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i \h\s') : 'No enviado' }}</td>
                </tr>
                <tr>
                    <td class="text-body-secondary py-2">Emisor:</td>
                    <td class="fw-semibold text-body py-2">{{ $communication->sender?->full_name ?? 'Administración' }}</td>
                </tr>
                <tr>
                    <td class="text-body-secondary py-2">Alcance:</td>
                    <td class="fw-semibold text-body py-2 text-uppercase">
                        <span class="badge bg-secondary-subtle text-secondary">{{ str_replace('_', ' ', $communication->target_type) }}</span>
                    </td>
                </tr>
            </table>

            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold text-body small mb-2">Mensaje Enviado:</h6>
                <div class="p-3 bg-body-secondary rounded-3 small text-body font-monospace" style="white-space: pre-wrap;">{{ $communication->content }}</div>
            </div>

            <div class="d-grid mt-4">
                <a href="{{ route('admin.comms.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Listado
                </a>
            </div>
        </div>
    </div>

    <!-- Recipient Logs list -->
    <div class="col-lg-8">
        <div class="ios-card">
            <h5 class="fw-bold mb-4 text-body">Registro Detallado por Destinatario</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-tertiary">
                        <tr class="border-bottom text-muted" style="font-size: 0.8rem; font-weight: 600;">
                            <th class="ps-3 py-3">DESTINATARIO</th>
                            <th>EMAIL</th>
                            <th class="text-center">ESTADO</th>
                            <th class="pe-3">DETALLE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipients as $rec)
                            @php
                                $delivery = $rec->deliveries->first();
                            @endphp
                            <tr class="border-bottom">
                                <td class="ps-3 fw-bold text-body">{{ $rec->user ? $rec->user->full_name : ($rec->email ?? 'Usuario') }}</td>
                                <td class="text-body-secondary font-monospace" style="font-size: 0.85rem;">{{ $rec->email }}</td>
                                <td class="text-center">
                                    @if(!$delivery)
                                        <span class="badge bg-secondary-subtle text-secondary badge-ios">En Cola</span>
                                    @elseif($delivery->status === 'opened')
                                        <span class="badge bg-success text-white badge-ios"><i class="bi bi-eye-fill me-1"></i> Abierto</span>
                                    @elseif($delivery->status === 'delivered')
                                        <span class="badge bg-success-subtle text-success badge-ios"><i class="bi bi-check-circle-fill me-1"></i> Entregado</span>
                                    @else
                                        <span class="badge bg-danger text-white badge-ios"><i class="bi bi-x-circle-fill me-1"></i> Fallido</span>
                                    @endif
                                </td>
                                <td class="pe-3" style="font-size: 0.85rem;">
                                    @if($delivery)
                                        @if($delivery->status === 'opened')
                                            <span class="text-success"><i class="bi bi-clock-history me-1"></i>Abierto el {{ $delivery->read_at ? $delivery->read_at->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y H:i') : '' }}</span>
                                        @elseif($delivery->status === 'failed')
                                            <span class="text-danger fw-semibold">{{ $delivery->error_message }}</span>
                                        @else
                                            <span class="text-body-secondary">Entregado en servidor de correo</span>
                                        @endif
                                    @else
                                        <span class="text-body-secondary">Procesando...</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No hay destinatarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($recipients->hasPages())
                <div class="p-3 border-top">
                    {{ $recipients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
