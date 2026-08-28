@extends('layouts.app')

@section('title', 'Detalle Campaña')
@section('page_title', 'Reporte de Envíos')

@section('content')
<!-- Analytical Counters -->
<div class="row g-4 mb-4">
    <!-- Total Recipients -->
    <div class="col-md-3">
        <div class="ios-card bg-body-secondary border-0 text-center py-3">
            <span class="text-muted d-block" style="font-size: 0.8rem;">TOTAL ENVIADOS</span>
            <h2 class="fw-bold m-0 text-dark">{{ $totalRecipients }}</h2>
        </div>
    </div>
    <!-- Delivered -->
    <div class="col-md-3">
        <div class="ios-card bg-success-subtle text-success text-center py-3">
            <span class="text-success d-block" style="font-size: 0.8rem;">ENTREGADOS</span>
            <h2 class="fw-bold m-0">{{ $delivered }}</h2>
        </div>
    </div>
    <!-- Opened -->
    <div class="col-md-3">
        <div class="ios-card bg-info-subtle text-info text-center py-3">
            <span class="text-info d-block" style="font-size: 0.8rem;">APERTURAS</span>
            <h2 class="fw-bold m-0">{{ $opened }}</h2>
        </div>
    </div>
    <!-- Bounces / Failed -->
    <div class="col-md-3">
        <div class="ios-card bg-danger-subtle text-danger text-center py-3">
            <span class="text-danger d-block" style="font-size: 0.8rem;">FALLIDOS (BOUNCES)</span>
            <h2 class="fw-bold m-0">{{ $failed }}</h2>
        </div>
    </div>
</div>

<div class="row">
    <!-- Campaign Details Card -->
    <div class="col-lg-4 mb-4">
        <div class="ios-card">
            <h5 class="fw-bold mb-4">Detalle de Campaña</h5>
            <table class="table table-borderless align-middle m-0" style="font-size: 0.9rem;">
                <tr>
                    <td class="text-muted py-2" style="width: 35%;">Asunto:</td>
                    <td class="fw-bold py-2">{{ $communication->subject }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Título Interno:</td>
                    <td class="fw-semibold py-2">{{ $communication->title }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Canal:</td>
                    <td class="fw-semibold py-2 text-uppercase">Email (SMTP)</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Fecha Envío:</td>
                    <td class="fw-semibold py-2">{{ $communication->sent_at ? $communication->sent_at->format('d/m/Y H:i') : 'No enviado' }}</td>
                </tr>
                <tr>
                    <td class="text-muted py-2">Plantilla:</td>
                    <td class="fw-semibold py-2">{{ $communication->template ? $communication->template->name : 'Texto plano' }}</td>
                </tr>
            </table>

            <div class="d-grid mt-4">
                <a href="{{ route('admin.comms.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver al Centro</a>
            </div>
        </div>
    </div>

    <!-- Recipient Logs list -->
    <div class="col-lg-8">
        <div class="ios-card">
            <h5 class="fw-bold mb-4">Registro de Entrega Detallado</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DESTINATARIO</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">EMAIL</th>
                            <th class="text-muted text-center" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DETALLE LOG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipients as $rec)
                            @php
                                $delivery = $rec->deliveries->first();
                            @endphp
                            <tr class="border-bottom border-ios">
                                <td class="fw-bold">{{ $rec->user ? $rec->user->full_name : 'Usuario borrado' }}</td>
                                <td>{{ $rec->email }}</td>
                                <td class="text-center">
                                    @if(!$delivery)
                                        <span class="badge bg-secondary-subtle text-secondary badge-ios">En Cola</span>
                                    @elseif($delivery->status === 'opened')
                                        <span class="badge bg-success text-white badge-ios"><i class="bi bi-eye-fill"></i> Abierto</span>
                                    @elseif($delivery->status === 'delivered')
                                        <span class="badge bg-success-subtle text-success badge-ios"><i class="bi bi-check-circle-fill"></i> Entregado</span>
                                    @else
                                        <span class="badge bg-danger text-white badge-ios"><i class="bi bi-x-circle-fill"></i> Fallido</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.85rem;">
                                    @if($delivery)
                                        @if($delivery->status === 'opened')
                                            Abierto el {{ $delivery->opened_at->format('d/m/Y H:i') }}
                                        @elseif($delivery->status === 'failed')
                                            <span class="text-danger fw-semibold">{{ $delivery->error_message }}</span>
                                        @else
                                            Entregado en el servidor SMTP
                                        @endif
                                    @else
                                        Procesando envío...
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    No hay destinatarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $recipients->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
