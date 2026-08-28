@extends('layouts.app')

@section('title', 'Nueva Novedad')
@section('page_title', 'Crear Publicación')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.news.store') }}">
            @csrf

            <!-- News Core Details -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-megaphone-fill text-success me-2"></i>Detalles del Comunicado</h5>
                
                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label fw-semibold" style="font-size: 0.85rem;">Título de la Publicación</label>
                        <input type="text" name="title" id="title" class="form-control form-control-ios @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Ej: Convocatoria a Asamblea General Ordinaria">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="summary" class="form-label fw-semibold" style="font-size: 0.85rem;">Resumen o Bajada (Opcional)</label>
                        <input type="text" name="summary" id="summary" class="form-control form-control-ios @error('summary') is-invalid @enderror" value="{{ old('summary') }}" placeholder="Ej: Se convoca a todos los propietarios para el día 15 de Octubre.">
                        @error('summary')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="content" class="form-label fw-semibold" style="font-size: 0.85rem;">Contenido Completo</label>
                        <textarea name="content" id="content" rows="8" class="form-control form-control-ios @error('content') is-invalid @enderror" required placeholder="Escribe aquí el cuerpo del mensaje..."></textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold" style="font-size: 0.85rem;">Estado Inicial</label>
                        <select name="status" id="status" class="form-select form-control-ios" required>
                            <option value="draft">Borrador</option>
                            <option value="published" selected>Publicada inmediatamente</option>
                            <option value="archived">Archivada</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="visibility" class="form-label fw-semibold" style="font-size: 0.85rem;">Visibilidad</label>
                        <select name="visibility" id="visibility" class="form-select form-control-ios" required>
                            <option value="public" selected>Pública (Visible en Portal Vecinos)</option>
                            <option value="internal">Interna (Solo personal administrativo)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="published_at" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha Programación (Opcional)</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control form-control-ios">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.news.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Publicar</button>
            </div>
        </form>
    </div>
</div>
@endsection
