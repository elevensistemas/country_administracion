@extends('layouts.app')

@section('title', 'Importación de Datos')
@section('page_title', 'Importación Masiva de Excel/CSV')

@section('content')
<div class="row">
    <!-- Imports List (Left) -->
    <div class="col-lg-8 mb-4">
        <div class="ios-card">
            <h5 class="fw-bold mb-4">Historial de Importaciones</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ARCHIVO</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">TIPO</th>
                            <th class="text-muted text-center" style="font-size: 0.85rem; font-weight: 600;">PROCESADOS</th>
                            <th class="text-muted text-center" style="font-size: 0.85rem; font-weight: 600;">FALLIDOS</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($imports as $imp)
                            <tr class="border-bottom border-ios">
                                <td style="font-size: 0.9rem;">{{ $imp->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold">{{ $imp->filename }}</td>
                                <td class="text-uppercase" style="font-size: 0.85rem;">{{ $imp->type }}</td>
                                <td class="text-center text-success fw-bold">{{ $imp->valid_rows }} / {{ $imp->total_rows }}</td>
                                <td class="text-center text-danger fw-bold">{{ $imp->invalid_rows }}</td>
                                <td>
                                    @if($imp->status === 'completed')
                                        <span class="badge bg-success-subtle text-success badge-ios">Completado</span>
                                    @elseif($imp->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning badge-ios">Pendiente</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary badge-ios">{{ $imp->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-arrow-up fs-1 d-block mb-3"></i>
                                    <span>No se registran importaciones previas.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $imports->links() }}
            </div>
        </div>
    </div>

    <!-- Upload Box (Right) -->
    <div class="col-lg-4">
        <div class="ios-card">
            <h6 class="fw-bold mb-4"><i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Nueva Importación</h6>
            <p class="text-muted" style="font-size: 0.85rem;">Carga archivos CSV para dar de alta lotes, propietarios o facturar expensas diferenciadas.</p>

            <form method="POST" action="{{ route('admin.imports.upload') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="type" class="form-label fw-semibold" style="font-size: 0.85rem;">Tipo de Registro</label>
                    <select name="type" id="type" class="form-select form-control-ios" required onchange="togglePeriodField(this.value)">
                        <option value="lots">Lotes Físicos (lots)</option>
                        <option value="owners">Propietarios Titulares (owners)</option>
                        <option value="expenses">Expensas Mensuales (expenses)</option>
                    </select>
                </div>

                <!-- Dinámico: Período Borrador (solo para expensas) -->
                <div class="mb-3 d-none" id="period_field_container">
                    <label for="billing_period_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Período de Facturación (Borrador)</label>
                    <select name="billing_period_id" id="billing_period_id" class="form-select form-control-ios">
                        <option value="">-- Seleccionar Período Borrador --</option>
                        @foreach($draftPeriods as $dp)
                            <option value="{{ $dp->id }}">Período {{ $dp->period }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="file" class="form-label fw-semibold" style="font-size: 0.85rem;">Seleccionar Archivo CSV</label>
                    <input type="file" name="file" id="file" class="form-control form-control-ios" required accept=".csv,.txt">
                    <div id="format_help" class="form-text text-muted mt-2" style="font-size: 0.75rem;">
                        <strong>Estructura esperada:</strong><br>
                        • Lotes: <span class="font-monospace">number,code,name,internal_address,status,balance</span><br>
                        • Propietarios: <span class="font-monospace">name,last_name,email,phone,dni,cuit,preferred_channel</span><br>
                        • Expensas: <span class="font-monospace">lote,monto_expensa,monto_fondo_reserva,concepto_expensa,concepto_fondo_reserva</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-ios btn-ios-primary w-100"><i class="bi bi-upload me-1"></i> Cargar y Validar</button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePeriodField(val) {
    const container = document.getElementById('period_field_container');
    const input = document.getElementById('billing_period_id');
    if (val === 'expenses') {
        container.classList.remove('d-none');
        input.setAttribute('required', 'required');
    } else {
        container.classList.add('d-none');
        input.removeAttribute('required');
    }
}
</script>
@endsection
