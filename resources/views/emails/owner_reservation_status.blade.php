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
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-confirmed {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #664d03;
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
            <h1>Estado de tu Reserva</h1>
        </div>
        <p>Hola {{ $reservation->user->name }},</p>
        <p>Tu solicitud de reserva para el espacio común **{{ $reservation->commonArea->name }}** ha sido registrada.</p>
        
        <div class="info-card">
            <div class="info-row">
                <strong>Espacio Común:</strong> {{ $reservation->commonArea->name }}
            </div>
            <div class="info-row">
                <strong>Lote:</strong> Lote {{ $reservation->lot->number }}
            </div>
            <div class="info-row">
                <strong>Fecha del evento:</strong> {{ $reservation->reservation_date->format('d/m/Y') }}
            </div>
            <div class="info-row">
                <strong>Horario asignado:</strong> {{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} hs
            </div>
            <div class="info-row">
                <strong>Costo de Alquiler:</strong> ${{ number_format($reservation->price, 2, ',', '.') }}
            </div>
            <div class="info-row">
                <strong>Estado de la Reserva:</strong>
                @if($reservation->status === 'confirmed')
                    <span class="status-badge status-confirmed">Confirmada</span>
                @else
                    <span class="status-badge status-pending">Pendiente de Aprobación</span>
                @endif
            </div>
        </div>

        @if($reservation->status === 'pending')
            <p style="font-size: 14px; color: #4a5568;">
                *Nota: Este espacio requiere la revisión y confirmación manual por parte del equipo de administración. Te enviaremos una notificación cuando esté aprobada.*
            </p>
        @else
            <p style="font-size: 14px; color: #4a5568;">
                *¡Todo listo! El costo del alquiler ha sido cargado a la cuenta corriente del lote y se liquidará en tu próxima expensa.*
            </p>
        @endif

        <div style="text-align: center;">
            <a href="{{ url('/owner/reservations') }}" class="btn">Ver mis reservas en el Portal</a>
        </div>

        <div class="footer">
            Este es un correo automático enviado por el sistema de administración de **La Ranita Consorcio**.
        </div>
    </div>
</body>
</html>
