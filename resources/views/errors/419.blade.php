<!DOCTYPE html>
<html lang="es" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="2;url={{ route('login') }}">
    <title>Sesión Expirada - La Ranita</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon & Mobile Touch Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#198754">

    <style>
        :root {
            --ios-bg: #f2f2f7;
            --ios-card-bg: rgba(255, 255, 255, 0.9);
            --ios-text: #1c1c1e;
            --font-outfit: 'Outfit', sans-serif;
        }
        [data-bs-theme="dark"] {
            --ios-bg: #000000;
            --ios-card-bg: rgba(28, 28, 30, 0.9);
            --ios-text: #f2f2f7;
        }
        body {
            font-family: var(--font-outfit);
            background-color: var(--ios-bg);
            color: var(--ios-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .error-card {
            background: var(--ios-card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(120, 120, 128, 0.2);
            border-radius: 28px;
            padding: 40px 30px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .icon-circle {
            width: 72px;
            height: 72px;
            background: rgba(255, 149, 0, 0.15);
            color: #ff9500;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .btn-ios {
            border-radius: 14px;
            font-weight: 600;
            padding: 12px 24px;
            transition: transform 0.2s, opacity 0.2s;
        }
        .btn-ios:active {
            transform: scale(0.97);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-circle">
            <i class="bi bi-clock-history fs-1"></i>
        </div>
        <h4 class="fw-bold mb-2">Tu sesión ha expirado</h4>
        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.4;">
            Por seguridad, tu sesión se cerró por inactividad. Te estamos redirigiendo a la pantalla de inicio de sesión...
        </p>

        <div class="spinner-border text-success mb-4" role="status" style="width: 2rem; height: 2rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>

        <div>
            <a href="{{ route('login') }}" class="btn btn-success btn-ios w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Ir a Iniciar Sesión
            </a>
        </div>
    </div>

    <script>
        // Automatic redirection to login
        setTimeout(function() {
            window.location.href = "{{ route('login') }}";
        }, 1200);
    </script>
</body>
</html>
