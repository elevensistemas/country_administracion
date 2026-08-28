<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial Lote {{ $lot->number }} - La Ranita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            color: #2e7d32;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 4px 8px;
            border: 0;
        }
        .meta-table td.label {
            font-weight: bold;
            color: #555;
            width: 15%;
        }
        .events-table {
            width: 100%;
            border-collapse: collapse;
        }
        .events-table th, .events-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .events-table th {
            background-color: #f2f2f7;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #e5e5ea;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #2e7d32; color: white; border: 0; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Imprimir Documento
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background-color: #e5e5ea; color: #333; border: 0; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Cerrar Ventana
        </button>
    </div>

    <div class="header">
        <h1>HISTORIAL OPERATIVO Y CLÍNICO - LOTE {{ $lot->number }}</h1>
        <span>Barrio Cerrado La Ranita - Pilar</span>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Código Lote:</td>
            <td>{{ $lot->code }}</td>
            <td class="label">Saldo de Cuenta:</td>
            <td><strong>${{ number_format($lot->balance, 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Propietario:</td>
            <td>{{ $lot->owner ? $lot->owner->full_name : 'Sin asignar' }}</td>
            <td class="label">Inquilino:</td>
            <td>{{ $lot->tenant ? $lot->tenant->full_name : 'Sin inquilino' }}</td>
        </tr>
        <tr>
            <td class="label">Exportado:</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
            <td class="label">Generado por:</td>
            <td>{{ auth()->user()->full_name }}</td>
        </tr>
    </table>

    <table class="events-table">
        <thead>
            <tr>
                <th style="width: 15%;">FECHA</th>
                <th style="width: 15%;">CATEGORÍA</th>
                <th style="width: 20%;">EVENTO</th>
                <th style="width: 40%;">DESCRIPCIÓN</th>
                <th style="width: 10%;">REGISTRADO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $event->event_date->format('d/m/Y H:i') }}</td>
                    <td><span class="badge">{{ $event->category->display_name }}</span></td>
                    <td><strong>{{ $event->title }}</strong></td>
                    <td>{{ $event->description }}</td>
                    <td>{{ $event->user ? $event->user->name : 'Sistema' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
