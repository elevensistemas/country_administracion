@extends('layouts.owner')

@section('title', 'Pase de Acceso QR')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-12">
        <div class="text-center mb-3">
            <h5 class="fw-bold m-0 text-success"><i class="bi bi-qr-code-scan me-2"></i>Pase de Acceso</h5>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Presenta o comparte este código QR en la guardia de ingreso.</p>
        </div>

        <!-- Premium QR Card -->
        <div class="ios-card bg-body-secondary border-0 p-4 text-center">
            <!-- Pulsing Active Badge -->
            <div class="d-inline-flex align-items-center bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 mb-4 fw-semibold border border-success border-opacity-25" style="font-size: 0.8rem;">
                <span class="pulsing-dot me-2"></span> PASE DE INGRESO ACTIVO
            </div>

            <!-- Guest details -->
            <div class="mb-4">
                <h4 class="fw-bold m-0 text-dark">{{ $guest->full_name }}</h4>
                @if($guest->dni)
                    <span class="text-muted d-block mt-1" style="font-size: 0.85rem;">DNI: <strong>{{ $guest->dni }}</strong></span>
                @endif
                <span class="badge bg-secondary-subtle text-secondary badge-ios mt-2">Lote {{ $guest->lot->number }}</span>
            </div>

            <!-- QR Container with pulsing scanner line -->
            <div class="qr-secure-container mx-auto mb-4 bg-white p-3 border border-2 border-success border-opacity-50 rounded-4 shadow-sm" style="width: 230px; height: 230px; position: relative;">
                <!-- Animated scanning line -->
                <div class="qr-scan-line"></div>
                
                <!-- Mock QR Code graphic -->
                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-dark">
                    <i class="bi bi-qr-code text-dark" style="font-size: 10rem; line-height: 1;"></i>
                </div>
            </div>

            <!-- Validities -->
            <div class="p-3 bg-body-tertiary rounded-4 mb-4" style="font-size: 0.85rem; line-height: 1.5;">
                @if($guest->type === 'individual' && $guest->visit_date)
                    <div class="mb-1"><span class="text-muted">Válido únicamente para el día:</span></div>
                    <strong class="text-dark fs-6">{{ $guest->visit_date->format('d/m/Y') }}</strong>
                @elseif($guest->type === 'frequent')
                    <div class="mb-1"><span class="text-muted">Pase frecuente permanente:</span></div>
                    <strong class="text-dark fs-6">{{ $guest->notes ?? 'Todos los días declarados' }}</strong>
                @endif
                
                @if($guest->license_plate)
                    <div class="mt-2 text-muted" style="font-size: 0.75rem;">Vehículo autorizado: <strong class="text-uppercase text-dark">{{ $guest->license_plate }}</strong></div>
                @endif
            </div>

            <!-- Share Buttons -->
            <div class="d-flex flex-column gap-2">
                @php
                    $shareText = rawurlencode("Hola! Te comparto tu pase de acceso para ingresar al Barrio La Ranita. Presentalo en la guardia desde este link: " . route('owner.guests.qr', $guest));
                @endphp
                <a href="https://api.whatsapp.com/send?text={{ $shareText }}" target="_blank" class="btn btn-ios btn-ios-primary w-100 py-3 d-flex align-items-center justify-content-center" style="background-color: #25d366; border-color: #25d366;">
                    <i class="bi bi-whatsapp me-2 fs-5"></i> Compartir por WhatsApp
                </a>
                <a href="{{ route('owner.guests.index') }}" class="btn btn-ios btn-ios-secondary w-100 py-2.5">
                    Volver a la Lista
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Pulsing Green Dot animation */
    .pulsing-dot {
        width: 8px;
        height: 8px;
        background-color: #34c759;
        border-radius: 50%;
        display: inline-block;
        animation: pulse-dot 1.5s infinite ease-in-out;
    }

    @keyframes pulse-dot {
        0% { transform: scale(0.8); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(0.8); opacity: 0.5; }
    }

    /* Scanning laser line animation */
    .qr-secure-container {
        position: relative;
        overflow: hidden;
    }
    .qr-scan-line {
        position: absolute;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #34c759;
        box-shadow: 0 0 8px #34c759;
        top: 0;
        z-index: 10;
        animation: scan-line 3s infinite linear;
    }

    @keyframes scan-line {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }
</style>
@endsection
