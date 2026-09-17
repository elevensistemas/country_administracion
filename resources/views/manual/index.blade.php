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
            --card-bg: rgba(255, 255, 255, 0.95);
            --border-color: rgba(226, 232, 240, 0.8);
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
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .glass-card:hover {
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.08);
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
            padding: 0.5rem 0.85rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-link-manual:hover, .nav-link-manual.active {
            color: var(--primary-color);
            background-color: rgba(46, 125, 50, 0.08);
            font-weight: 600;
        }

        /* Step Bubbles */
        .step-bubble {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* Visual Media */
        .img-mockup {
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 100%;
            height: auto;
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
                La Ranita Country Club <span class="badge bg-success-subtle text-success ms-2 fs-6">Manual Oficial</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->relationship_type === 'accounting' || auth()->user()->relationship_type === 'operator')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Volver al Panel Admin
                        </a>
                    @else
                        <a href="{{ route('owner.dashboard') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Volver a Mi Portal
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                    </a>
                @endauth
                <button onclick="window.print()" class="btn btn-success btn-sm rounded-pill px-3">
                    <i class="bi bi-printer me-1"></i> Imprimir / PDF
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
                        <input type="text" id="manualSearch" class="form-control form-control-sm rounded-pill" placeholder="🔍 Buscar tema o función...">
                    </div>

                    <div class="text-uppercase text-muted fw-bold small px-2 mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Introducción</div>
                    <a href="#intro" class="nav-link-manual"><i class="bi bi-house me-2"></i> Bienvenida y Objetivos</a>
                    <a href="#roles" class="nav-link-manual"><i class="bi bi-people me-2"></i> Roles y Perfiles</a>
                    <a href="#seguridad" class="nav-link-manual"><i class="bi bi-shield-lock me-2"></i> Acceso y Seguridad</a>

                    <div class="text-uppercase text-muted fw-bold small px-2 mt-3 mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Portal Propietarios</div>
                    <a href="#prop-dashboard" class="nav-link-manual"><i class="bi bi-speedometer2 me-2"></i> Dashboard de Vecino</a>
                    <a href="#prop-expensas" class="nav-link-manual"><i class="bi bi-receipt me-2"></i> Consulta de Expensas</a>
                    <a href="#prop-pagos" class="nav-link-manual"><i class="bi bi-cash-stack me-2"></i> Informar Pagos</a>
                    <a href="#prop-reclamos" class="nav-link-manual"><i class="bi bi-chat-left-dots me-2"></i> Reclamos y Soporte</a>
                    <a href="#prop-reservas" class="nav-link-manual"><i class="bi bi-calendar-event me-2"></i> Reservas de Amenidades</a>
                    <a href="#prop-visitas" class="nav-link-manual"><i class="bi bi-qr-code me-2"></i> Autorización de Visitas</a>

                    <div class="text-uppercase text-muted fw-bold small px-2 mt-3 mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Panel de Administración</div>
                    <a href="#admin-lotes" class="nav-link-manual"><i class="bi bi-geo-alt me-2"></i> Lotes y Catastro</a>
                    <a href="#admin-expensas" class="nav-link-manual"><i class="bi bi-calculator me-2"></i> Liquidación de Expensas</a>
                    <a href="#admin-conciliacion" class="nav-link-manual"><i class="bi bi-check2-circle me-2"></i> Conciliación Bancaria</a>
                    <a href="#admin-proveedores" class="nav-link-manual"><i class="bi bi-truck me-2"></i> Proveedores y Facturas</a>
                    <a href="#admin-auditoria" class="nav-link-manual"><i class="bi bi-journal-text me-2"></i> Registro de Eventos y Logins</a>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9 manual-content">

                <!-- Hero Section -->
                <div class="glass-card p-4 p-md-5 mb-4 text-center">
                    <img src="{{ asset('img/manual/manual_portada_1789645553441.jpg') }}" alt="Portada Manual La Ranita" class="img-mockup mb-4" style="max-height: 380px;">
                    <h1 class="fw-bold text-dark mb-2">Manual Oficial de Usuario y Administración</h1>
                    <p class="lead text-muted mx-auto" style="max-width: 700px;">
                        Guía completa paso a paso para residentes, propietarios, personal de mantenimiento, contabilidad y administradores del Barrio Cerrado La Ranita Country Club.
                    </p>
                </div>

                <!-- Section 1: Intro -->
                <section id="intro" class="glass-card p-4 mb-4">
                    <h2 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-bookmark-check-fill text-success me-2"></i>1. Introducción al Sistema</h2>
                    <p>
                        La plataforma integral de <strong>La Ranita Country Club</strong> permite gestionar de forma ágil, segura y transparente todas las operaciones del barrio cerrado: desde la consulta y liquidación de expensas hasta la reserva de espacios deportivos, el control de visitas en garita y la auditoría detallada de cada cambio operativo.
                    </p>
                    
                    <div class="row g-3 mt-2" id="roles">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <h5 class="fw-bold text-success"><i class="bi bi-person-circle me-2"></i>Perfil Propietario / Residente</h5>
                                <ul class="small text-muted mb-0 ps-3">
                                    <li>Descarga de liquidaciones de expensas en PDF.</li>
                                    <li>Carga de comprobantes de pago por transferencia.</li>
                                    <li>Historial de cuenta corriente y saldo actualizado.</li>
                                    <li>Reserva online de canchas, SUM y quinchos.</li>
                                    <li>Reporte y seguimiento de reclamos con fotos.</li>
                                    <li>Autorización previa de invitados y visitas.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <h5 class="fw-bold text-primary"><i class="bi bi-shield-lock-fill me-2"></i>Perfil Administrador / Contable</h5>
                                <ul class="small text-muted mb-0 ps-3">
                                    <li>Emisión masiva y prorrateo de expensas por lote.</li>
                                    <li>Conciliación bancaria e imputación de cobros.</li>
                                    <li>Gestión de cuentas por pagar y proveedores.</li>
                                    <li>Tablero de control de reclamos y mantenimiento.</li>
                                    <li>Auditoría forense de cambios (quién editó qué cosa).</li>
                                    <li>Control de accesos e inicios de sesión por IP.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Security & Login -->
                <section id="seguridad" class="glass-card p-4 mb-4">
                    <h2 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-shield-lock-fill text-danger me-2"></i>2. Acceso y Seguridad</h2>
                    <p class="text-muted small">
                        El sistema cuenta con un esquema de protección y cifrado para salvaguardar la privacidad de la comunidad:
                    </p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="fw-bold text-dark mb-1"><i class="bi bi-key-fill text-warning me-1"></i> Credenciales Únicas</div>
                                <div class="small text-muted">Cada propietario y administrador ingresa con su correo electrónico personal y contraseña encriptada.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-slash-fill text-danger me-1"></i> Bloqueo de Intentos</div>
                                <div class="small text-muted">Tras 5 intentos fallidos consecutivos, el acceso se bloquea temporalmente para evitar ataques de fuerza bruta.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="fw-bold text-dark mb-1"><i class="bi bi-globe2 text-info me-1"></i> Trazabilidad de IP</div>
                                <div class="small text-muted">Cada ingreso queda registrado con fecha, hora, navegador, sistema operativo y dirección IP de conexión.</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Resident Portal -->
                <section id="prop-dashboard" class="glass-card p-4 mb-4">
                    <h2 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-phone-fill text-success me-2"></i>3. Guía para el Vecino (Mi Portal)</h2>
                    
                    <div class="text-center my-3">
                        <img src="{{ asset('img/manual/manual_portal_propietario_1789645574473.jpg') }}" alt="Portal Propietario" class="img-mockup mb-3" style="max-height: 420px;">
                        <small class="text-muted d-block">Interfaz moderna adaptada a celulares, tablets y computadoras.</small>
                    </div>

                    <!-- Step by step -->
                    <div class="mt-4" id="prop-expensas">
                        <h4 class="fw-bold text-dark mb-3"><i class="bi bi-receipt text-success me-2"></i>Consulta y Pago de Expensas</h4>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="step-bubble me-3">1</div>
                            <div>
                                <h6 class="fw-bold mb-1">Visualización de Saldo</h6>
                                <p class="text-muted small mb-0">Al ingresar, verá en la pantalla principal si tiene saldo a favor o expensas pendientes de cancelación con su respectiva fecha de 1º y 2º vencimiento.</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3" id="prop-pagos">
                            <div class="step-bubble me-3">2</div>
                            <div>
                                <h6 class="fw-bold mb-1">Informar un Pago Bancario</h6>
                                <p class="text-muted small mb-0">
                                    Realice la transferencia al CBU oficial de la administración. Luego, ingrese a <strong>"Informar Pago"</strong>, complete el importe exacto, el banco emisor, el número de transacción y adjunte la foto/PDF del comprobante.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <div class="step-bubble me-3">3</div>
                            <div>
                                <h6 class="fw-bold mb-1">Acreditación y Recibo Digital</h6>
                                <p class="text-muted small mb-0">Una vez que administración concilia el ingreso bancario, su saldo se actualiza a $0.00 y se genera automáticamente su recibo de pago digital.</p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Reclamos y Reservas -->
                    <div class="row g-3" id="prop-reclamos">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h5 class="fw-bold text-dark"><i class="bi bi-tools text-warning me-2"></i>Cómo Crear un Reclamo</h5>
                                <ol class="small text-muted ps-3 mb-0">
                                    <li>Vaya a <strong>Mis Reclamos > Nuevo Reclamo</strong>.</li>
                                    <li>Elija la categoría (Luminarias, Calles, Seguridad, etc.).</li>
                                    <li>Describa la situación y adjunte fotos si es necesario.</li>
                                    <li>Recibirá avisos cuando el personal técnico comience la resolución.</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-md-6" id="prop-reservas">
                            <div class="p-3 bg-light rounded-3 border">
                                <h5 class="fw-bold text-dark"><i class="bi bi-calendar-check text-info me-2"></i>Reserva de Canchas y SUM</h5>
                                <ol class="small text-muted ps-3 mb-0">
                                    <li>Vaya a <strong>Espacios Comunes</strong>.</li>
                                    <li>Elija la instalación y consulte los horarios libres.</li>
                                    <li>Seleccione la fecha y turno disponible.</li>
                                    <li>Confirme la reserva. Si posee arancel, se sumará a su próxima expensa.</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-white rounded-3 border mt-3" id="prop-visitas">
                        <h5 class="fw-bold text-dark"><i class="bi bi-qr-code text-success me-2"></i>Autorización de Visitas y Proveedores</h5>
                        <p class="small text-muted mb-0">
                            Para evitar demoras en el ingreso al country, cargue con antelación el DNI y nombre de sus invitados o cuadrillas de trabajo. El personal de seguridad validará la identidad al instante en la garita de guardia.
                        </p>
                    </div>
                </section>

                <!-- Section 4: Admin & Audit -->
                <section id="admin-lotes" class="glass-card p-4 mb-4">
                    <h2 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-laptop-fill text-primary me-2"></i>4. Guía de Administración y Operaciones</h2>
                    
                    <div class="text-center my-3">
                        <img src="{{ asset('img/manual/manual_panel_administracion_1789645596516.jpg') }}" alt="Panel de Administración" class="img-mockup mb-3" style="max-height: 420px;">
                        <small class="text-muted d-block">Panel de Control Ejecutivo para Operadores y Administradores.</small>
                    </div>

                    <div class="row g-3 mt-3" id="admin-expensas">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <h5 class="fw-bold text-dark"><i class="bi bi-calculator-fill text-success me-2"></i>Liquidación de Expensas</h5>
                                <p class="text-muted small mb-0">
                                    1. Abra un nuevo período mensual.<br>
                                    2. Cargue los gastos ordinarios y extraordinarios.<br>
                                    3. El sistema prorratea según el coeficiente de cada lote.<br>
                                    4. Emita y despache masivamente por Email con 1 clic.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6" id="admin-conciliacion">
                            <div class="p-3 border rounded-3 bg-white">
                                <h5 class="fw-bold text-dark"><i class="bi bi-bank2 text-primary me-2"></i>Conciliación de Cobranzas</h5>
                                <p class="text-muted small mb-0">
                                    1. Revise los comprobantes enviados por los vecinos.<br>
                                    2. Valide con el extracto bancario oficial.<br>
                                    3. Al hacer clic en <strong>Aprobar</strong>, se cancela la deuda y se emite el recibo oficial.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border rounded-3 bg-white mt-3" id="admin-proveedores">
                        <h5 class="fw-bold text-dark"><i class="bi bi-truck text-secondary me-2"></i>Proveedores y Cuentas a Pagar</h5>
                        <p class="text-muted small mb-0">
                            Lleve el registro clasificado de prestadores de servicios (Seguridad, Jardinería, Electricidad), carga de facturas recibidas y calendario de vencimientos de pago.
                        </p>
                    </div>

                    <hr class="my-4" id="admin-auditoria">

                    <!-- Auditoría y Seguridad -->
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-shield-check text-danger me-2"></i>Módulo de Auditoría y Registro de Eventos</h4>
                    <p class="text-muted small">
                        El sistema cuenta con un motor automático de trazabilidad que registra de forma inmutable cada acción en la base de datos:
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm small">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pestaña</th>
                                    <th>Qué Información Registra</th>
                                    <th>Filtros Disponibles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-primary">Auditoría de Cambios y Cargas</td>
                                    <td>Quién creó, editó o borró un lote, pago, expensa o usuario. Incluye modal interactivo que muestra el valor antes (rojo) y después (verde).</td>
                                    <td>Operador, Acción (Crear/Editar/Borrar), Módulo, Fechas, IP.</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-success">Registro de Logins</td>
                                    <td>Cada acceso al sistema, si fue exitoso o fallido por clave incorrecta, bloqueos por intentos, IP, navegador y sistema operativo.</td>
                                    <td>Estado (Exitoso/Fallido/Bloqueado), Usuario, Fechas, IP.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Footer note -->
                <div class="text-center text-muted small py-3 no-print">
                    © 2026 Barrio Cerrado La Ranita Country Club — Sistema de Gestión Integral.
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
