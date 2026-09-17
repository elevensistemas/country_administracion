<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f2f2f7;
            color: #1c1c1e;
            margin: 0;
            padding: 30px 15px;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 18px;
            padding: 32px 28px;
            margin: 0 auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e5e5ea;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #f2f2f7;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .header h1 {
            color: #34c759;
            font-size: 22px;
            margin: 8px 0 0 0;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .badge {
            display: inline-block;
            background-color: #dcfce7;
            color: #15803d;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .news-title {
            color: #1c1c1e;
            font-size: 20px;
            font-weight: 700;
            margin: 16px 0 8px 0;
            line-height: 1.3;
        }
        .news-date {
            color: #8e8e93;
            font-size: 13px;
            margin-bottom: 18px;
        }
        .summary-box {
            background-color: #f8fafc;
            border-left: 4px solid #34c759;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: 500;
            color: #334155;
            line-height: 1.4;
        }
        .news-body {
            font-size: 15px;
            line-height: 1.6;
            color: #2c2c2e;
            white-space: pre-line;
            margin-bottom: 28px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 28px 0;
        }
        .btn-primary {
            display: inline-block;
            background-color: #34c759;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding: 12px 28px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(52, 199, 89, 0.3);
        }
        .footer {
            text-align: center;
            border-top: 1px solid #f2f2f7;
            padding-top: 20px;
            margin-top: 25px;
            font-size: 12px;
            color: #8e8e93;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">Novedad Oficial</span>
            <h1>Barrio Cerrado La Ranita</h1>
        </div>

        @if($recipient)
            <p style="font-size: 14px; color: #636366; margin-bottom: 15px;">
                Hola <strong>{{ $recipient->name }}</strong>,
            </p>
        @endif

        <div class="news-title">{{ $news->title }}</div>
        <div class="news-date">
            Publicado el {{ $news->published_at ? $news->published_at->format('d/m/Y') : now()->format('d/m/Y') }}
        </div>

        @if($news->summary)
            <div class="summary-box">
                {{ $news->summary }}
            </div>
        @endif

        <div class="news-body">
            {{ $news->content }}
        </div>

        <div class="btn-wrapper">
            <a href="{{ route('owner.news.show', $news->id ?? 1) }}" class="btn-primary" target="_blank">
                Ver Novedad en Mi Portal
            </a>
        </div>

        <div class="footer">
            <p>Este es un comunicado automático de la Administración de <strong>La Ranita</strong>.<br>
            Para consultas o reclamos, ingresa a tu portal en la sección de Reclamos y Gestiones.</p>
        </div>
    </div>
</body>
</html>