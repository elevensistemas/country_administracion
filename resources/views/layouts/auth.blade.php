<!DOCTYPE html>
<html lang="es" data-bs-theme="{{ $currentTheme ?? 'auto' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - La Ranita Admin</title>
    <!-- Bootstrap 5.3 CSS -->
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
            --ios-card-bg: rgba(255, 255, 255, 0.85);
            --ios-primary: #34c759;
            --ios-text: #1c1c1e;
            --ios-border: rgba(229, 229, 234, 0.5);
            --ios-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --font-outfit: 'Outfit', sans-serif;
        }

        [data-bs-theme="dark"] {
            --ios-bg: #000000;
            --ios-card-bg: rgba(28, 28, 30, 0.85);
            --ios-text: #f2f2f7;
            --ios-border: rgba(44, 44, 46, 0.5);
            --ios-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        body {
            font-family: var(--font-outfit);
            background-color: var(--ios-bg);
            color: var(--ios-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Abstract iOS blurred backgrounds */
        .circle-bg-1 {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(52, 199, 89, 0.3), rgba(0, 122, 255, 0.15));
            filter: blur(80px);
            z-index: -1;
            top: -10%;
            left: -10%;
        }
        .circle-bg-2 {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 204, 0, 0.15), rgba(52, 199, 89, 0.25));
            filter: blur(100px);
            z-index: -1;
            bottom: -15%;
            right: -10%;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            z-index: 1;
        }

        .auth-card {
            background-color: var(--ios-card-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--ios-border);
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: var(--ios-shadow);
        }

        .btn-ios {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-ios-primary {
            background-color: var(--ios-primary);
            border-color: var(--ios-primary);
            color: #ffffff;
        }
        .btn-ios-primary:hover {
            background-color: #28a745;
            border-color: #28a745;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .form-control-ios {
            border-radius: 12px;
            border: 1px solid var(--ios-border);
            padding: 12px;
            background-color: rgba(0, 0, 0, 0.02);
            color: var(--ios-text);
            transition: all 0.2s ease;
        }
        [data-bs-theme="dark"] .form-control-ios {
            background-color: rgba(255, 255, 255, 0.02);
        }
        .form-control-ios:focus {
            background-color: var(--ios-card-bg);
            border-color: var(--ios-primary);
            box-shadow: 0 0 0 3px rgba(52, 199, 89, 0.2);
            color: var(--ios-text);
        }
    </style>
</head>
<body>

    <div class="circle-bg-1"></div>
    <div class="circle-bg-2"></div>

    <div class="auth-container">
        <div class="text-center mb-4">
            <div class="bg-success text-white rounded-4 p-3 d-inline-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-water text-white" style="font-size: 2.2rem;"></i>
            </div>
            <h3 class="fw-bold m-0 text-success">La Ranita</h3>
            <span class="text-muted" style="font-size: 0.9rem;">Panel de Administración y Propietarios</span>
        </div>

        <div class="auth-card">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Automatic theme detection
        const savedTheme = localStorage.getItem('theme') || 'auto';
        if (savedTheme === 'auto') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
        } else {
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        }
    </script>
</body>
</html>
