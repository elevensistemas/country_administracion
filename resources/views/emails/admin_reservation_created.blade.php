<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333333;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 16px;
            padding: 30px;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #eef2f0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #eef2f0;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #198754;
            font-size: 24px;
            margin: 0;
            font-weight: 700;
        }
        .info-card {
            background-color: #f8faf9;
            border-left: 4px solid #198754;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .info-row {
            margin-bottom: 12px;
            font-size: 15px;
        }
        .info-row strong {
            color: #4a5568;
        }
        .btn {
            display: inline-block;
            background-color: #198754;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin-top: 10px;
            box-shadow: 0 4px 6px rgba(25, 135, 84, 0.15);
        }
        .footer {
            text-align: center;
            margin-top: 35px;
            font-size: 12px;
            color: #718096;
            border-top: 1px solid #eef2f0;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nueva Reserva Registrada</h1>
        </div>
        <p>Estimado Administrador,</p>
        <p>Se ha registrado una nueva reserva para un espacio común en el sistema:</p>
        
        <div class="info-card">
            <div class="info-row">
                <strong>Espacio Común:</strong> {{ $reservation->commonArea->name }}
            </div>
            <div class="info-row">
                <strong>Reservado por:</strong> Lote {{ $reservation->lot->number }} ({{ $reservation->user->full_name }})
            </div>
            <div class="info-row">
                <strong>Fecha del evento:</strong> {{ $reservation->reservation_date->format('d/m/Y') }}
            </div>
            <div class="info-row">
                <strong>Horario asignado:</strong> {{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} hs
            </div>
            <div class="info-row">
                <strong>Precio de Alquiler:</strong> ${{ number_format($reservation->price, 2, ',', '.') }}
            </div>
            <div class="info-row">
                <strong>Estado Inicial:</strong> {{ $reservation->status === 'confirmed' ? 'Confirmado Automáticamente' : 'Pendiente de Aprobación' }}
            </div>
            @if($reservation->notes)
                <div class="info-row" style="margin-top: 15px; border-top: 1px dashed #cbd5e0; padding-top: 10px;">
                    <strong>Comentarios del residente:</strong><br>
                    <span style="font-style: italic; color: #4a5568;">"{{ $reservation->notes }}"</span>
                </div>
            @endif
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/admin/reservations') }}" class="btn">Gestionar en el Panel Admin</a>
        </div>

        <div class="footer">
            Este es un correo automático enviado por el sistema de administración de **La Ranita Consorcio**.
        </div>
    </div>
</body>
</html>
