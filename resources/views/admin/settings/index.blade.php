@extends('layouts.app')

@section('title', 'Configuración')
@section('page_title', 'Configuración General del Consorcio')

@section('content')
<div class="row">
    <!-- Configuration Form (Left) -->
    <div class="col-lg-8 mb-4">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf

            <!-- Financial Rules -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-percent text-success me-2"></i>Reglas Contables y Mora</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="interest_rate_monthly" class="form-label fw-semibold" style="font-size: 0.85rem;">Tasa de Interés Mensual por Mora (%)</label>
                        <input type="number" step="0.01" name="interest_rate_monthly" id="interest_rate_monthly" class="form-control form-control-ios" value="{{ old('interest_rate_monthly', $settings['interest_rate_monthly'] ?? '3.5') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="due_day" class="form-label fw-semibold" style="font-size: 0.85rem;">Día de Vencimiento</label>
                        <input type="number" name="due_day" id="due_day" class="form-control form-control-ios" value="{{ old('due_day', $settings['due_day'] ?? '10') }}" required min="1" max="28">
                    </div>

                    <div class="col-md-3">
                        <label for="second_due_day" class="form-label fw-semibold" style="font-size: 0.85rem;">Segundo Vencimiento</label>
                        <input type="number" name="second_due_day" id="second_due_day" class="form-control form-control-ios" value="{{ old('second_due_day', $settings['second_due_day'] ?? '20') }}" required min="1" max="28">
                    </div>
                </div>
            </div>

            <!-- SMTP Config -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-envelope-fill text-success me-2"></i>Servidor de Correo (SMTP)</h5>
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="mail_host" class="form-label fw-semibold" style="font-size: 0.85rem;">Servidor Host SMTP</label>
                        <input type="text" name="mail_host" id="mail_host" class="form-control form-control-ios" value="{{ old('mail_host', $email->mail_host ?? 'smtp.laranita.com') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="mail_port" class="form-label fw-semibold" style="font-size: 0.85rem;">Puerto</label>
                        <input type="number" name="mail_port" id="mail_port" class="form-control form-control-ios" value="{{ old('mail_port', $email->mail_port ?? '587') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="mail_username" class="form-label fw-semibold" style="font-size: 0.85rem;">Usuario / Cuenta</label>
                        <input type="text" name="mail_username" id="mail_username" class="form-control form-control-ios" value="{{ old('mail_username', $email->mail_username ?? 'alertas@laranita.com') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="mail_password" class="form-label fw-semibold" style="font-size: 0.85rem;">Contraseña</label>
                        <input type="password" name="mail_password" id="mail_password" class="form-control form-control-ios" value="{{ old('mail_password', $email->mail_password ?? 'secret_smtp_password') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="mail_encryption" class="form-label fw-semibold" style="font-size: 0.85rem;">Encriptación</label>
                        <select name="mail_encryption" id="mail_encryption" class="form-select form-control-ios">
                            <option value="tls" {{ ($email->mail_encryption ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($email->mail_encryption ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ !($email->mail_encryption ?? '') ? 'selected' : '' }}>Ninguna</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="mail_from_address" class="form-label fw-semibold" style="font-size: 0.85rem;">Remitente (Email)</label>
                        <input type="email" name="mail_from_address" id="mail_from_address" class="form-control form-control-ios" value="{{ old('mail_from_address', $email->mail_from_address ?? 'consorcio@laranita.com') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="mail_from_name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre Remitente</label>
                        <input type="text" name="mail_from_name" id="mail_from_name" class="form-control form-control-ios" value="{{ old('mail_from_name', $email->mail_from_name ?? 'La Ranita Consorcio') }}" required>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Provider Settings & QR Sync -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-whatsapp text-success me-2"></i>Proveedor e Integración de WhatsApp</h5>
                
                <div class="row g-4">
                    <!-- Config Left -->
                    <div class="col-md-7 border-end border-ios pr-md-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="provider" class="form-label fw-semibold" style="font-size: 0.85rem;">Proveedor API</label>
                                <select name="provider" id="provider" class="form-select form-control-ios" required>
                                    <option value="twilio" {{ ($whatsapp->provider ?? 'twilio') === 'twilio' ? 'selected' : '' }}>Twilio WhatsApp Business</option>
                                    <option value="chatapi" {{ ($whatsapp->provider ?? 'twilio') === 'chatapi' ? 'selected' : '' }}>ChatAPI / Custom Gateway (QR Web)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="api_url" class="form-label fw-semibold" style="font-size: 0.85rem;">Endpoint API URL</label>
                                <input type="url" name="api_url" id="api_url" class="form-control form-control-ios" value="{{ old('api_url', $whatsapp->api_url ?? 'https://api.twilio.com/v1/whatsapp') }}" required>
                            </div>

                            <div class="col-12">
                                <label for="api_token" class="form-label fw-semibold" style="font-size: 0.85rem;">Token de Autenticación / API Key</label>
                                <input type="password" name="api_token" id="api_token" class="form-control form-control-ios" value="{{ old('api_token', $whatsapp->api_token ?? 'secret_api_key_token') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- QR Right -->
                    <div class="col-md-5 pl-md-4 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-2">Vinculación de Dispositivo (QR Web)</h6>
                            <p class="text-muted" style="font-size: 0.8rem;">Escanea el código QR desde tu celular (WhatsApp > Dispositivos vinculados) para iniciar sesión.</p>
                            
                            <div class="p-3 bg-body-secondary rounded-4 mb-3 border border-ios text-center">
                                <span class="text-muted d-block mb-1" style="font-size: 0.75rem;">ESTADO CONEXIÓN</span>
                                <div id="wa-status-container">
                                    <span class="badge bg-danger text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 0.85rem;"><i class="bi bi-x-circle-fill me-1"></i> Desconectado</span>
                                </div>
                            </div>
                        </div>

                        <div id="wa-action-container">
                            <button type="button" class="btn btn-ios btn-ios-secondary w-100" data-bs-toggle="modal" data-bs-target="#waQrModal" onclick="startQrSimulation()">
                                <i class="bi bi-qr-code me-2 text-success"></i> Vincular por Código QR
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal QR Vinculación -->
            <div class="modal fade" id="waQrModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4">
                        <div class="modal-header border-bottom border-ios p-4">
                            <h5 class="modal-title fw-bold"><i class="bi bi-whatsapp text-success me-2"></i>Iniciar Sesión en WhatsApp</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="wa-qr-close"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <!-- QR Screen -->
                            <div id="qr-screen">
                                <p class="text-muted mb-4" style="font-size: 0.9rem;">Abre WhatsApp en tu teléfono, toca **Dispositivos Vinculados** y escanea este código para conectar el sistema.</p>
                                
                                <div class="position-relative d-inline-block p-4 bg-white rounded-4 shadow-sm border border-ios mb-3">
                                    <!-- QR Code Mock SVG -->
                                    <svg width="220" height="220" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg" id="qr-svg-code">
                                        <path d="M0 0h7v7H0V0zm1 1v5h5V1H1zm1 1h3v3H2V2zm6-2h1v1H8V0zm2 0h2v1h-2V0zm3 0h1v1h-1V0zm2 0h1v2h-1V0zm3 0h1v2h-1V0zm1 0h1v1h-1V0zm2 0h1v2h-1V0zM8 2h1v1H8V2zm2 0h1v1h-1V2zm4 0h1v1h-1V2zm1 0h1v1h-1V2zm2 0h1v1h-1V2zm3 0h1v1h-1V2zM0 8h7v1H0V8zm1 1v5h5V9H1zm1 1h3v3H2v-3zm6-2h1v1H8V8zm3 0h1v2h-1V8zm2 0h2v1h-2V8zm3 0h1v1h-1V8zm1 0h1v2h-1V8zm2 0h1v1h-1V8zm1 0h1v1h-1V8z" fill="#000"/>
                                        <path d="M22 0h7v7h-7V0zm1 1v5h5V1h-5zm1 1h3v3h-3V2zM8 10h1v2H8v-2zm1 0h1v2H9v-2zm3 0h1v1h-1v-1zm1 0h2v1h-2v-1zm4 0h1v2h-1v-2zm1 0h1v1h-1v-1zm2 0h1v1h-1v-1zm1 0h1v2h-1v-2zm2 0h1v1h-1v-1zM8 13h1v1H8v-1zm1 0h1v1H9v-1zm1 0h1v1h-1v-1zm3 0h1v1h-1v-1zm2 0h1v1h-1v-1zm1 0h1v1h-1v-1zm1 0h2v1h-2v-1zm3 0h1v1h-1v-1zm1 0h1v1h-1v-1z" fill="#000"/>
                                        <path d="M0 22h7v7H0v-7zm1 1v5h5v-5H1zm1 1h3v3H2v-3zm6-8h1v2H8v-2zm2 0h1v1h-1v-1zm1 0h1v1h-1v-1zm2 0h1v2h-1v-2zm3 0h1v1h-1v-1zm1 0h1v2h-1v-2zm2 0h1v1h-1v-1zm1 0h1v2h-1v-2zm2 0h1v1h-1v-1zM8 19h1v1H8v-1zm2 0h1v2h-1v-2zm2 0h1v1h-1v-1zm3 0h1v1h-1v-1zm2 0h1v1h-1v-1zm1 0h1v1h-1v-1zm1 0h1v1h-1v-1zm3 0h1v1h-1v-1zm1 0h1v1h-1v-1zM8 22h1v1H8v-1zm2 0h1v1h-1v-1zm2 0h1v2h-1v-2zm3 0h1v1h-1v-1zm2 0h1v1h-1v-1zm1 0h1v1h-1v-1zm2 0h1v1h-1v-1zm3 0h1v1h-1v-1zm1 0h1v1h-1v-1zM8 25h1v1H8v-1zm1 0h1v2H9v-2zm2 0h1v1h-1v-1zm2 0h1v1h-1v-1zm1 0h1v2h-1v-2zm3 0h1v1h-1v-1zm1 0h1v1h-1v-1zm1 0h1v1h-1v-1zm2 0h1v1h-1v-1zm2 0h1v1h-1v-1z" fill="#000"/>
                                    </svg>
                                    <!-- Scanning overlay spinner -->
                                    <div id="scanning-overlay" style="display: none;" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white bg-opacity-75 rounded-4">
                                        <div class="spinner-border text-success mb-2" role="status"></div>
                                        <strong class="text-success" style="font-size: 0.9rem;">Vinculando...</strong>
                                    </div>
                                </div>
                                <div class="text-muted" style="font-size: 0.8rem;"><span class="spinner-grow spinner-grow-sm text-success me-1"></span> Esperando escaneo...</div>
                            </div>
                            
                            <!-- Success Screen -->
                            <div id="success-screen" style="display: none;">
                                <div class="text-center py-4">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
                                    <h5 class="fw-bold mt-3">Dispositivo Vinculado</h5>
                                    <p class="text-muted" style="font-size: 0.9rem;">La sesión de WhatsApp Web ha sido conectada correctamente en tu dispositivo.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Config -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-bell-fill text-success me-2"></i>Notificaciones de Reservas</h5>
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="form-check-label fw-bold" for="notify_reservation_email">Notificar al Administrador por Email</label>
                                <small class="text-muted d-block">Envía un correo cada vez que un propietario realiza una nueva reserva.</small>
                            </div>
                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="notify_reservation_email" name="notify_reservation_email" value="1" style="width: 45px; height: 24px;" {{ ($settings['notify_reservation_email'] ?? '1') === '1' ? 'checked' : '' }}>
                        </div>
                        
                        <hr class="my-3 border-ios">

                        <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="form-check-label fw-bold" for="notify_reservation_system">Notificar en el Sistema (Campana)</label>
                                <small class="text-muted d-block">Muestra una alerta en la campana de notificaciones de la barra superior al administrador.</small>
                            </div>
                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="notify_reservation_system" name="notify_reservation_system" value="1" style="width: 45px; height: 24px;" {{ ($settings['notify_reservation_system'] ?? '1') === '1' ? 'checked' : '' }}>
                        </div>

                        <hr class="my-3 border-ios">

                        <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center mb-0">
                            <div>
                                <label class="form-check-label fw-bold" for="notify_reservation_owner_email">Confirmación al Propietario por Email</label>
                                <small class="text-muted d-block">Envía una confirmación inmediata por correo al vecino con el detalle y estado de su reserva.</small>
                            </div>
                            <input class="form-check-input ms-0" type="checkbox" role="switch" id="notify_reservation_owner_email" name="notify_reservation_owner_email" value="1" style="width: 45px; height: 24px;" {{ ($settings['notify_reservation_owner_email'] ?? '1') === '1' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end align-items-center mb-5">
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Guardar Ajustes</button>
            </div>
        </form>
    </div>

    <!-- Health checks triggers (Right) -->
    <div class="col-lg-4">
        <div class="ios-card border-warning">
            <h6 class="fw-bold mb-3 text-warning"><i class="bi bi-heart-pulse-fill me-2"></i>Pruebas de Conexión (Health checks)</h6>
            <p class="text-muted" style="font-size: 0.85rem;">Verifica la integridad y estado de los proveedores externos de mensajería.</p>

            <div class="d-grid gap-2 mt-4">
                <!-- Test Email -->
                <form action="{{ route('admin.settings.email-test') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ios btn-ios-secondary w-100 text-start">
                        <i class="bi bi-envelope-check-fill text-success me-2"></i> Probar Servidor SMTP
                    </button>
                </form>

                <!-- Test WhatsApp -->
                <form action="{{ route('admin.settings.whatsapp-test') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-ios btn-ios-secondary w-100 text-start mt-2">
                        <i class="bi bi-whatsapp text-success me-2"></i> Probar Endpoint WhatsApp
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let isLinked = localStorage.getItem('wa_linked') === 'true';

    function updateWaUI() {
        const statusContainer = document.getElementById('wa-status-container');
        const actionContainer = document.getElementById('wa-action-container');
        
        if (isLinked) {
            statusContainer.innerHTML = `
                <span class="badge bg-success text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 0.85rem;"><i class="bi bi-check-circle-fill me-1"></i> Conectado</span>
                <small class="text-muted d-block mt-2">Celular: Consorcio (Número: +54 9 11 5566-7788)</small>
            `;
            actionContainer.innerHTML = `
                <button type="button" class="btn btn-ios btn-outline-danger w-100" onclick="disconnectWa()">
                    <i class="bi bi-trash3 me-2"></i> Desvincular Dispositivo
                </button>
            `;
        } else {
            statusContainer.innerHTML = `
                <span class="badge bg-danger text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 0.85rem;"><i class="bi bi-x-circle-fill me-1"></i> Desconectado</span>
            `;
            actionContainer.innerHTML = `
                <button type="button" class="btn btn-ios btn-ios-secondary w-100" data-bs-toggle="modal" data-bs-target="#waQrModal" onclick="startQrSimulation()">
                    <i class="bi bi-qr-code me-2 text-success"></i> Vincular por Código QR
                </button>
            `;
        }
    }

    let qrTimeout;
    let scanTimeout;
    
    function startQrSimulation() {
        // Reset screens
        document.getElementById('qr-screen').style.display = 'block';
        document.getElementById('success-screen').style.display = 'none';
        document.getElementById('scanning-overlay').style.display = 'none';
        
        // Simulate Scan after 3 seconds
        scanTimeout = setTimeout(() => {
            document.getElementById('scanning-overlay').style.display = 'flex';
            
            // Simulate Success after 2.5 more seconds
            qrTimeout = setTimeout(() => {
                document.getElementById('qr-screen').style.display = 'none';
                document.getElementById('success-screen').style.display = 'block';
                
                isLinked = true;
                localStorage.setItem('wa_linked', 'true');
                updateWaUI();
                
                // Close modal automatically after 1.5 seconds
                setTimeout(() => {
                    const closeBtn = document.getElementById('wa-qr-close');
                    if (closeBtn) closeBtn.click();
                }, 1500);
            }, 2500);
        }, 3000);
    }

    function disconnectWa() {
        if(confirm('¿Estás seguro de que deseas desvincular el dispositivo de WhatsApp?')) {
            isLinked = false;
            localStorage.removeItem('wa_linked');
            updateWaUI();
        }
    }
    
    // On Page Load
    document.addEventListener('DOMContentLoaded', () => {
        updateWaUI();
    });
</script>
@endsection

