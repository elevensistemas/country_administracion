@extends('layouts.app')

@section('title', 'Validación de Importación')
@section('page_title', 'Validar Registros')

@section('content')
<div class="ios-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold m-0">Revisión de Registros Cargados</h5>
            <small class="text-muted">Archivo: {{ $import->file_name }} • Registros: {{ $import->total_rows }}</small>
        </div>
        <form method="POST" action="{{ route('admin.imports.process', $import) }}">
            @csrf
            <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-circle-fill me-1"></i> Confirmar e Importar</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" style="font-size: 0.9rem;">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 10%;">FILA</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DATOS A IMPORTAR</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">VALIDACIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($import->rows as $row)
                    <tr class="border-bottom border-ios">
                        <td>Fila #{{ $row->row_number }}</td>
                        <td>
                            @foreach($row->data as $k => $v)
                                <span class="badge bg-secondary-subtle text-secondary me-2 mb-1" style="font-size: 0.75rem;">
                                    <strong>{{ $k }}:</strong> {{ $v }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @if($row->status === 'invalid')
                                <span class="badge bg-danger-subtle text-danger badge-ios"><i class="bi bi-x-circle-fill"></i> Inválido</span>
                                <div class="text-danger mt-1" style="font-size: 0.78rem;">
                                    @if(is_array($row->errors))
                                        @foreach($row->errors as $err)
                                            <div>• {{ $err }}</div>
                                        @endforeach
                                    @else
                                        {{ $row->errors }}
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-success-subtle text-success badge-ios"><i class="bi bi-check-circle"></i> Válido</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
