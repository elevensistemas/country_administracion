<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Oficial de Usuario y Administración - Barrio Cerrado La Ranita</title>
    <!-- Google Fonts & Bootstrap 5 & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: #10b981;
            --primary-dark: #047857;
            --secondary-color: #0f172a;
            --bg-light: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.98);
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            line-height: 1.65;
            letter-spacing: -0.01em;
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease;
        }
        .glass-card:hover {
            box-shadow: 0 10px 30px -4px rgba(0, 0, 0, 0.08);
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #064e3b 100%);
            border-radius: 24px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.3);
        }
        .hero-banner::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
        }

        /* Sticky Sidebar */
        .manual-sidebar {
            position: sticky;
            top: 5rem;
            max-height: calc(100vh - 6rem);
            overflow-y: auto;
            border-radius: 18px;
            scrollbar-width: thin;
        }
        .manual-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .manual-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .nav-link-manual {
            color: var(--text-muted);
            padding: 0.5rem 0.85rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.15s;
        }
        .nav-link-manual:hover, .nav-link-manual.active {
            color: var(--primary-dark);
            background-color: rgba(16, 185, 129, 0.12);
            font-weight: 600;
            transform: translateX(3px);
        }

        /* Step Bubbles */
        .step-bubble {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.15);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        /* Mockup Images */
        .img-mockup {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 12px 35px -5px rgba(0, 0, 0, 0.12);
            max-width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }
        .img-mockup:hover {
            transform: scale(1.01);
        }

        /* Badges & Tags */
        .badge-path {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            background: #f1f5f9;
            color: #0f172a;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        .section-title {
            color: #0f172a;
            font-weight: 800;
            letter-spacing: -0.02em;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        /* Callouts */
        .callout-info {
            background-color: #f0fdf4;
            border-left: 4px solid var(--primary-dark);
            padding: 1.1rem;
            border-radius: 0 12px 12px 0;
            margin: 1.25rem 0;
        }
        .callout-warning {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 1.1rem;
            border-radius: 0 12px 12px 0;
            margin: 1.25rem 0;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.25rem;
            height: 100%;
            transition: all 0.2s ease;
        }
        .feature-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0,0,0,0.04);
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
                border: 1px solid #ccc !important;
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
    <header class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3 no-print shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center fw-bold text-dark fs-5" href="{{ url('/') }}">
                <span class="p-2 rounded-circle bg-success bg-opacity-10 text-success me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-shield-shaded fs-5"></i>
                </span>
                La Ranita Country Club <span class="badge bg-success-subtle text-success ms-2 fs-6 px-3 py-1 rounded-pill">Manual Oficial</span>
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
                <button onclick="window.print()" class="btn btn-success btn-sm rounded-pill px-3 fw-semibold">
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
                        <input type="text" id="manualSearch" class="form-control form-control-sm rounded-pill px-3" placeholder="🔍 Buscar funcionalidad...">
                    </div>

                    <div class="text-uppercase text-muted fw-bold px-2 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">1. General y Acceso</div>
                    <a href="#intro" class="nav-link-manual"><i class="bi bi-info-circle me-2 text-success"></i> Introducción al Sistema</a>
                    <a href="#roles" class="nav-link-manual"><i class="bi bi-people me-2 text-success"></i> Roles y Permisos</a>
                    <a href="#auth-flow" class="nav-link-manual"><i class="bi bi-shield-lock me-2 text-success"></i> Acceso y Seguridad</a>

                    <div class="text-uppercase text-muted fw-bold px-2 mt-3 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">2. Portal del Propietario (Mi Portal)</div>
                    <a href="#owner-dashboard" class="nav-link-manual"><i class="bi bi-house me-2 text-primary"></i> Inicio y Resumen de Saldo</a>
                    <a href="#owner-expenses" class="nav-link-manual"><i class="bi bi-receipt me-2 text-primary"></i> Mis Expensas y Descarga PDF</a>
                    <a href="#owner-accounting" class="nav-link-manual"><i class="bi bi-wallet2 me-2 text-primary"></i> Mi Cuenta Corriente</a>
                    <a href="#owner-payments" class="nav-link-manual"><i class="bi bi-cash-coin me-2 text-primary"></i> Informar Pago de Expensas</a>
                    <a href="#owner-tickets" class="nav-link-manual"><i class="bi bi-chat-right-dots me-2 text-primary"></i> Mis Reclamos / Soporte</a>
                    <a href="#owner-reservations" class="nav-link-manual"><i class="bi bi-calendar-check me-2 text-primary"></i> Espacios Comunes y Reservas</a>
                    <a href="#owner-guests" class="nav-link-manual"><i class="bi bi-qr-code me-2 text-primary"></i> Autorizaciones y Pase QR</a>
                    <a href="#owner-news" class="nav-link-manual"><i class="bi bi-newspaper me-2 text-primary"></i> Novedades y Documentos</a>
                    <a href="#owner-property" class="nav-link-manual"><i class="bi bi-car-front me-2 text-primary"></i> Mi Propiedad y Perfil</a>

                    <div class="text-uppercase text-muted fw-bold px-2 mt-3 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">3. Panel de Administración</div>
                    <a href="#admin-dashboard" class="nav-link-manual"><i class="bi bi-speedometer2 me-2 text-info"></i> Tablero Principal (Dashboard)</a>
                    <a href="#admin-lots" class="nav-link-manual"><i class="bi bi-grid-3x3 me-2 text-info"></i> Lotes y Unidades Funcionales</a>
                    <a href="#admin-history" class="nav-link-manual"><i class="bi bi-clock-history me-2 text-info"></i> Historial y Seguimientos</a>
                    <a href="#admin-owners" class="nav-link-manual"><i class="bi bi-person-lines-fill me-2 text-info"></i> Padrón de Propietarios</a>
                    <a href="#admin-users" class="nav-link-manual"><i class="bi bi-person-badge me-2 text-info"></i> Usuarios y Roles</a>

                    <div class="text-uppercase text-muted fw-bold px-2 mt-3 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">4. Finanzas y Operaciones</div>
                    <a href="#admin-expenses" class="nav-link-manual"><i class="bi bi-calculator me-2 text-warning"></i> Facturación de Expensas</a>
                    <a href="#admin-accounting" class="nav-link-manual"><i class="bi bi-currency-dollar me-2 text-warning"></i> Cuentas Corrientes y Ajustes</a>
                    <a href="#admin-payments" class="nav-link-manual"><i class="bi bi-check2-all me-2 text-warning"></i> Conciliación de Pagos</a>
                    <a href="#admin-suppliers" class="nav-link-manual"><i class="bi bi-truck me-2 text-warning"></i> Proveedores y Facturas</a>
                    <a href="#admin-reports" class="nav-link-manual"><i class="bi bi-bar-chart-line-fill me-2 text-warning"></i> Reportes y Estadísticas</a>
                    <a href="#admin-tickets" class="nav-link-manual"><i class="bi bi-tools me-2 text-warning"></i> Gestión de Tickets y Amenidades</a>

                    <div class="text-uppercase text-muted fw-bold px-2 mt-3 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">5. Seguridad y Auditoría</div>
                    <a href="#admin-audit" class="nav-link-manual"><i class="bi bi-shield-check me-2 text-danger"></i> Auditoría de Cambios (Diff)</a>
                    <a href="#admin-logins" class="nav-link-manual"><i class="bi bi-box-arrow-in-right me-2 text-danger"></i> Registro de Logins e IP</a>
                    <a href="#admin-imports" class="nav-link-manual"><i class="bi bi-file-earmark-spreadsheet me-2 text-danger"></i> Importación Masiva (Excel)</a>
                    <a href="#admin-settings" class="nav-link-manual"><i class="bi bi-gear me-2 text-danger"></i> Parámetros y Configuración</a>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9 manual-content">

                <!-- Hero Section Banner -->
                <div class="hero-banner p-4 p-md-5 mb-4 position-relative">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7">
                            <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-50 px-3 py-1 rounded-pill mb-3 small">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Sistema Oficial v2.4
                            </span>
                            <h1 class="display-6 fw-bold mb-3">Manual Integral de Usuario y Administración</h1>
                            <p class="text-white-50 lead fs-6 mb-4">
                                Guía visual y operativa del Sistema de Gestión del Barrio Cerrado La Ranita Country Club. Documentación completa basada fielmente en el funcionamiento real de la plataforma.
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="#owner-dashboard" class="btn btn-success btn-sm rounded-pill px-3 py-2 fw-semibold">
                                    <i class="bi bi-person-fill me-1"></i> Portal Propietarios
                                </a>
                                <a href="#admin-dashboard" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2 fw-semibold">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Panel Administración
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 text-center">
                            <img src="/img/manual/login_real.png" alt="Acceso al Sistema La Ranita" class="img-mockup" style="max-height: 280px; border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 15px 35px rgba(0,0,0,0.35);">
                        </div>
                    </div>
                </div>

                <!-- ==================================================== -->
                <!-- SECCION 1: INTRODUCCION Y ROLES -->
                <!-- ==================================================== -->
                <section id="intro" class="glass-card p-4 p-md-5 mb-4">
                    <h3 class="section-title"><i class="bi bi-info-circle-fill text-success me-2"></i>1. Introducción y Arquitectura</h3>
                    <p class="text-muted small">
                        La plataforma web de <strong>La Ranita Country Club</strong> centraliza de forma segura y transparente toda la gestión del consorcio: catastro de lotes, liquidación periódica de expensas, conciliación bancaria, mesa de reclamos, reservas de amenidades comunitarias, repositorio de documentos y auditoría de eventos.
                    </p>

                    <h5 class="fw-bold mt-4 mb-3 text-dark" id="roles">Perfiles y Roles de Usuario</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="p-2 rounded-circle bg-danger bg-opacity-10 text-danger me-2">
                                        <i class="bi bi-shield-lock-fill fs-5"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Superadmin & Administrador</h6>
                                        <small class="badge-path">/admin/dashboard</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-0">Acceso total a finanzas, facturación, cuentas corrientes, catastro, usuarios, auditoría y configuraciones del sistema.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="p-2 rounded-circle bg-info bg-opacity-10 text-info me-2">
                                        <i class="bi bi-calculator-fill fs-5"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Contable (Accounting)</h6>
                                        <small class="badge-path">/admin/dashboard</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-0">Emisión de expensas, movimientos de cuenta corriente, conciliación de cobranzas, facturas de compra y balances.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="p-2 rounded-circle bg-warning bg-opacity-10 text-warning me-2">
                                        <i class="bi bi-tools fs-5"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Operador / Mantenimiento</h6>
                                        <small class="badge-path">/admin/dashboard</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-0">Mesa de ayuda, asignación de reclamos, aprobación de reservas de espacios comunes y publicación de novedades.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="p-2 rounded-circle bg-success bg-opacity-10 text-success me-2">
                                        <i class="bi bi-house-door-fill fs-5"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-bold m-0 text-dark">Propietario & Inquilino</h6>
                                        <small class="badge-path">/owner/dashboard</small>
                                    </div>
                                </div>
                                <p class="small text-muted mb-0">Descarga de expensas en PDF, informe de transferencias, reclamos, reservas, autorizaciones de visitas y reglamentos.</p>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold mt-5 mb-3 text-dark" id="auth-flow">Seguridad, Acceso y Recuperación de Claves</h5>
                    <div class="p-3 bg-light rounded-3 border small">
                        <ul class="mb-0 ps-3 text-muted">
                            <li><strong>Inicio de Sesión (<span class="badge-path">/login</span>):</strong> Ingreso con correo electrónico y clave encriptada (BCrypt). Redirección automática según el rol asignado.</li>
                            <li><strong>Protección contra Fuerza Bruta (Rate Limiting):</strong> Tras 5 intentos fallidos consecutivos de contraseña, el sistema bloquea temporalmente el acceso por 60 segundos y registra el evento en la bitácora de seguridad.</li>
                            <li><strong>Primer Acceso Obligatorio (<span class="badge-path">/password/force-change</span>):</strong> Los usuarios dados de alta por invitación son redirigidos a definir su contraseña definitiva y aceptar los términos de uso.</li>
                            <li><strong>Recuperación de Contraseña (<span class="badge-path">/forgot-password</span>):</strong> Envía un enlace seguro con token de restablecimiento por correo electrónico.</li>
                            <li><strong>Modo Oscuro / Claro:</strong> Selector en la barra de navegación para alternar entre Modo Claro, Modo Oscuro o Automático.</li>
                        </ul>
                    </div>
                </section>

                <!-- ==================================================== -->
                <!-- SECCION 2: MANUAL DEL PROPIETARIO -->
                <!-- ==================================================== -->
                <section id="owner-dashboard" class="glass-card p-4 p-md-5 mb-4">
                    <h3 class="section-title"><i class="bi bi-phone-fill text-primary me-2"></i>2. Manual del Propietario / Residente (Mi Portal)</h3>

                    <!-- Visual Real Screens Card -->
                    <div class="p-4 bg-light rounded-4 border text-center mb-4">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-8">
                                <img src="/img/manual/owner_dashboard_real.png" alt="Portal del Propietario en PC" class="img-mockup w-100 mb-2">
                                <div class="small text-muted fw-semibold">Vista Escritorio: Panel consolidado del lote y accesos inmediatos.</div>
                            </div>
                            <div class="col-lg-4 text-center">
                                <img src="/img/manual/owner_mobile_real.png" alt="Portal del Propietario en Móvil" class="img-mockup" style="max-height: 380px;">
                                <div class="small text-muted fw-semibold mt-2">Vista Móvil (App Web)</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2.1 Dashboard -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark"><i class="bi bi-speedometer2 text-primary me-2"></i>2.1 Inicio y Resumen de Saldo (<span class="badge-path">/owner</span>)</h5>
                        <p class="small text-muted">Pantalla principal donde el vecino supervisa su estado de cuenta.</p>
                        <div class="feature-card">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Tarjeta de Saldo:</strong> Indica el saldo consolidado del lote. Si registra deuda se resalta en rojo con la fecha de próximo vencimiento. Si no registra deuda se indica como <em>Saldo a Favor</em> o <em>¡Tu cuenta está al día!</em>.</li>
                                <li><strong>Botones de Acción Rápida:</strong> Acceso con un clic a <strong>"Informar Pago"</strong> y <strong>"Ver Expensas"</strong>.</li>
                                <li><strong>Cuadrícula de Accesos Rápidos:</strong> Botones para ir directamente a Expensas, Invitados, Reservas y Reclamos.</li>
                                <li><strong>Novedades Recientes:</strong> Visualización de las últimas noticias y avisos oficiales emitidos por el barrio.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.2 Expensas y PDF -->
                    <div class="mb-4" id="owner-expenses">
                        <h5 class="fw-bold text-dark"><i class="bi bi-receipt text-primary me-2"></i>2.2 Mis Expensas y Descarga PDF (<span class="badge-path">/owner/expenses</span>)</h5>
                        <div class="feature-card">
                            <p class="small text-muted mb-2">Permite consultar el historial completo de liquidaciones mensuales emitidas para su unidad:</p>
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Listado de Liquidaciones:</strong> Detalla cada período (ej. <em>Octubre 2026</em>), importe al 1° vencimiento, importe al 2° vencimiento y estado (<code>Pagada</code>, <code>Pendiente</code> o <code>Vencida</code>).</li>
                                <li><strong>Descarga de PDF:</strong> Botón individual para descargar la liquidación oficial de expensas en formato PDF con el desglose de conceptos.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.3 Cuenta Corriente -->
                    <div class="mb-4" id="owner-accounting">
                        <h5 class="fw-bold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i>2.3 Mi Cuenta Corriente (<span class="badge-path">/owner/accounting</span>)</h5>
                        <div class="feature-card">
                            <p class="small text-muted mb-2">Libro mayor con la trazabilidad cronológica de movimientos:</p>
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Débitos (+):</strong> Emisión de expensas mensuales, recargos de 2° vencimiento o ajustes contables.</li>
                                <li><strong>Créditos (-):</strong> Pagos bancarios transferidos y conciliados por administración.</li>
                                <li><strong>Saldo Evolutivo:</strong> Balance actualizado paso a paso tras cada operación.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.4 Informar Pago -->
                    <div class="mb-4" id="owner-payments">
                        <h5 class="fw-bold text-dark"><i class="bi bi-cash-coin text-primary me-2"></i>2.4 Informar Pago de Expensas (<span class="badge-path">/owner/payments/report</span>)</h5>
                        <p class="small text-muted">Cuando el residente abona mediante transferencia o depósito bancario, debe cargar el aviso de pago para su posterior conciliación:</p>
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-lg-7">
                                <div class="p-3 bg-light rounded-3 border small h-100">
                                    <div class="fw-bold text-dark mb-2">Campos del Formulario:</div>
                                    <ol class="mb-3 ps-3 text-muted">
                                        <li><strong>Importe Transferido ($):</strong> Monto exacto en pesos transferido.</li>
                                        <li><strong>Fecha del Pago:</strong> Día en que se realizó la operación bancaria.</li>
                                        <li><strong>Medio de Pago:</strong> Selección entre <em>Transferencia Bancaria</em>, <em>Depósito Bancario</em> u <em>Otro Medio</em>.</li>
                                        <li><strong>Banco de Destino:</strong> Cuenta bancaria a la que se transfirió (Banco Supervielle).</li>
                                        <li><strong>N° de Comprobante / Referencia:</strong> Código o número de transacción bancaria.</li>
                                        <li><strong>Adjuntar Comprobante:</strong> Archivo digital (PDF, JPG o PNG de hasta 5MB).</li>
                                        <li><strong>Observaciones:</strong> Campo opcional para notas aclaratorias.</li>
                                    </ol>
                                    <div class="fw-bold text-dark mb-1">Qué ocurre al guardar:</div>
                                    <p class="mb-0 text-muted">
                                        El pago queda registrado con estado <code>Pendiente</code>. En cuanto Administración lo verifica contra su extracto bancario, el estado cambia a <code>Conciliado</code> y el saldo de la cuenta corriente se cancela automáticamente.
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-5 text-center">
                                <img src="/img/manual/owner_report_payment_real.png" alt="Formulario Informar Pago" class="img-mockup w-100">
                                <div class="small text-muted fw-semibold mt-1">Formulario interactivo de carga de pagos</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2.5 Reclamos -->
                    <div class="mb-4" id="owner-tickets">
                        <h5 class="fw-bold text-dark"><i class="bi bi-chat-right-dots-fill text-primary me-2"></i>2.5 Mis Reclamos / Mesa de Ayuda (<span class="badge-path">/owner/tickets</span>)</h5>
                        <div class="feature-card">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Nuevo Reclamo (<span class="badge-path">/owner/tickets/create</span>):</strong> Permite seleccionar el Lote de origen, la Categoría del problema, Asunto/Título, Prioridad estimada (Baja, Media, Alta), Detalle descriptivo y adjuntar una foto o archivo.</li>
                                <li><strong>Seguimiento e Hilo de Mensajes:</strong> Al ingresar al detalle de un reclamo, el residente puede ver las respuestas del personal administrativo y escribir nuevos mensajes para brindar más detalles.</li>
                                <li><strong>Estados del Reclamo:</strong> <code>Abierto</code>, <code>En Progreso</code>, <code>Resuelto</code>, <code>Cerrado</code>.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 2.6 Reservas -->
                    <div class="mb-4" id="owner-reservations">
                        <h5 class="fw-bold text-dark"><i class="bi bi-calendar-check-fill text-primary me-2"></i>2.6 Espacios Comunes y Reservas (<span class="badge-path">/owner/reservations</span>)</h5>
                        <div class="feature-card mb-3">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Catálogo de Espacios:</strong> Muestra los espacios disponibles configurados por la administración con su capacidad máxima, duración de turno, precio/arancel (o <em>Sin Costo</em>) y descripción.</li>
                                <li><strong>Solicitar Reserva:</strong> Se elige la fecha, el horario de inicio y fin, notas y se acepta el reglamento del espacio.</li>
                                <li><strong>Estados:</strong> <code>Pendiente de Aprobación</code>, <code>Aprobada</code>, <code>Rechazada</code> o <code>Cancelada</code>.</li>
                                <li><strong>Cancelación:</strong> El propietario puede cancelar sus reservas futuras directamente desde su listado.</li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <img src="/img/manual/owner_reservations_real.png" alt="Gestión de Reservas de Espacios Comunes" class="img-mockup w-100" style="max-height: 380px;">
                            <div class="small text-muted fw-semibold mt-1">Catálogo y Mis Reservas confirmadas de espacios comunes</div>
                        </div>
                    </div>

                    <!-- 2.7 Invitados -->
                    <div class="mb-4" id="owner-guests">
                        <h5 class="fw-bold text-dark"><i class="bi bi-qr-code text-primary me-2"></i>2.7 Autorización de Visitas y Pase QR (<span class="badge-path">/owner/guests</span>)</h5>
                        <div class="feature-card">
                            <ul class="mb-2 ps-3 small text-muted">
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

                    <!-- 2.8 Novedades, Documentos, Propiedad y Perfil -->
                    <div class="row g-3" id="owner-news">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-newspaper text-success me-1"></i>Novedades (<span class="badge-path">/owner/news</span>)</h6>
                                <p class="small text-muted mb-0">Lectura de noticias, comunicados y avisos institucionales publicados por el country club con imágenes adjuntas.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="owner-documents">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>Documentos (<span class="badge-path">/owner/documents</span>)</h6>
                                <p class="small text-muted mb-0">Descarga de reglamentos internos, actas de asamblea, estatutos y formularios en PDF clasificados por categoría.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="owner-property">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-car-front-fill text-secondary me-1"></i>Mi Propiedad (<span class="badge-path">/owner/property</span>)</h6>
                                <p class="small text-muted mb-0">Consulta de residentes y vehículos registrados para su lote, con botón para solicitar altas o bajas a la administración.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="owner-profile">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-gear text-primary me-1"></i>Mi Perfil (<span class="badge-path">/owner/profile</span>)</h6>
                                <p class="small text-muted mb-0">Actualización de teléfonos, correo alternativo, configuración de recepción de avisos por Email/WhatsApp y cambio de clave.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ==================================================== -->
                <!-- SECCION 3: MANUAL DE ADMINISTRACION -->
                <!-- ==================================================== -->
                <section id="admin-dashboard" class="glass-card p-4 p-md-5 mb-4">
                    <h3 class="section-title"><i class="bi bi-laptop-fill text-info me-2"></i>3. Manual de Administración y Operaciones</h3>

                    <!-- Visual Real Screen Card -->
                    <div class="p-4 bg-light rounded-4 border text-center mb-4">
                        <img src="/img/manual/admin_dashboard_real.png" alt="Panel de Administración" class="img-mockup w-100 mb-2">
                        <div class="small text-muted fw-semibold">Tablero Principal: KPIs de Deuda, Pagos a Conciliar, Gráficos de Reclamos y Adopción Digital.</div>
                    </div>

                    <!-- 3.1 Dashboard -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark"><i class="bi bi-speedometer2 text-info me-2"></i>3.1 Tablero Principal (<span class="badge-path">/admin</span>)</h5>
                        <div class="feature-card">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Tarjetas de Métricas:</strong> Deuda Total Consolidada, Saldos a Favor de vecinos, Pagos a Conciliar y Porcentaje de Adopción del Portal.</li>
                                <li><strong>Gráficos de Gestión:</strong> Reclamos por Categoría, Estado Operativo de Tickets y Adopción Digital.</li>
                                <li><strong>Tablas de Monitoreo:</strong> Reclamos recientes con estado en vivo y Pagos reportados pendientes de confirmación.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.2 Lotes y Unidades Funcionales -->
                    <div class="mb-4" id="admin-lots">
                        <h5 class="fw-bold text-dark"><i class="bi bi-grid-3x3 text-info me-2"></i>3.2 Catastro de Lotes y Unidades Funcionales (<span class="badge-path">/admin/lots</span>)</h5>
                        <div class="feature-card mb-3">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Lotes:</strong> Alta y edición de número de lote, código identificador, dirección interna, estado (Baldío, En Construcción, Habitado), propietario titular y saldo consolidado.</li>
                                <li><strong>Unidades Funcionales (<span class="badge-path">/admin/functional-units</span>):</strong> Registro de UFs vinculadas a cada lote con su respectivo código, co-titulares y saldo acumulado.</li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <img src="/img/manual/admin_lots_real.png" alt="Administración de Unidades Funcionales" class="img-mockup w-100" style="max-height: 380px;">
                            <div class="small text-muted fw-semibold mt-1">Catálogo de Unidades Funcionales, asignación de lotes y saldos</div>
                        </div>
                    </div>

                    <!-- 3.3 Historial y Seguimientos -->
                    <div class="mb-4" id="admin-history">
                        <h5 class="fw-bold text-dark"><i class="bi bi-clock-history text-info me-2"></i>3.3 Historial y Seguimientos de Lotes (<span class="badge-path">/admin/history</span>)</h5>
                        <div class="feature-card">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Historial de Acontecimientos:</strong> Bitácora de eventos dominiales, obras, inspecciones o notas con filtros por lote, categoría y fecha.</li>
                                <li><strong>Seguimientos (<span class="badge-path">/admin/follow-ups</span>):</strong> Tareas y recordatorios pendientes sobre lotes específicos con estados <code>Pendiente</code> o <code>Completado</code>.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3.4 Propietarios y Usuarios -->
                    <div class="row g-3 mb-2" id="admin-owners">
                        <div class="col-md-6">
                            <div class="feature-card h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-lines-fill text-info me-1"></i>Padrón de Propietarios (<span class="badge-path">/admin/owners</span>)</h6>
                                <p class="small text-muted mb-0">Alta y modificación de propietarios con Nombre, Apellido, Razón Social, DNI, CUIT, teléfonos, correos y canal de contacto preferido.</p>
                            </div>
                        </div>
                        <div class="col-md-6" id="admin-users">
                            <div class="feature-card h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-badge text-info me-1"></i>Usuarios del Sistema (<span class="badge-path">/admin/users</span>)</h6>
                                <p class="small text-muted mb-0">Gestión de cuentas. Permite crear usuarios, asignar roles (Admin, Operador, Contador, Propietario), activar/desactivar cuentas, reenviar invitaciones y resetear contraseñas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <img src="/img/manual/admin_users_real.png" alt="Gestión de Usuarios y Roles" class="img-mockup w-100" style="max-height: 380px;">
                        <div class="small text-muted fw-semibold mt-1">Padrón de cuentas de usuarios, estados, roles asignados y controles de acceso</div>
                    </div>
                </section>

                <!-- ==================================================== -->
                <!-- SECCION 4: GESTION CONTABLE Y FINANCIERA -->
                <!-- ==================================================== -->
                <section id="admin-expenses" class="glass-card p-4 p-md-5 mb-4">
                    <h3 class="section-title"><i class="bi bi-calculator-fill text-warning me-2"></i>4. Gestión Contable, Pagos y Proveedores</h3>

                    <!-- Visual Real Screen Card -->
                    <div class="p-4 bg-light rounded-4 border text-center mb-4">
                        <img src="/img/manual/admin_payments_real.png" alt="Finanzas y Conciliación de Pagos" class="img-mockup w-100 mb-2">
                        <div class="small text-muted fw-semibold">Tablero de Conciliación Bancaria: Control de pagos pendientes, coincidencia de montos y auto-conciliación.</div>
                    </div>

                    <!-- 4.1 Facturación de Expensas -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark"><i class="bi bi-receipt-cutoff text-warning me-2"></i>4.1 Facturación y Liquidación de Expensas (<span class="badge-path">/admin/expenses</span>)</h5>
                        <div class="p-3 bg-light rounded-3 border small mb-3">
                            <div class="fw-bold text-dark mb-1">Flujo de Emisión:</div>
                            <ol class="mb-2 ps-3 text-muted">
                                <li><strong>Crear Período (<span class="badge-path">/admin/expenses/create-period</span>):</strong> Se define el mes/año (ej. <em>10/2026</em>), fechas de 1° y 2° vencimiento, porcentaje de recargo y notas.</li>
                                <li><strong>Generar Expensas Masivas:</strong> Calcula automáticamente la cuota de cada lote/UF multiplicando los gastos por su coeficiente, o permite <strong>Importar Expensas desde Excel</strong>.</li>
                                <li><strong>Publicar y Descargar:</strong> Se revisan los montos generados y se hace clic en <strong>Publicar</strong> para impactar en las cuentas corrientes y habilitar la descarga del PDF a los residentes.</li>
                            </ol>
                        </div>
                        <div class="text-center">
                            <img src="/img/manual/admin_expenses_real.png" alt="Módulo de Expensas y Liquidaciones" class="img-mockup w-100" style="max-height: 380px;">
                            <div class="small text-muted fw-semibold mt-1">Listado de períodos de expensas, emisión masiva e importación</div>
                        </div>
                    </div>

                    <!-- 4.2 Cuentas Corrientes y Ajustes -->
                    <div class="mb-4" id="admin-accounting">
                        <h5 class="fw-bold text-dark"><i class="bi bi-currency-dollar text-warning me-2"></i>4.2 Cuentas Corrientes y Ajustes (<span class="badge-path">/admin/accounting</span>)</h5>
                        <div class="feature-card">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Visor de Saldos:</strong> Listado de todas las unidades funcionales con su saldo actualizado (deudor o acreedor).</li>
                                <li><strong>Ajustes Contables:</strong> Permite ingresar al detalle de una unidad e imputar un <em>Ajuste Manual</em> indicando Tipo (Débito o Crédito), Monto y Motivo justificado.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 4.3 Conciliación de Pagos -->
                    <div class="mb-4" id="admin-payments">
                        <h5 class="fw-bold text-dark"><i class="bi bi-check2-all text-warning me-2"></i>4.3 Conciliación de Pagos (<span class="badge-path">/admin/payments</span>)</h5>
                        <div class="feature-card">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Tablero de Control:</strong> Filtros por estado (<code>Pendientes</code>, <code>Conciliados</code>, <code>En Revisión</code>, <code>Rechazados</code>) y métricas de dinero conciliado en el día.</li>
                                <li><strong>Simulación y Auto-Conciliación:</strong> Herramienta que cruza automáticamente pagos pendientes con expensas adeudadas mediante algoritmo de scoring por coincidencia de monto y fecha.</li>
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

                    <!-- 4.4 Proveedores y Facturas -->
                    <div class="row g-3 mb-4" id="admin-suppliers">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-truck text-warning me-1"></i>Padrón de Proveedores (<span class="badge-path">/admin/suppliers</span>)</h6>
                                <p class="small text-muted mb-0">Registro de CUIT, Razón Social, Rubro/Categoría, Teléfono, Email, Banco y CBU/Alias bancario para pagos de tesorería.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-receipt text-warning me-1"></i>Facturas de Compra (<span class="badge-path">/admin/supplier-invoices</span>)</h6>
                                <p class="small text-muted mb-0">Carga de facturas recibidas con número, concepto, monto, fecha de emisión, vencimiento y vista de <strong>Flujo Semanal (<span class="badge-path">/admin/supplier-invoices/print</span>)</strong>.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 4.5 Reportes Ejecutivos y Estadísticas -->
                    <div class="mb-4" id="admin-reports">
                        <h5 class="fw-bold text-dark"><i class="bi bi-bar-chart-line-fill text-warning me-2"></i>4.5 Tablero Ejecutivo de Reportes y Estadísticas (<span class="badge-path">/admin/reports</span>)</h5>
                        <div class="feature-card mb-3">
                            <p class="small text-muted mb-2">
                                Centro integral de inteligencia operativa, morosidad y auditoría contable. Ofrece métricas consolidadas en tiempo real, gráficos de evolución financiera y herramientas de exportación masiva.
                            </p>
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Tarjetas de Métricas Principales (KPIs):</strong> Recaudación acumulada del año seleccionado, Total facturado en expensas, Deuda activa de lotes morosos y Compras/Gastos a proveedores con selector interactivo de año.</li>
                                <li><strong>Pestaña Finanzas y Cobranzas:</strong> Comparativa mensual de cobranzas confirmadas vs. liquidaciones emitidas y balance de posición neta del consorcio.</li>
                                <li><strong>Pestaña Ranking de Morosidad:</strong> Listado jerárquico de lotes con saldo deudor, datos de contacto del propietario (teléfono, email) y estado del lote.</li>
                                <li><strong>Pestaña Proveedores y Gastos:</strong> Resumen de gastos por empresa proveedora, historial de facturas y control de vencimientos.</li>
                                <li><strong>Pestaña Reclamos e Incidencias:</strong> Distribución de tickets según categoría, estado de resolución y calificación de atención del vecino.</li>
                                <li><strong>Pestaña Padrón de Lotes:</strong> Estado de habitabilidad (Baldío, En Construcción, Habitado) y unidades funcionales asociadas.</li>
                                <li><strong>Exportación Masiva a CSV / Excel (<span class="badge-path">/admin/reports/export</span>):</strong> Descargas directas en formato CSV con cabecera UTF-8 BOM compatible con Microsoft Excel (Morosos, Padrón de Lotes, Cobranzas, Proveedores y Reclamos).</li>
                                <li><strong>Modo de Impresión / PDF:</strong> Botón <em>"Imprimir Informe"</em> adaptado con diseño ejecutivo limpio y legible para reuniones de comisión o asambleas.</li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <img src="/img/manual/admin_reports_real.png" alt="Tablero Ejecutivo de Reportes y Estadísticas" class="img-mockup w-100" style="max-height: 440px;">
                            <div class="small text-muted fw-semibold mt-1">Tablero Ejecutivo de Reportes: KPIs anuales, pestañas de análisis y exportación de datos</div>
                        </div>
                    </div>
                </section>

                <!-- ==================================================== -->
                <!-- SECCION 5: AUDITORIA, TRAZABILIDAD Y CONFIGURACION -->
                <!-- ==================================================== -->
                <section id="admin-audit" class="glass-card p-4 p-md-5 mb-4">
                    <h3 class="section-title"><i class="bi bi-shield-check text-danger me-2"></i>5. Auditoría, Trazabilidad y Configuración</h3>

                    <!-- Visual Real Screen Card -->
                    <div class="p-4 bg-light rounded-4 border text-center mb-4">
                        <img src="/img/manual/admin_audit_real.png" alt="Auditoría y Bitácora de Eventos" class="img-mockup w-100 mb-2">
                        <div class="small text-muted fw-semibold">Bitácora de Eventos y Control de Accesos: Trazabilidad por IP, usuario y diferencias en datos.</div>
                    </div>

                    <!-- 5.1 Bitácora de Cambios -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark"><i class="bi bi-journal-text text-danger me-2"></i>5.1 Bitácora de Modificaciones y Cargas (<span class="badge-path">/admin/audit</span>)</h5>
                        <div class="feature-card">
                            <ul class="mb-0 ps-3 small text-muted">
                                <li><strong>Captura Automática:</strong> Registra automáticamente qué usuario creó, modificó o eliminó cualquier registro en el sistema (Lotes, Propietarios, Pagos, Expensas, Usuarios, etc.) con su dirección IP y marca temporal.</li>
                                <li><strong>Modal Comparativo de Cambios (Diff):</strong> Al hacer clic en <em>"Ver Cambios"</em>, se abre una tabla que resalta en rojo el valor anterior y en verde el valor nuevo de cada campo modificado.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- 5.2 Logins -->
                    <div class="mb-4" id="admin-logins">
                        <h5 class="fw-bold text-dark"><i class="bi bi-box-arrow-in-right text-danger me-2"></i>5.2 Registro de Inicios de Sesión (<span class="badge-path">/admin/audit?tab=logins</span>)</h5>
                        <div class="feature-card">
                            <p class="small text-muted mb-0">
                                Monitorea todos los intentos de acceso: <code>Exitoso</code>, <code>Fallido (Clave errónea)</code> o <code>Bloqueado (Exceso de intentos)</code>, detallando la dirección IP, navegador y sistema operativo de cada usuario.
                            </p>
                        </div>
                    </div>

                    <!-- 5.3 Importador, Documentos y Configuración -->
                    <div class="row g-3" id="admin-imports">
                        <div class="col-md-4">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-spreadsheet-fill text-success me-1"></i>Importador Masivo (<span class="badge-path">/admin/imports</span>)</h6>
                                <p class="small text-muted mb-0">Carga de planillas CSV/Excel para Lotes, Propietarios y Expensas con validación previa fila por fila de errores de formato o duplicados.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-folder2-open text-primary me-1"></i>Repositorio Documental (<span class="badge-path">/admin/documents</span>)</h6>
                                <p class="small text-muted mb-0">Subida de archivos con control de versiones (v1, v2), clasificación por categorías y visibilidad pública o privada.</p>
                            </div>
                        </div>
                        <div class="col-md-4" id="admin-settings">
                            <div class="feature-card">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-gear-fill text-danger me-1"></i>Configuración (<span class="badge-path">/admin/settings</span>)</h6>
                                <p class="small text-muted mb-0">Parámetros del country, tasas de mora mensual, servidor SMTP con botón de prueba de conexión y API de WhatsApp.</p>
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
