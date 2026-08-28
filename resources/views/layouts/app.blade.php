<!DOCTYPE html>
<html lang="es" data-bs-theme="{{ $currentTheme ?? 'auto' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - La Ranita Admin</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/style.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" disabled>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --ios-bg: #f2f2f7;
            --ios-card-bg: #ffffff;
            --ios-sidebar-bg: #ffffff;
            --ios-primary: #34c759; /* Green for La Ranita */
            --ios-primary-hover: #28a745;
            --ios-text: #1c1c1e;
            --ios-muted: #8e8e93;
            --ios-border: #e5e5ea;
            --ios-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --ios-card-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
            --font-outfit: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        [data-bs-theme="dark"] {
            --ios-bg: #000000;
            --ios-card-bg: #1c1c1e;
            --ios-sidebar-bg: #1c1c1e;
            --ios-text: #f2f2f7;
            --ios-muted: #8e8e93;
            --ios-border: #2c2c2e;
            --ios-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            --ios-card-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
        }

        body {
            font-family: var(--font-outfit);
            background-color: var(--ios-bg);
            color: var(--ios-text);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* IOS Cards */
        .ios-card {
            background-color: var(--ios-card-bg);
            border: 1px solid var(--ios-border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--ios-card-shadow);
            transition: all 0.3s ease;
        }
        .ios-card:hover {
            box-shadow: var(--ios-shadow);
        }

        /* Main Layout Wrapper */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar styling */
        .ios-sidebar {
            background-color: var(--ios-sidebar-bg);
            border-right: 1px solid var(--ios-border);
            height: 100vh;
            width: 260px;
            position: sticky;
            top: 0;
            z-index: 1000;
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .ios-main-content {
            flex-grow: 1;
            min-width: 0;
            padding: 30px;
            background-color: var(--ios-bg);
            min-height: 100vh;
        }

        @media (max-width: 991.98px) {
            .ios-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                z-index: 1050;
                box-shadow: none;
            }
            .ios-sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            }
            .ios-main-content {
                padding: 15px;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(0, 0, 0, 0.4);
                z-index: 1040;
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
            }
            .ios-sidebar.show + .sidebar-backdrop {
                display: block;
            }
        }

        /* Nav links iOS style */
        .ios-nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            margin: 4px 12px;
            color: var(--ios-text);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        
        .ios-nav-link i {
            font-size: 1.25rem;
            margin-right: 12px;
            color: var(--ios-muted);
            transition: color 0.2s ease;
        }

        .ios-nav-link:hover {
            background-color: rgba(52, 199, 89, 0.1);
            color: var(--ios-primary);
        }
        
        .ios-nav-link:hover i {
            color: var(--ios-primary);
        }

        .ios-nav-link.active {
            background-color: var(--ios-primary);
            color: #ffffff;
        }

        .ios-nav-link.active i {
            color: #ffffff;
        }

        /* Header / Navbar */
        .ios-header {
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--ios-border);
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        [data-bs-theme="dark"] .ios-header {
            background-color: rgba(28, 28, 30, 0.8);
        }

        /* Buttons styling */
        .btn-ios {
            border-radius: 12px;
            padding: 8px 18px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-ios-primary {
            background-color: var(--ios-primary);
            border-color: var(--ios-primary);
            color: #ffffff;
        }
        .btn-ios-primary:hover, .btn-ios-primary:focus {
            background-color: var(--ios-primary-hover);
            border-color: var(--ios-primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-ios-secondary {
            background-color: transparent;
            border: 1px solid var(--ios-border);
            color: var(--ios-text);
        }
        .btn-ios-secondary:hover {
            background-color: rgba(0, 0, 0, 0.05);
            color: var(--ios-text);
        }
        [data-bs-theme="dark"] .btn-ios-secondary:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Badges */
        .badge-ios {
            border-radius: 20px;
            padding: 5px 12px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timeline-ios {
            position: relative;
            padding-left: 30px;
            border-left: 2px solid var(--ios-border);
            margin-left: 15px;
        }
        .timeline-item-ios {
            position: relative;
            margin-bottom: 25px;
        }
        .timeline-badge-ios {
            position: absolute;
            left: -41px;
            top: 4px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background-color: var(--ios-bg);
            border: 2px solid var(--ios-primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Inputs */
        .form-control-ios {
            border-radius: 12px;
            border: 1px solid var(--ios-border);
            background-color: var(--ios-bg);
            color: var(--ios-text);
            padding: 10px 14px;
            transition: all 0.2s ease;
        }
        .form-control-ios:focus {
            background-color: var(--ios-card-bg);
            border-color: var(--ios-primary);
            box-shadow: 0 0 0 3px rgba(52, 199, 89, 0.2);
            color: var(--ios-text);
        }

        /* Pagination Styling iOS style */
        .pagination {
            margin-bottom: 0;
            gap: 5px;
        }
        .page-item .page-link {
            border-radius: 10px;
            border: 1px solid var(--ios-border);
            color: var(--ios-text);
            background-color: var(--ios-card-bg);
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .page-item.active .page-link {
            background-color: var(--ios-primary);
            border-color: var(--ios-primary);
            color: #ffffff;
        }
        .page-item.disabled .page-link {
            background-color: var(--ios-bg);
            border-color: var(--ios-border);
            color: var(--ios-muted);
        }
        .page-item:first-child .page-link, .page-item:last-child .page-link {
            border-radius: 10px;
        }
        .page-item .page-link:hover:not(.active) {
            background-color: rgba(52, 199, 89, 0.1);
            color: var(--ios-primary);
            border-color: var(--ios-primary);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="app-wrapper">

    <!-- Sidebar -->
    <div class="ios-sidebar d-flex flex-column justify-content-between" id="sidebar">
        <div>
            <!-- Logo Section -->
            <div class="p-4 border-bottom border-ios d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-water text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h5 class="m-0 fw-bold text-success">La Ranita</h5>
                        <small class="text-muted" style="font-size: 0.75rem;">Consorcio Barrio Cerrado</small>
                    </div>
                </div>
                <button class="btn btn-sm d-lg-none text-ios" onclick="toggleSidebar()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="py-3" style="max-height: 70vh; overflow-y: auto;">
                @if(Auth::user()->isAdmin() || Auth::user()->relationship_type === 'accounting' || Auth::user()->relationship_type === 'operator')
                    <!-- ADMIN MENU -->
                    <small class="text-uppercase text-muted fw-bold px-4 py-2 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Gestión</small>
                    <a href="{{ route('admin.dashboard') }}" class="ios-nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Inicio
                    </a>
                    <a href="{{ route('admin.owners.index') }}" class="ios-nav-link {{ Route::is('admin.owners.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Propietarios
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="ios-nav-link {{ Route::is('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-fill-gear"></i> Usuarios
                    </a>
                    <a href="{{ route('admin.lots.index') }}" class="ios-nav-link {{ Route::is('admin.lots.*') || Route::is('admin.functional-units.*') ? 'active' : '' }}">
                        <i class="bi bi-house-fill"></i> Lotes y Unidades
                    </a>
                    <a href="{{ route('admin.history.index') }}" class="ios-nav-link {{ Route::is('admin.history.index') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Historial General
                    </a>
                    <a href="{{ route('admin.follow-ups.index') }}" class="ios-nav-link {{ Route::is('admin.follow-ups.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check-fill"></i> Seguimientos
                    </a>
                    <a href="{{ route('admin.common-areas.index') }}" class="ios-nav-link {{ Route::is('admin.common-areas.*') ? 'active' : '' }}">
                        <i class="bi bi-building-fill"></i> Zonas Comunes
                    </a>
                    <a href="{{ route('admin.reservations.index') }}" class="ios-nav-link {{ Route::is('admin.reservations.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event-fill"></i> Reservas Espacios
                    </a>

                    <small class="text-uppercase text-muted fw-bold px-4 py-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Finanzas</small>
                    <a href="{{ route('admin.expenses.index') }}" class="ios-nav-link {{ Route::is('admin.expenses.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt-cutoff"></i> Expensas
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="ios-nav-link {{ Route::is('admin.payments.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i> Conciliar Pagos
                    </a>
                    <a href="{{ route('admin.accounting.index') }}" class="ios-nav-link {{ Route::is('admin.accounting.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i> Cuentas Corrientes
                    </a>
                    <a href="{{ route('admin.suppliers.index') }}" class="ios-nav-link {{ Route::is('admin.suppliers.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill"></i> Proveedores
                    </a>
                    <a href="{{ route('admin.supplier-invoices.index') }}" class="ios-nav-link {{ Route::is('admin.supplier-invoices.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-range-fill"></i> Planificador de Pagos
                    </a>

                    <small class="text-uppercase text-muted fw-bold px-4 py-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Comunicaciones</small>
                    <a href="{{ route('admin.tickets.index') }}" class="ios-nav-link {{ Route::is('admin.tickets.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-left-text-fill"></i> Reclamos
                    </a>
                    <a href="{{ route('admin.news.index') }}" class="ios-nav-link {{ Route::is('admin.news.*') ? 'active' : '' }}">
                        <i class="bi bi-newspaper"></i> Novedades
                    </a>
                    <a href="{{ route('admin.comms.index') }}" class="ios-nav-link {{ Route::is('admin.comms.*') ? 'active' : '' }}">
                        <i class="bi bi-envelope-paper-fill"></i> Comunicados
                    </a>
                    <a href="{{ route('admin.documents.index') }}" class="ios-nav-link {{ Route::is('admin.documents.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Documentos
                    </a>

                    <small class="text-uppercase text-muted fw-bold px-4 py-2 mt-3 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Sistema</small>
                    <a href="{{ route('admin.adoption.index') }}" class="ios-nav-link {{ Route::is('admin.adoption.index') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow"></i> Adopción
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="ios-nav-link {{ Route::is('admin.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-line-fill"></i> Reportes
                    </a>
                    <a href="{{ route('admin.audit.index') }}" class="ios-nav-link {{ Route::is('admin.audit.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill"></i> Auditoría
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="ios-nav-link {{ Route::is('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill"></i> Configuración
                    </a>
                @else
                    <!-- OWNER / RESIDENT MENU -->
                    <small class="text-uppercase text-muted fw-bold px-4 py-2 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">Mi Portal</small>
                    <a href="{{ route('owner.dashboard') }}" class="ios-nav-link {{ Route::is('owner.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill"></i> Inicio
                    </a>
                    <a href="{{ route('owner.expenses.index') }}" class="ios-nav-link {{ Route::is('owner.expenses.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Mis Expensas
                    </a>
                    <a href="{{ route('owner.accounting.index') }}" class="ios-nav-link {{ Route::is('owner.accounting.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i> Mi Cuenta Corriente
                    </a>
                    <a href="{{ route('owner.payments.report') }}" class="ios-nav-link {{ Route::is('owner.payments.report') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i> Informar Pago
                    </a>
                    <a href="{{ route('owner.tickets.index') }}" class="ios-nav-link {{ Route::is('owner.tickets.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-right-dots-fill"></i> Mis Reclamos
                    </a>
                    <a href="{{ route('owner.news.index') }}" class="ios-nav-link {{ Route::is('owner.news.*') ? 'active' : '' }}">
                        <i class="bi bi-newspaper"></i> Novedades
                    </a>
                    <a href="{{ route('owner.documents.index') }}" class="ios-nav-link {{ Route::is('owner.documents.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Documentos
                    </a>
                    <a href="{{ route('owner.history') }}" class="ios-nav-link {{ Route::is('owner.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Historia de mi Lote
                    </a>
                    <a href="{{ route('owner.profile.show') }}" class="ios-nav-link {{ Route::is('owner.profile.show') ? 'active' : '' }}">
                        <i class="bi bi-person-fill"></i> Mi Perfil
                    </a>
                @endif
            </div>
        </div>

        <!-- Sidebar Footer / User Profile & Theme toggler -->
        <div class="p-3 border-top border-ios">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-weight: 600;">
                        {{ substr(Auth::user()->name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                    </div>
                    <div class="ms-2">
                        <p class="m-0 fw-semibold text-truncate" style="max-width: 140px; font-size: 0.85rem;">{{ Auth::user()->full_name }}</p>
                        <small class="text-muted d-block text-capitalize" style="font-size: 0.75rem;">
                            {{ Auth::user()->relationship_type }}
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-1 bg-body-secondary p-1 rounded-3 mb-3">
                <button onclick="setTheme('light')" id="theme-btn-light" class="btn btn-sm flex-fill border-0 rounded-2 py-1"><i class="bi bi-brightness-high"></i></button>
                <button onclick="setTheme('dark')" id="theme-btn-dark" class="btn btn-sm flex-fill border-0 rounded-2 py-1"><i class="bi bi-moon-stars"></i></button>
                <button onclick="setTheme('auto')" id="theme-btn-auto" class="btn btn-sm flex-fill border-0 rounded-2 py-1"><small>Auto</small></button>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="d-grid">
                @csrf
                <button type="submit" class="btn btn-ios btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
    <div class="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <!-- Main Content Panel -->
    <div class="ios-main-content">
        <!-- Header / Navbar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-lg-none me-3 btn-ios" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h2 class="fw-bold m-0 text-success">@yield('page_title', 'La Ranita')</h2>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted d-none d-md-inline" style="font-size: 0.9rem;">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd D [de] MMMM, Y') }}
                </span>

                <!-- Notification Bell Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary position-relative btn-ios rounded-circle p-2 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;">
                        <i class="bi bi-bell-fill fs-5 text-success"></i>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white animate__animated animate__pulse animate__infinite" style="font-size: 0.65rem; padding: 0.35em 0.5em;">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4 p-2 mt-2" style="width: 320px; font-size: 0.85rem;">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom border-ios">
                            <span class="fw-bold text-success">Notificaciones</span>
                            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-success p-0 m-0 text-decoration-none fw-semibold" style="font-size: 0.75rem;">Marcar todo leído</button>
                                </form>
                            @endif
                        </div>
                        
                        <div class="py-1" style="max-height: 280px; overflow-y: auto;">
                            @if(isset($unreadNotifications) && $unreadNotifications->isNotEmpty())
                                @foreach($unreadNotifications as $notif)
                                    <a class="dropdown-item p-3 border-bottom border-ios rounded-3 d-flex align-items-start gap-2" href="{{ $notif->link ?? '#' }}" onclick="markAsRead(event, {{ $notif->id }}, '{{ $notif->link ?? '#' }}')">
                                        <div class="bg-success-subtle text-success rounded-circle p-1.5 d-flex align-items-center justify-content-center mt-0.5">
                                            <i class="bi bi-calendar-check-fill" style="font-size: 0.85rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong class="text-dark d-block" style="font-size: 0.82rem;">{{ $notif->title }}</strong>
                                            <span class="text-muted d-block text-wrap" style="font-size: 0.78rem; line-height: 1.3;">{{ $notif->message }}</span>
                                            <small class="text-muted d-block mt-1 font-monospace" style="font-size: 0.7rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-bell-slash fs-3 d-block mb-1 text-muted opacity-50"></i>
                                    <span>No tienes nuevas notificaciones</span>
                                </div>
                            @endif
                        </div>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Global Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center border-0 rounded-4 shadow-sm py-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="alert alert-danger d-flex align-items-center border-0 rounded-4 shadow-sm py-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                <div>
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        Por favor verifica los errores indicados en el formulario.
                    @endif
                </div>
            </div>
        @endif

        @yield('content')
    </div>
</div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Theme management script -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Apply visual active state to theme buttons
        function updateThemeButtons(theme) {
            const btns = ['light', 'dark', 'auto'];
            btns.forEach(b => {
                const btn = document.getElementById('theme-btn-' + b);
                if (b === theme) {
                    btn.classList.add('bg-success', 'text-white');
                    btn.classList.remove('bg-transparent', 'text-secondary');
                } else {
                    btn.classList.remove('bg-success', 'text-white');
                    btn.classList.add('bg-transparent', 'text-secondary');
                }
            });
        }

        function applyTheme(theme) {
            const html = document.documentElement;
            if (theme === 'auto') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                html.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
            } else {
                html.setAttribute('data-bs-theme', theme);
            }
            updateThemeButtons(theme);
        }

        function setTheme(theme) {
            applyTheme(theme);
            localStorage.setItem('theme', theme);

            // Send ajax request to persist preference on server
            fetch("{{ route('preferences.theme') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ theme: theme })
            })
            .then(res => res.json())
            .catch(err => console.error("Error setting theme preference: ", err));
        }

        // Initialize Theme
        const savedTheme = localStorage.getItem('theme') || "{{ $currentTheme ?? 'auto' }}";
        applyTheme(savedTheme);

        // Listen for system theme changes in auto mode
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            const current = localStorage.getItem('theme') || 'auto';
            if (current === 'auto') {
                applyTheme('auto');
            }
        });

        function markAsRead(e, id, link) {
            e.preventDefault();
            fetch(`/admin/notifications/${id}/read`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(() => {
                window.location.href = link;
            })
            .catch(() => {
                window.location.href = link;
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
