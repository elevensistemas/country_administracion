<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Liquidación Expensas - UF Lote {{ $expense->functionalUnit->lot->number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo h1 {
            color: #2e7d32;
            margin: 0;
            font-size: 24px;
        }
        .logo span {
            color: #666;
            font-size: 12px;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #333;
        }
        .invoice-details p {
            margin: 2px 0;
            font-size: 12px;
            color: #666;
        }
        .meta-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .meta-block h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            text-transform: uppercase;
            color: #555;
            letter-spacing: 0.5px;
        }
        .meta-block p {
            margin: 4px 0;
            font-size: 13px;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .items-table th {
            background-color: #f2f2f7;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            color: #555;
        }
        .items-table td {
            font-size: 13px;
        }
        .totals-section {
            margin-left: auto;
            width: 40%;
            border-top: 2px solid #ddd;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 16px;
            color: #2e7d32;
            border-top: 1px solid #eee;
            padding-top: 8px;
            margin-top: 8px;
        }
        .no-print {
            margin-bottom: 30px;
            text-align: center;
        }
        @media print {
            .no-print { display: none; }
            body { border: 0; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #2e7d32; color: white; border: 0; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Imprimir Liquidación / Guardar PDF
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #e5e5ea; color: #333; border: 0; border-radius: 6px; cursor: pointer; margin-left: 10px;">
            Cerrar Ventana
        </button>
    </div>

    <div class="header">
        <div class="logo">
            <h1>BARRIO PRIVADO LA RANITA</h1>
            <span>Consorcio de Copropietarios - Ruta 25, Pilar</span>
        </div>
        <div class="invoice-details">
            <h2>LIQUIDACIÓN DE EXPENSAS</h2>
            <p><strong>Período:</strong> {{ $expense->billingPeriod->period }}</p>
            <p><strong>Emisión:</strong> {{ $expense->issue_date->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="meta-section">
        <div class="meta-block">
            <h3>Destinatario</h3>
            <p>{{ $expense->functionalUnit->lot->owner ? $expense->functionalUnit->lot->owner->full_name : 'Copropietario' }}</p>
            <span style="font-size: 12px; color: #666;">DNI: {{ $expense->functionalUnit->lot->owner->dni ?? 'N/C' }}</span>
        </div>
        
        <div class="meta-block">
            <h3>Unidad Funcional</h3>
            <p>Lote {{ $expense->functionalUnit->lot->number }}</p>
            <span style="font-size: 12px; color: #666;">Código UF: {{ $expense->functionalUnit->code }}</span>
        </div>

        <div class="meta-block" style="text-align: right;">
            <h3>Vencimiento</h3>
            <p style="color: #c62828;">{{ $expense->due_date->format('d/m/Y') }}</p>
            <span style="font-size: 11px; color: #666;">2do Vencimiento: {{ $expense->second_due_date->format('d/m/Y') }}</span>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 70%;">Concepto / Detalle</th>
                <th style="width: 30%; text-align: right;">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expense->items as $item)
                <tr>
                    <td>{{ $item->concept }} <span class="badge" style="font-size: 9px; background: #e5e5ea; padding: 2px 4px; border-radius:3px; margin-left:5px;">{{ $item->category }}</span></td>
                    <td style="text-align: right; font-weight: bold;">${{ number_format($item->amount, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row">
            <span>Saldo Anterior:</span>
            <span>${{ number_format($expense->previous_balance, 2, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Nuevos Cargos:</span>
            <span>${{ number_format($expense->total_amount, 2, ',', '.') }}</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL A PAGAR:</span>
            <span>${{ number_format($expense->previous_balance + $expense->total_amount, 2, ',', '.') }}</span>
        </div>
    </div>
</body>
</html>
