<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Semanal de Pagos - La Ranita</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
            color: #333;
            background-color: #fff;
            padding: 30px;
        }
        .print-header {
            border-bottom: 2px solid #34c759;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .sec-title {
            font-weight: 700;
            background-color: #f2f2f7;
            padding: 8px 15px;
            border-radius: 6px;
            margin-top: 25px;
            margin-bottom: 15px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table th {
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
            background-color: #fafafa !important;
        }
        .table td {
            font-size: 0.85rem;
            vertical-align: middle;
        }
        .grand-total {
            background-color: #e5e5ea;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 30px;
            font-size: 1.1rem;
        }
        
        /* Print optimization rules */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            .table th {
                background-color: #fafafa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .sec-title {
                background-color: #f2f2f7 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Quick Bar (hidden during printing) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light border rounded-3">
        <div>
            <span class="text-muted fw-semibold"><i class="bi bi-info-circle me-1"></i> Vista de Impresión/Exportación a PDF</span>
        </div>
        <div>
            <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer-fill me-2"></i>Imprimir / Guardar PDF</button>
            <button class="btn btn-secondary" onclick="window.close()"><i class="bi bi-x-lg ms-1"></i> Cerrar</button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="print-header d-flex justify-content-between align-items-end">
        <div>
            <h4 class="fw-bold m-0 text-success" style="letter-spacing: -0.5px;">LA RANITA BARRIO CERRADO</h4>
            <span class="text-muted" style="font-size: 0.85rem;">Módulo de Finanzas y Administración</span>
        </div>
        <div class="text-end">
            <h5 class="fw-bold m-0">Agenda de Pagos a Proveedores</h5>
            <small class="text-muted">Generado el {{ $today->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    @php
        $sections = [
            ['title' => '1. Facturas Vencidas Impagas', 'list' => $overdueInvoices],
            ['title' => '2. Vencen Esta Semana', 'list' => $thisWeekInvoices],
            ['title' => '3. Vencen Próxima Semana', 'list' => $nextWeekInvoices],
            ['title' => '4. Vencimientos Posteriores (Previsión)', 'list' => $futureInvoices],
        ];
        $grandTotal = 0;
    @endphp

    @foreach($sections as $sec)
        @if($sec['list']->isNotEmpty())
            @php
                $secTotal = $sec['list']->sum('amount');
                $grandTotal += $secTotal;
            @endphp
            <div class="sec-title">{{ $sec['title'] }}</div>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 25%;">PROVEEDOR</th>
                        <th style="width: 15%;">CUIT</th>
                        <th style="width: 25%;">CONCEPTO Y NRO FACTURA</th>
                        <th style="width: 12%;">VENCIMIENTO</th>
                        <th style="width: 13%;">CBU / ALIAS</th>
                        <th style="width: 10%; text-align: right;">MONTO ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sec['list'] as $invoice)
                        <tr>
                            <td><strong>{{ $invoice->supplier->business_name }}</strong></td>
                            <td>{{ $invoice->supplier->cuit }}</td>
                            <td>
                                <span>{{ $invoice->concept }}</span>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Factura: {{ $invoice->invoice_number }}</small>
                            </td>
                            <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                            <td style="font-size: 0.75rem;">{{ $invoice->supplier->cbu_alias ?? '-' }}</td>
                            <td style="text-align: right; font-weight: 600;">
                                ${{ number_format($invoice->amount, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-group-divider">
                        <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                        <td style="text-align: right; font-weight: bold;">
                            ${{ number_format($secTotal, 2, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    @endforeach

    @if($paidInvoices->isNotEmpty())
        <div class="sec-title">5. Facturas Pagadas (Historial del período)</div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width: 25%;">PROVEEDOR</th>
                    <th style="width: 15%;">CUIT</th>
                    <th style="width: 37%;">CONCEPTO Y NRO FACTURA</th>
                    <th style="width: 13%;">EMISION</th>
                    <th style="width: 10%; text-align: right;">MONTO ($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paidInvoices as $invoice)
                    <tr>
                        <td>{{ $invoice->supplier->business_name }}</td>
                        <td>{{ $invoice->supplier->cuit }}</td>
                        <td>{{ $invoice->concept }} (Factura: {{ $invoice->invoice_number }})</td>
                        <td>{{ $invoice->issue_date->format('d/m/Y') }}</td>
                        <td style="text-align: right; font-weight: 500;">
                            ${{ number_format($invoice->amount, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                <tr class="table-group-divider">
                    <td colspan="4" class="text-end fw-bold">Total Pagado:</td>
                    <td style="text-align: right; font-weight: bold;">
                        ${{ number_format($paidInvoices->sum('amount'), 2, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="grand-total d-flex justify-content-between align-items-center">
        <span>TOTAL EGRESOS PROGRAMADOS / VENCIDOS:</span>
        <span>${{ number_format($grandTotal, 2, ',', '.') }}</span>
    </div>

    <!-- Automatic trigger for print -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Delay slightly to ensure fonts render nicely
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
