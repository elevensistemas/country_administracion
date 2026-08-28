@extends('layouts.app')

@section('title', 'Nuevo Período')
@section('page_title', 'Crear Período de Facturación')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="ios-card">
            <h5 class="fw-bold mb-4"><i class="bi bi-calendar-plus text-success me-2"></i>Período de Facturación</h5>
            
            <form method="POST" action="{{ route('admin.expenses.store-period') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="period" class="form-label fw-semibold" style="font-size: 0.85rem;">Período (AAAA-MM)</label>
                    <input type="text" name="period" id="period" class="form-control form-control-ios @error('period') is-invalid @enderror" value="{{ old('period') }}" required placeholder="Ej: 2026-09">
                    @error('period')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Debe tener el formato de año y mes con guión intermedio.</small>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha de Inicio</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-ios @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                    @error('start_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="end_date" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha de Fin</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-ios @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                    @error('end_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                    <button type="submit" class="btn btn-ios btn-ios-primary px-4">Crear Período</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
