<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario y Administración - Barrio Cerrado La Ranita</title>
    <!-- Google Fonts & Bootstrap 5 & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: #2e7d32;
            --primary-hover: #1b5e20;
            --secondary-color: #0f172a;
            --bg-light: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.98);
            --border-color: rgba(226, 232, 240, 0.85);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            line-height: 1.6;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        /* Sidebar Navigation */
        .manual-sidebar {
            position: sticky;
            top: 5rem;
            max-height: calc(100vh - 6rem);
            overflow-y: auto;
            border-radius: 16px;
        }

        .nav-link-manual {
            color: var(--text-muted);
            padding: 0.45rem 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.15s;
        }
        .nav-link-manual:hover, .nav-link-manual.active {
            color: var(--primary-color);
            background-color: rgba(46, 125, 50, 0.08);
            font-weight: 600;
        }

        /* Step Bubbles */
        .step-bubble {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* Code/Badge Tag */
        .badge-path {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            background: #f1f5f9;
            color: #334155;
            padding: 3px 7px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        /* Section Headings */
        .section-title {
            color: #0f172a;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1.25rem;
        }

        /* Alerts and Callouts */
        .callout-info {
            background-color: #f0fdf4;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            margin: 1rem 0;
        }
        .callout-warning {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 1rem;
            border-radius: 0 8px 8px 0;
            margin: 1rem 0;
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            .manual-content {
                width: 100% !important;
            }
            .glass-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
                margin-bottom: 1.5rem !important;
            }
            body {
                background: #fff !important;
            }
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <header class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3 no-print">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center fw-bold text-dark fs-5" href="{{ url('/') }}">
                <span class="p-2 rounded-circle bg-success bg-opacity-10 text-success me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-shield-shaded"></i>
                </span>
                La Ranita Country Club <span class="badge bg-success-subtle text-success ms-2 fs-6">Manual Oficial del Sistema</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->relationship_type === 'accounting' || auth()->user()->relationship_type === 'operator')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Panel de Administración
                        </a>
                    @else
                        <a href="{{ route('owner.dashboard') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Mi Portal
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                    </a>
                @endauth
                <button onclick="window.print()" class="btn btn-success btn-sm rounded-pill px-3">
                    <i class="bi bi-printer me-1"></i> Imprimir / Guardar PDF
                </button>
            </div>
        </div>
    </header>

    <div class="container-fluid px-4 py-4">
        <div class="row g-4">
            
            <!-- Left Sticky Sidebar -->
            <div class="col-lg-3 no-print">
                <div class="glass-card p-3 manual-sidebar">
                    <div class="mb-3">
                        <input type="text" id="manualSearch" class="form-control form-control-sm rounded-pill" placeholder="🔍 Buscar funcionalidad...">
                    </div>

                    <div class="text-uppercase text-muted fw-bold px-2 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">1. General y Acceso</div>
                    <a href="#intro" class="nav-link-manual"><i class="bi bi-info-circle me-2"></i> Introducción al Sistema</a>
                    <a href="#roles" class="nav-link-manual"><i class="bi bi-people me-2"></i> Roles y Permisos</a>
                    <a href="#auth-flow" class="nav-link-manual"><i class="bi bi-shield-lock me-2"></i> Acceso, Claves y Seguridad</a>

                    <div class="text-uppercase text-muted fw-bold px-2 mt-3 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">2. Portal del Propietario (Mi Portal)</div>
                    <a href="#owner-dashboard" class="nav-link-manual"><i class="bi bi-house me-2"></i> Inicio y Resumen de Saldo</a>
                    <a href="#owner-expenses" class="nav-link-manual"><i class="bi bi-receipt me-2"></i> Mis Expensas y Descarga PDF</a>
                    <a href="#owner-accounting" class="nav-link-manual"><i class="bi bi-wallet2 me-2"></i> Mi Cuenta Corriente</a>
                    <a href="#owner-payments" class="nav-link-manual"><i class="bi bi-cash-coin me-2"></i> Informar Pago de Expensas</a>
                    <a href="#owner-tickets" class="nav-link-manual"><i class="bi bi-chat-right-dots me-2"></i> Mis Reclamos / Mesa de Ayuda</a>
                    <a href="#owner-reservations" class="nav-link-manual"><i class="bi bi-calendar-check me-2"></i> Espacios Comunes y Reservas</a>
                    <a href="#owner-guests" class="nav-link-manual"><i class="bi bi-qr-code me-2"></i> Autorización de Visitas y Pase QR</a>
                    <a href="#owner-news" class="nav-link-manual"><i class="bi bi-newspaper me-2"></i> Novedades y Comunicados</a>
                    <a href="#owner-documents" class="nav-link-manual"><i class="bi bi-file-earmark-pdf me-2"></i> Descarga de Documentos</a>
                    <a href="#owner-property" class="nav-link-manual"><i class="bi bi-car-front me-2"></i> Mi Propiedad y Vehículos</a>
                    <a href="#owner-profile" class="nav-link-manual"><i class="bi bi-person-gear me-2"></i> Perfil, Avisos y Clave</a>

                    <div class="text-uppercase text-muted fw-bold px-2 mt-3 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">3. Panel de Administración</div>
                    <a href="#admin-dashboard" class="nav-link-manual"><i class="bi bi-speedometer2 me-2"></i> Tablero General (Dashboard)</a>
                    <a href="#admin-lots" class="nav-link-manual"><i class="bi bi-grid-3x3 me-2"></i> Lotes y Unidades Funcionales</a>
                    <a href="#admin-history" class="nav-link-manual"><i class="bi bi-clock-history me-2"></i> Historial y Seguimientos de Lote</a>
                    <a href="#admin-owners" class="nav-link-manual"><i class="bi bi-person-lines-fill me-2"></i> Padrón de Propietarios</a>
                    <a href="#admin-users" class="nav-link-manual"><i class="bi bi-person-badge me-2"></i> Usuarios y Roles del Sistema</a>
                    <a href="#admin-expenses" class="nav-link-manual"><i class="bi bi-calculator me-2"></i> Emisión y Liquidación de Expensas</a>
                    <a href="#admin-accounting" class="nav-link-manual"><i class="bi bi-currency-dollar me-2"></i> Cuentas Corrientes y Ajustes</a>
                    <a href="#admin-payments" class="nav-link-manual"><i class="bi bi-check2-all me-2"></i> Conciliación y Auto-Conciliación</a>
                    <a href="#admin-suppliers" class="nav-link-manual"><i class="bi bi-truck me-2"></i> Proveedores y Facturas de Compra</a>
                    <a href="#admin-tickets" class="nav-link-manual"><i class="bi bi-tools me-2"></i> Gestión de Tickets y Reclamos</a>
                    <a href="#admin-common-areas" class="nav-link-manual"><i class="bi bi-calendar3 me-2"></i> Espacios Comunes y Reservas</a>
                    <a href="#admin-news-comms" class="nav-link-manual"><i class="bi bi-megaphone me-2"></i> Novedades y Comunicados Masivos</a>
                    <a href="#admin-documents" class="nav-link-manual"><i class="bi bi-folder2-open me-2"></i> Repositorio de Documentos</a>
                    <a href="#admin-imports" class="nav-link-manual"><i class="bi bi-file-earmark-spreadsheet me-2"></i> Importación Masiva (Excel/CSV)</a>
                    <a href="#admin-audit" class="nav-link-manual"><i class="bi bi-shield-check me-2"></i> Auditoría de Cambios y Logins</a>
                    <a href="#admin-reports" class="nav-link-manual"><i class="bi bi-graph-up me-2"></i> Reportes y Métricas de Adopción</a>
                    <a href="#admin-settings" class="nav-link-manual"><i class="bi bi-gear me-2"></i> Parámetros, SMTP y WhatsApp</a>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9 manual-content">

                <!-- Header Intro Card -->
                <div class="glass-card p-4 p-md-5 mb-4 bg-white border-0 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <div class="p-3 rounded-4 bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-journal-bookmark-fill fs-2"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold text-dark m-0">Manual de Usuario y Administración</h2>
                            <p class="text-muted m-0 small">Guía técnica y operativa del Sistema de Gestión del Barrio Cerrado La Ranita</p>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">
                        Este documento detalla exhaustivamente las funciones reales implementadas en la plataforma, abarcando tanto el portal del residente como las herramientas de gestión contable, operativa y de auditoría para el equipo de administración.
                    </p>
                </div>

                <!-- ==================================================== -->
                <!-- SECCION 1: INTRODUCCION Y ROLES -->
                <!-- ==================================================== -->
                <section id="intro" class="glass-card p-4 mb-4">
                    <h3 class="section-title"><i class="bi bi-info-circle text-success me-2"></i>1. Introducción y Arquitectura</h3>
                    <p class="small text-muted">
                        El sistema de <strong>La Ranita Administración</strong> es una plataforma web desarrollada en Laravel que centraliza la administración integral del barrio: catastro de lotes, liquidación de expensas, conciliación bancaria, registro de reclamos, reserva de amenidades, repositorio documental y auditoría de eventos.
                    </p>

                    <h5 class="fw-bold mt-4 mb-2 text-dark" id="roles">Roles de Usuario en el Sistema</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm small align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 25%;">Rol</th>
                                    <th style="width: 25%;">Destino de Inicio</th>
                                    <th>Alcance y Permisos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-danger-subtle text-danger">superadmin</span> / <span class="badge bg-primary-subtle text-primary">admin</span></td>
                                    <td><span class="badge-path">/admin/dashboard</span></td>
                                    <td>Acceso total a finanzas, facturación, cuentas corrientes, catastro, configuración, auditoría y gestión de usuarios.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info-subtle text-info">accounting</span> (Contable)</td>
                                    <td><span class="badge-path">/admin/dashboard</span></td>
                                    <td>Acceso a facturación de expensas, cuentas corrientes, conciliación de pagos, proveedores y reportes financieros.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning-subtle text-warning">operator</span> (Operador)</td>
                                    <td><span class="badge-path">/admin/dashboard</span></td>
                                    <td>Gestión de reclamos/tickets, reservas de espacios comunes, novedades, comunicados y catastro de lotes.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success-subtle text-success">owner</span> (Propietario) / <span class="badge bg-secondary-subtle text-secondary">tenant</span> (Inquilino)</td>
                                    <td><span class="badge-path">/owner/dashboard</span></td>
                                    <td>Consulta de expensas de su unidad, informe de pagos con comprobante, cuenta corriente, reclamos, reservas de amenidades, autorizaciones de invitados y documentos.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="fw-bold mt-4 mb-2 text-dark" id="auth-flow">Acceso, Seguridad y Recuperación de Clave</h5>
                    <ul class="small text-muted ps-3 mb-0">
                        <li><strong>Ingreso (<span class="badge-path">/login</span>):</strong> Se realiza mediante Correo Electrónico y Contraseña.</li>
                        <li><strong>Protección de Intentos (Rate Limiting):</strong> Tras 5 intentos fallidos consecutivos desde una misma IP o correo, el sistema bloquea temporalmente el acceso por 1 minuto y registra el intento en la bitácora de seguridad.</li>
                        <li><strong>Primer Acceso / Cambio Forzado (<span class="badge-path">/password/force-change</span>):</strong> Los usuarios invitados deben establecer su propia contraseña y aceptar los términos del portal en su primer ingreso.</li>
                        <li><strong>Recuperación de Contraseña (<span class="badge-path">/forgot-password</span>):</strong> Envía un enlace seguro con token de restablecimiento al correo registrado.</li>
                        <li><strong>Tema Visual:</strong> En la barra lateral o menú inferior se puede alternar entre Modo Claro, Modo Oscuro o Automático según el sistema operativo.</li>
                    </ul>
                </section>

                <!-- ==================================================== -->
                <!-- SECCION 2: MANUAL DEL PROPIETARIO (MI PORTAL) -->
                <!-- ==================================================== -->
                <section id="owner-dashboard" class="glass-card p-4 mb-4">
                    <h3 class="section-title"><i class="bi bi-house-door-fill text-success me-2"></i>2. Manual del Propietario / Residente (Mi Portal)</h3>

                    <!-- 2.1 Dashboard -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark"><i class="bi bi-speedometer2 text-success me-2"></i>2.1 Inicio y Resumen de Saldo (<span class="badge-path">/owner</span>)</h5>
                        <p class="small text-muted">Es la pantalla principal que visualiza el vecino al ingresar.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Tarjeta de Saldo:</strong> Indica el saldo pendiente consolidado en color rojo (si registra deuda) junto a la fecha de próximo vencimiento, o en color verde como <em>"Saldo a Favor"</em> o <em>"¡Tu cuenta está al día!"</em>.</li>
                                <li><strong>Botones de Acción Rápida:</strong> Acceso directo a <strong>"Informar Pago"</strong> y <strong>"Ver Expensas"</strong>.</li>
                                <li><strong>Cuadrícula de Accesos Rápidos:</strong> Botones para ir directamente a Expensas, Invitados, Reservas y Reclamos.</li>
                                <li><strong>Novedades Recientes:</strong> Visualización de los últimos comunicados emitidos por la administración.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.2 Expensas -->
                    <div class="mb-4" id="owner-expenses">
                        <h5 class="fw-bold text-dark"><i class="bi bi-receipt text-success me-2"></i>2.2 Mis Expensas (<span class="badge-path">/owner/expenses</span>)</h5>
                        <p class="small text-muted">Permite consultar el historial de liquidaciones emitidas para su lote y descargar los comprobantes.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Listado de Liquidaciones:</strong> Muestra cada período (ej. <em>Octubre 2026</em>), importe al 1° vencimiento, importe al 2° vencimiento y estado (<code>Pagada</code>, <code>Pendiente</code> o <code>Vencida</code>).</li>
                                <li><strong>Descarga de PDF:</strong> Cada período incluye el botón para descargar el documento oficial de liquidación de expensas en formato PDF.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.3 Cuenta Corriente -->
                    <div class="mb-4" id="owner-accounting">
                        <h5 class="fw-bold text-dark"><i class="bi bi-wallet2 text-success me-2"></i>2.3 Mi Cuenta Corriente (<span class="badge-path">/owner/accounting</span>)</h5>
                        <p class="small text-muted">Muestra el libro de movimientos contables históricos de su unidad funcional.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Débitos (+):</strong> Emisión de expensas mensuales, recargos de segundo vencimiento o ajustes contables.</li>
                                <li><strong>Créditos (-):</strong> Pagos acreditados y conciliados por administración.</li>
                                <li><strong>Saldo Acumulado:</strong> Muestra la evolución cronológica del saldo tras cada transacción.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.4 Informar Pago -->
                    <div class="mb-4" id="owner-payments">
                        <h5 class="fw-bold text-dark"><i class="bi bi-cash-coin text-success me-2"></i>2.4 Informar Pago de Expensas (<span class="badge-path">/owner/payments/report</span>)</h5>
                        <p class="small text-muted">
                            Cuando el residente abona sus expensas mediante transferencia o depósito bancario, debe cargar el aviso de pago para que administración lo concilie.
                        </p>
                        <div class="p-3 bg-light rounded-3 border small mb-2">
                            <div class="fw-bold text-dark mb-2">Campos del Formulario:</div>
                            <ol class="mb-2 ps-3 text-muted">
                                <li><strong>Importe Transferido ($):</strong> Monto exacto en pesos transferido.</li>
                                <li><strong>Fecha del Pago:</strong> Día en que se realizó la operación.</li>
                                <li><strong>Medio de Pago:</strong> Selección entre <em>Transferencia Bancaria</em>, <em>Depósito Bancario</em> u <em>Otro Medio</em>.</li>
                                <li><strong>Banco de Destino:</strong> Cuenta bancaria a la que se transfirió (ej. Banco Galicia - Cuenta Consorcio o Banco Nación).</li>
                                <li><strong>N° de Comprobante / Referencia:</strong> Código o número de transacción bancaria.</li>
                                <li><strong>Adjuntar Comprobante:</strong> Archivo digital (PDF, JPG o PNG de hasta 5MB).</li>
                                <li><strong>Observaciones:</strong> Campo opcional para aclaraciones.</li>
                            </ol>
                            <div class="fw-bold text-dark mb-1">Qué ocurre al guardar:</div>
                            <p class="mb-0 text-muted">
                                El pago queda registrado con estado <code>Pendiente</code>. En cuanto Administración lo verifica contra su extracto bancario, el estado cambia a <code>Conciliado</code> y el saldo del lote se descuenta automáticamente.
                            </p>
                        </div>
                    </div>

                    <!-- 2.5 Reclamos -->
                    <div class="mb-4" id="owner-tickets">
                        <h5 class="fw-bold text-dark"><i class="bi bi-chat-right-dots-fill text-success me-2"></i>2.5 Mis Reclamos / Mesa de Ayuda (<span class="badge-path">/owner/tickets</span>)</h5>
                        <p class="small text-muted">Canal oficial para reportar incidencias de mantenimiento, seguridad o convivencia.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-2 ps-3 text-muted">
                                <li><strong>Nuevo Reclamo (<span class="badge-path">/owner/tickets/create</span>):</strong> Permite seleccionar el Lote de origen, la Categoría del problema, Asunto/Título, Prioridad estimada (Baja, Media, Alta), Detalle descriptivo y adjuntar una foto o archivo.</li>
                                <li><strong>Seguimiento e Hilo de Mensajes:</strong> Al ingresar al detalle de un reclamo, el propietario puede ver las respuestas del personal administrativo y escribir nuevos mensajes para brindar más detalles.</li>
                                <li><strong>Estados del Reclamo:</strong> <code>Abierto</code>, <code>En Progreso</code>, <code>Resuelto</code>, <code>Cerrado</code>.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.6 Reservas -->
                    <div class="mb-4" id="owner-reservations">
                        <h5 class="fw-bold text-dark"><i class="bi bi-calendar-check-fill text-success me-2"></i>2.6 Espacios Comunes y Reservas (<span class="badge-path">/owner/reservations</span>)</h5>
                        <p class="small text-muted">Permite reservar las instalaciones comunitarias que hayan sido habilitadas por la administración.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Catálogo de Espacios:</strong> Muestra los espacios disponibles con su capacidad máxima, duración permitida por turno, precio/arancel (o <em>Sin Costo</em>) y descripción.</li>
                                <li><strong>Solicitar Reserva:</strong> Se elige la fecha, el horario de inicio y fin, notas y se acepta el reglamento del espacio.</li>
                                <li><strong>Estados:</strong> <code>Pendiente de Aprobación</code>, <code>Aprobada</code>, <code>Rechazada</code> o <code>Cancelada</code>.</li>
                                <li><strong>Cancelación:</strong> El propietario puede cancelar sus reservas futuras directamente desde su listado.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.7 Invitados -->
                    <div class="mb-4" id="owner-guests">
                        <h5 class="fw-bold text-dark"><i class="bi bi-qr-code text-success me-2"></i>2.7 Autorización de Visitas e Invitados (<span class="badge-path">/owner/guests</span>)</h5>
                        <p class="small text-muted">Permite precargar autorizaciones para visitas o proveedores que concurrirán a su lote.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-2 ps-3 text-muted">
                                <li><strong>Tipos de Autorización:</strong>
                                    <ul>
                                        <li><code>Individual</code>: Para una persona puntual (Nombre, Apellido, DNI, Patente y Fecha de visita).</li>
                                        <li><code>Frecuente</code>: Para visitas o empleados recurrentes.</li>
                                        <li><code>Lista</code>: Para eventos o reuniones con múltiples invitados, detallando la nómina de nombres en el campo de notas.</li>
                                    </ul>
                                </li>
                                <li><strong>Pase Digital QR:</strong> Cada invitación genera una vista con código QR y datos del ingreso que el residente puede compartir con su invitado.</li>
                                <li><strong>Baja de Invitación:</strong> Las autorizaciones activas pueden eliminarse/revocarse en cualquier momento con el botón de eliminar.</li>
                            </ul>
                            <div class="callout-warning small m-0">
                                <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>Nota sobre Control de Garita:</strong>
                                Actualmente las autorizaciones se guardan y consultan en la base de datos de administración web. La integración automática con el software y hardware local de la garita de seguridad se encuentra en fase de desarrollo posterior.
                            </div>
                        </div>
                    </div>

                    <!-- 2.8 Novedades y Documentos -->
                    <div class="row g-3" id="owner-news">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-newspaper text-primary me-1"></i>Novedades (<span class="badge-path">/owner/news</span>)</h6>
                                <p class="text-muted mb-0">Permite leer las noticias, avisos de obras y comunicados oficiales publicados por el barrio cerrado con sus imágenes adjuntas.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="owner-documents">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>Documentos (<span class="badge-path">/owner/documents</span>)</h6>
                                <p class="text-muted mb-0">Repositorio donde el residente puede descargar reglamentos internos, actas de asamblea, estatutos y formularios en PDF categorizados.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2.9 Propiedad y Perfil -->
                    <div class="row g-3 mt-1" id="owner-property">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-car-front-fill text-secondary me-1"></i>Mi Propiedad (<span class="badge-path">/owner/property</span>)</h6>
                                <p class="text-muted mb-0">Muestra los residentes y vehículos registrados actualmente en su lote, con un botón para solicitar altas o bajas a la administración.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="owner-profile">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-gear text-success me-1"></i>Mi Perfil (<span class="badge-path">/owner/profile</span>)</h6>
                                <p class="text-muted mb-0">Permite actualizar el teléfono, correo alternativo, configurar la recepción de avisos por Email/WhatsApp y cambiar la contraseña de acceso.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================================================== -->
                <!-- SECCION 3: MANUAL DE ADMINISTRACION -->
                <!-- ==================================================== -->
                <section id="admin-dashboard" class="glass-card p-4 mb-4">
                    <h3 class="section-title"><i class="bi bi-laptop-fill text-primary me-2"></i>3. Manual de Administración y Operaciones</h3>

                    <!-- 3.1 Dashboard -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark"><i class="bi bi-speedometer2 text-primary me-2"></i>3.1 Tablero Principal (<span class="badge-path">/admin</span>)</h5>
                        <p class="small text-muted">Panel central con los indicadores en tiempo real de la administración.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Tarjetas de Métricas:</strong> Total de Lotes, Propietarios Activos, Monto Total de Expensas del Mes, Pagos Recibidos y Reclamos Abiertos.</li>
                                <li><strong>Gráficos y Tablas:</strong> Distribución de estados de cobranza, últimos movimientos de cuenta corriente y accesos rápidos a tareas operativas.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.2 Lotes y Unidades Funcionales -->
                    <div class="mb-4" id="admin-lots">
                        <h5 class="fw-bold text-dark"><i class="bi bi-grid-3x3 text-primary me-2"></i>3.2 Catastro de Lotes y Unidades Funcionales (<span class="badge-path">/admin/lots</span>)</h5>
                        <p class="small text-muted">Gestión de la estructura catastral del country club.</p>
                        <div class="p-3 bg-light rounded-3 border small mb-2">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Lotes:</strong> Permite crear y editar el número de lote, código identificador, dirección interna, estado (Baldío, En Construcción, Habitado), propietario titular y saldo consolidado.</li>
                                <li><strong>Unidades Funcionales (<span class="badge-path">/admin/functional-units</span>):</strong> Cada lote tiene asociadas una o más Unidades Funcionales con su respectivo código y coeficiente de participación para el prorrateo de expensas.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.3 Historial y Seguimientos -->
                    <div class="mb-4" id="admin-history">
                        <h5 class="fw-bold text-dark"><i class="bi bi-clock-history text-primary me-2"></i>3.3 Historial y Seguimientos de Lotes (<span class="badge-path">/admin/history</span>)</h5>
                        <p class="small text-muted">Bitácora histórica y tareas pendientes por lote.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Historial de Acontecimientos:</strong> Registro cronológico de eventos dominiales, obras, inspecciones o notas con filtros por lote, categoría y fecha.</li>
                                <li><strong>Seguimientos (<span class="badge-path">/admin/follow-ups</span>):</strong> Tareas y recordatorios pendientes sobre lotes específicos con estados <code>Pendiente</code> o <code>Completado</code>.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.4 Propietarios y Usuarios -->
                    <div class="row g-3 mb-4" id="admin-owners">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-lines-fill text-primary me-1"></i>Padrón de Propietarios (<span class="badge-path">/admin/owners</span>)</h6>
                                <p class="text-muted mb-0">Alta y modificación de propietarios con Nombre, Apellido, Razón Social, DNI, CUIT, teléfonos, correos principales/alternativos y canal de contacto preferido.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="admin-users">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-badge text-primary me-1"></i>Usuarios del Sistema (<span class="badge-path">/admin/users</span>)</h6>
                                <p class="text-muted mb-0">Gestión de cuentas de acceso. Permite crear usuarios, asignar roles (Admin, Operador, Contador, Propietario), activar/desactivar cuentas, reenviar invitaciones y resetear contraseñas.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3.5 Expensas -->
                    <div class="mb-4" id="admin-expenses">
                        <h5 class="fw-bold text-dark"><i class="bi bi-calculator-fill text-primary me-2"></i>3.4 Facturación y Liquidación de Expensas (<span class="badge-path">/admin/expenses</span>)</h5>
                        <p class="small text-muted">Módulo para la emisión y distribución de expensas.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <div class="fw-bold text-dark mb-1">Flujo de Emisión:</div>
                            <ol class="mb-2 ps-3 text-muted">
                                <li><strong>Crear Período (<span class="badge-path">/admin/expenses/create-period</span>):</strong> Se define el mes/año (ej. <em>10/2026</em>), fechas de 1° y 2° vencimiento, porcentaje de recargo y notas.</li>
                                <li><strong>Generar Expensas Masivas:</strong> Calcula automáticamente la cuota de cada lote/UF multiplicando los gastos por su coeficiente, o permite <strong>Importar Expensas desde Excel</strong>.</li>
                                <li><strong>Publicar y Descargar:</strong> Se revisan los montos generados y se hace clic en <strong>Publicar</strong> para impactar en las cuentas corrientes y habilitar la descarga del PDF a los residentes.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- 3.6 Cuentas Corrientes y Ajustes -->
                    <div class="mb-4" id="admin-accounting">
                        <h5 class="fw-bold text-dark"><i class="bi bi-currency-dollar text-primary me-2"></i>3.5 Cuentas Corrientes y Ajustes (<span class="badge-path">/admin/accounting</span>)</h5>
                        <p class="small text-muted">Supervisión contable de saldos y carga de ajustes manuales.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Visor de Saldos:</strong> Listado de todas las unidades funcionales con su saldo actualizado (deudor o acreedor).</li>
                                <li><strong>Ajustes Contables:</strong> Permite ingresar al detalle de una unidad e imputar un <em>Ajuste Manual</em> indicando Tipo (Débito o Crédito), Monto y Motivo justificado.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.7 Conciliación de Pagos -->
                    <div class="mb-4" id="admin-payments">
                        <h5 class="fw-bold text-dark"><i class="bi bi-check2-all text-primary me-2"></i>3.6 Conciliación de Pagos (<span class="badge-path">/admin/payments</span>)</h5>
                        <p class="small text-muted">Herramienta para verificar y aprobar las cobranzas informadas por los residentes.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-2 ps-3 text-muted">
                                <li><strong>Tablero de Control:</strong> Filtros por estado (<code>Pendientes</code>, <code>Conciliados</code>, <code>En Revisión</code>, <code>Rechazados</code>) y métricas de dinero conciliado en el día.</li>
                                <li><strong>Simulación y Auto-Conciliación:</strong> Herramienta que analiza los pagos pendientes y los cruza automáticamente con las expensas adeudadas mediante algoritmo de scoring por monto y fecha.</li>
                                <li><strong>Acciones en Detalle de Pago:</strong>
                                    <ul>
                                        <li><strong>Conciliar / Aprobar:</strong> Imputa el dinero a la deuda más antigua de la unidad, actualiza el saldo a $0.00 y genera el movimiento de crédito.</li>
                                        <li><strong>Marcar en Revisión:</strong> Deja el pago en pausa para consultar con el banco o el vecino.</li>
                                        <li><strong>Rechazar:</strong> Descarta el pago indicando el motivo de rechazo.</li>
                                        <li><strong>Revertir Pago:</strong> Si un pago fue aprobado por error o la transferencia fue rebotada, permite anularlo transaccionalmente generando un contra-asiento contable.</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.8 Proveedores y Facturas -->
                    <div class="mb-4" id="admin-suppliers">
                        <h5 class="fw-bold text-dark"><i class="bi bi-truck text-primary me-2"></i>3.7 Proveedores y Facturas de Compra (<span class="badge-path">/admin/suppliers</span>)</h5>
                        <p class="small text-muted">Gestión de cuentas por pagar y prestadores de servicios.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Padrón de Proveedores:</strong> Registro de CUIT, Razón Social, Rubro/Categoría, Teléfono, Email, Banco y CBU/Alias bancario.</li>
                                <li><strong>Facturas de Compra (<span class="badge-path">/admin/supplier-invoices</span>):</strong> Carga de facturas recibidas con número, concepto, monto, fecha de emisión, fecha de vencimiento y PDF adjunto.</li>
                                <li><strong>Planilla de Flujo Semanal (<span class="badge-path">/admin/supplier-invoices/print</span>):</strong> Vista de impresión para planificación de egresos y pagos de tesorería.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.9 Tickets y Amenidades -->
                    <div class="row g-3 mb-4" id="admin-tickets">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-tools text-primary me-1"></i>Gestión de Tickets (<span class="badge-path">/admin/tickets</span>)</h6>
                                <p class="text-muted mb-0">Tablero de reclamos. Permite asignar personal responsable, cambiar estados (Abierto, En Progreso, Resuelto), responder al vecino y guardar <strong>Notas Internas Privadas</strong> (visibles solo para el equipo).</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="admin-common-areas">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-calendar3 text-primary me-1"></i>Espacios Comunes (<span class="badge-path">/admin/common-areas</span>)</h6>
                                <p class="text-muted mb-0">Alta de amenidades (capacidad, precio, turnos, horarios y reglas). En <strong>Reservas (<span class="badge-path">/admin/reservations</span>)</strong> se aprueban o rechazan los turnos solicitados por los vecinos.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3.10 Novedades, Comunicados y Documentos -->
                    <div class="row g-3 mb-4" id="admin-news-comms">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-megaphone-fill text-primary me-1"></i>Comunicaciones Masivas (<span class="badge-path">/admin/comms</span>)</h6>
                                <p class="text-muted mb-0">Creación y despacho de comunicados oficiales dirigidos a toda la comunidad o segmentados por lote, con soporte para envío por Email y WhatsApp.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="admin-documents">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-folder2-open text-primary me-1"></i>Repositorio de Documentos (<span class="badge-path">/admin/documents</span>)</h6>
                                <p class="text-muted mb-0">Subida de archivos con control de versiones (v1, v2), clasificación por categorías y visibilidad pública (para vecinos) o privada (solo administración).</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3.11 Importador Masivo -->
                    <div class="mb-4" id="admin-imports">
                        <h5 class="fw-bold text-dark"><i class="bi bi-file-earmark-spreadsheet-fill text-primary me-2"></i>3.8 Importador Masivo de Datos (<span class="badge-path">/admin/imports</span>)</h5>
                        <p class="small text-muted">Carga acelerada de datos iniciales o periódicos mediante archivos Excel/CSV.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Tipos Soportados:</strong> Lotes, Propietarios y Liquidaciones de Expensas.</li>
                                <li><strong>Validación Previa:</strong> El sistema analiza el archivo fila por fila, reportando errores de formato, campos faltantes o duplicados antes de confirmar la inserción definitiva.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.12 Auditoría y Trazabilidad -->
                    <div class="mb-4" id="admin-audit">
                        <h5 class="fw-bold text-dark"><i class="bi bi-shield-check text-danger me-2"></i>3.9 Módulo de Auditoría y Control de Accesos (<span class="badge-path">/admin/audit</span>)</h5>
                        <p class="small text-muted">Trazabilidad inmutable de la actividad en la plataforma.</p>
                        <div class="p-3 bg-light rounded-3 border small">
                            <ul class="mb-0 ps-3 text-muted">
                                <li><strong>Pestaña 1 (Auditoría de Cambios y Cargas):</strong> Registra automáticamente qué usuario creó, modificó o eliminó cualquier dato (lotes, expensas, pagos, usuarios, proveedores, etc.) con su IP y fecha. Incluye un botón para abrir el <strong>Modal Comparativo (Diff)</strong> que muestra en rojo el valor anterior y en verde el valor nuevo.</li>
                                <li><strong>Pestaña 2 (Registro de Inicios de Sesión):</strong> Lista cronológica de todos los accesos (exitosos, fallidos por clave incorrecta o bloqueados) con dirección IP, navegador y sistema operativo.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.13 Reportes, Adopción y Configuración -->
                    <div class="row g-3" id="admin-reports">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-graph-up-arrow text-primary me-1"></i>Reportes y Adopción (<span class="badge-path">/admin/reports</span>)</h6>
                                <p class="text-muted mb-0">Métricas de morosidad, recaudación y exportación de reportes a Excel/CSV. En <strong>Adopción (<span class="badge-path">/admin/adoption</span>)</strong> se visualiza qué porcentaje de propietarios ya activó su cuenta y se lanzan recordatorios de invitación.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="admin-settings">
                            <div class="p-3 bg-light rounded-3 border small h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-gear-fill text-primary me-1"></i>Configuración del Sistema (<span class="badge-path">/admin/settings</span>)</h6>
                                <p class="text-muted mb-0">Parámetros institucionales del country, tasas de interés por mora, configuración del servidor SMTP para emails con botón de prueba de conexión y credenciales de API de WhatsApp.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Footer note -->
                <div class="text-center text-muted small py-4 no-print border-top">
                    © 2026 Barrio Cerrado La Ranita Country Club — Manual Oficial del Sistema v2.4
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple search filter in manual sidebar
        document.getElementById('manualSearch').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.manual-sidebar a').forEach(link => {
                const text = link.innerText.toLowerCase();
                link.style.display = text.includes(query) ? 'flex' : 'none';
            });
        });
    </script>
</body>
</html>
