<!DOCTYPE html>
<html lang="es" data-bs-theme="{{ $currentTheme ?? 'auto' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title') - Mi Ranita</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts (Outfit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- PWA Manifest support -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ asset('img/logo-192.png') }}">

    <style>
        :root {
            --ios-bg: #f2f2f7;
            --ios-card-bg: #ffffff;
            --ios-primary: #34c759; /* Froggy Green */
            --ios-primary-hover: #28a745;
            --ios-text: #1c1c1e;
            --ios-muted: #8e8e93;
            --ios-border: #e5e5ea;
            --ios-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --ios-card-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
            --font-outfit: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        [data-bs-theme="dark"] {
            --ios-bg: #000000;
            --ios-card-bg: #1c1c1e;
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
            padding-bottom: 75px; /* space for bottom nav bar */
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* IOS Card Style */
        .ios-card {
            background-color: var(--ios-card-bg);
            border: 1px solid var(--ios-border);
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 16px;
            box-shadow: var(--ios-card-shadow);
        }

        /* IOS Button Style */
        .btn-ios {
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.95rem;
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

        /* Bottom Fixed Navigation Bar (App style) */
        .owner-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background-color: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid var(--ios-border);
            z-index: 1000;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        [data-bs-theme="dark"] .owner-bottom-nav {
            background-color: rgba(28, 28, 30, 0.92);
        }

        .owner-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--ios-muted);
            text-decoration: none;
            font-size: 0.72rem;
            font-weight: 500;
            flex-grow: 1;
            height: 100%;
            transition: color 0.15s ease;
        }
        .owner-nav-item i {
            font-size: 1.45rem;
            margin-bottom: 2px;
        }
        .owner-nav-item.active {
            color: var(--ios-primary);
            font-weight: 600;
        }

        /* Top Header App styling */
        .owner-top-bar {
            height: 60px;
            background-color: var(--ios-card-bg);
            border-bottom: 1px solid var(--ios-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .badge-ios {
            border-radius: 20px;
            padding: 5px 12px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .form-control-ios {
            border-radius: 12px;
            border: 1px solid var(--ios-border);
            background-color: var(--ios-bg);
            color: var(--ios-text);
            padding: 10px 14px;
        }
        .form-control-ios:focus {
            background-color: var(--ios-card-bg);
            border-color: var(--ios-primary);
            box-shadow: 0 0 0 3px rgba(52, 199, 89, 0.2);
            color: var(--ios-text);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Top Header Bar -->
    <div class="owner-top-bar">
        <div class="d-flex align-items-center">
            <div class="bg-success text-white rounded-3 p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="bi bi-water text-white" style="font-size: 1.1rem;"></i>
            </div>
            <h5 class="fw-bold m-0 text-success" style="font-size: 1.1rem;">Mi Ranita</h5>
        </div>

        <!-- Active Lot context selector (Only if owner has multiple lots) -->
        <div class="d-flex align-items-center gap-2">
            @php
                $user = Auth::user();
                $ownerLots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
                $activeLotId = session('active_lot_id', $ownerLots->first()?->id);
                $activeLot = $ownerLots->firstWhere('id', $activeLotId);
            @endphp
            
            @if($ownerLots->count() > 1)
                <form action="{{ route('preferences.theme') }}" id="lot-context-form" method="POST" class="m-0">
                    @csrf
                    <select name="active_lot_id" class="form-select form-select-sm rounded-pill px-3 py-1 bg-body-secondary border-0" onchange="changeActiveLot(this.value)">
                        @foreach($ownerLots as $lot)
                            <option value="{{ $lot->id }}" {{ $activeLotId == $lot->id ? 'selected' : '' }}>
                                Lote {{ $lot->number }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @elseif($activeLot)
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 font-monospace" style="font-size: 0.8rem;">
                    Lote {{ $activeLot->number }}
                </span>
            @endif

            <!-- Profile / Logout dropdown -->
            <div class="dropdown">
                <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600; font-size: 0.85rem; cursor: pointer;" data-bs-toggle="dropdown">
                    {{ substr($user->name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow rounded-4 p-2 mt-2">
                    <li><span class="dropdown-item-text fw-bold text-success">{{ $user->full_name }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item rounded-3" href="{{ route('owner.profile.show') }}"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                    <li><a class="dropdown-item rounded-3" href="{{ route('owner.property.index') }}"><i class="bi bi-house me-2"></i>Mi Propiedad</a></li>
                    <li>
                        <!-- Theme Toggle Button inside profile menu -->
                        <div class="dropdown-item d-flex align-items-center justify-content-between rounded-3" style="cursor: pointer;" onclick="toggleThemeMode()">
                            <span><i class="bi bi-moon-stars me-2"></i>Modo Oscuro</span>
                            <div class="form-check form-switch p-0 m-0">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" id="theme-switch-chk" style="width: 35px; height: 18px;">
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger rounded-3"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container py-3">
        <!-- Global Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center border-0 rounded-4 shadow-sm py-2 mb-3" style="font-size: 0.9rem;" role="alert">
                <i class="bi bi-check-circle-fill me-2 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="alert alert-danger d-flex align-items-center border-0 rounded-4 shadow-sm py-2 mb-3" style="font-size: 0.9rem;" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
                <div>
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        Por favor verifica los errores indicados.
                    @endif
                </div>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="owner-bottom-nav">
        <a href="{{ route('owner.dashboard') }}" class="owner-nav-item {{ Route::is('owner.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i>
            <span>Inicio</span>
        </a>
        <a href="{{ route('owner.expenses.index') }}" class="owner-nav-item {{ Route::is('owner.expenses.*') || Route::is('owner.accounting.*') || Route::is('owner.payments.*') ? 'active' : '' }}">
            <i class="bi bi-receipt-cutoff"></i>
            <span>Expensas</span>
        </a>
        <a href="{{ route('owner.guests.index') }}" class="owner-nav-item {{ Route::is('owner.guests.*') ? 'active' : '' }}">
            <i class="bi bi-qr-code"></i>
            <span>Invitados</span>
        </a>
        <a href="{{ route('owner.reservations.index') }}" class="owner-nav-item {{ Route::is('owner.reservations.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event-fill"></i>
            <span>Reservas</span>
        </a>
        <a href="{{ route('owner.profile.show') }}" class="owner-nav-item {{ Route::is('owner.profile.show') || Route::is('owner.tickets.*') || Route::is('owner.news.*') || Route::is('owner.documents.*') || Route::is('owner.history') || Route::is('owner.property.index') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i>
            <span>Más</span>
        </a>
    </nav>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Active Lot Context Switch JS -->
    <script>
        function changeActiveLot(lotId) {
            // Post to preferences route or custom route to change active lot session
            fetch("{{ route('preferences.theme') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ active_lot_id: lotId })
            }).then(() => {
                window.location.reload();
            });
        }

        // Dark/Light Theme manager
        function getPreferredTheme() {
            return localStorage.getItem('theme') || 'auto';
        }

        function setTheme(theme) {
            const html = document.documentElement;
            if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.setAttribute('data-bs-theme', 'dark');
            } else {
                html.setAttribute('data-bs-theme', theme === 'auto' ? 'light' : theme);
            }
            localStorage.setItem('theme', theme);
            
            const themeSwitch = document.getElementById('theme-switch-chk');
            if (themeSwitch) {
                themeSwitch.checked = (theme === 'dark' || (theme === 'auto' && html.getAttribute('data-bs-theme') === 'dark'));
            }
        }

        function toggleThemeMode() {
            const currentTheme = getPreferredTheme();
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const preferred = getPreferredTheme();
            setTheme(preferred);
        });

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered:', reg.scope))
                    .catch(err => console.error('Service Worker registration failed:', err));
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
