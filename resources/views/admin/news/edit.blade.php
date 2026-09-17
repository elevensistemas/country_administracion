@extends('layouts.app')

@section('title', 'Editar Novedad')
@section('page_title', 'Modificar Publicación')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.news.update', $news) }}">
            @csrf
            @method('PUT')

            <!-- News Core Details -->
            <div class="ios-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0"><i class="bi bi-megaphone-fill text-success me-2"></i>Detalles del Comunicado</h5>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios">ID: {{ $news->id }}</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label fw-semibold" style="font-size: 0.85rem;">Título de la Publicación</label>
                        <input type="text" name="title" id="title" class="form-control form-control-ios @error('title') is-invalid @enderror" value="{{ old('title', $news->title) }}" required>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="summary" class="form-label fw-semibold" style="font-size: 0.85rem;">Resumen o Bajada (Opcional)</label>
                        <input type="text" name="summary" id="summary" class="form-control form-control-ios @error('summary') is-invalid @enderror" value="{{ old('summary', $news->summary) }}">
                        @error('summary')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="content" class="form-label fw-semibold" style="font-size: 0.85rem;">Contenido Completo</label>
                        <textarea name="content" id="content" rows="8" class="form-control form-control-ios @error('content') is-invalid @enderror" required>{{ old('content', $news->content) }}</textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold" style="font-size: 0.85rem;">Estado</label>
                        <select name="status" id="status" class="form-select form-control-ios" required>
                            <option value="draft" {{ old('status', $news->status) === 'draft' ? 'selected' : '' }}>Borrador</option>
                            <option value="published" {{ old('status', $news->status) === 'published' ? 'selected' : '' }}>Publicada</option>
                            <option value="archived" {{ old('status', $news->status) === 'archived' ? 'selected' : '' }}>Archivada</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="visibility" class="form-label fw-semibold" style="font-size: 0.85rem;">Visibilidad</label>
                        <select name="visibility" id="visibility" class="form-select form-control-ios" required>
                            <option value="public" {{ old('visibility', $news->visibility) === 'public' ? 'selected' : '' }}>Pública (Portal Vecinos)</option>
                            <option value="internal" {{ old('visibility', $news->visibility) === 'internal' ? 'selected' : '' }}>Interna (Administración)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="published_at" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha Publicación</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control form-control-ios" value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
            </div>

            <!-- Notification Options -->
            <div class="ios-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-bell-fill text-success me-2"></i>Re-notificar a Propietarios</h5>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Si realizaste cambios importantes y deseas enviar nuevamente un aviso a los propietarios:</p>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="notify_portal" id="notify_portal" value="1">
                    <label class="form-check-label fw-semibold" for="notify_portal" style="font-size: 0.9rem;">
                        <i class="bi bi-app-indicator text-success me-1"></i> Re-enviar aviso a la campanita del Portal
                    </label>
                    <small class="text-muted d-block ms-1" style="font-size: 0.8rem;">Vuelve a colocar una alerta en el panel de todos los vecinos.</small>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="send_email" id="send_email" value="1">
                    <label class="form-check-label fw-semibold" for="send_email" style="font-size: 0.9rem;">
                        <i class="bi bi-envelope-check-fill text-success me-1"></i> Re-enviar por Correo Electrónico
                    </label>
                    <small class="text-muted d-block ms-1" style="font-size: 0.8rem;">Envía la versión actualizada por email a todos los propietarios.</small>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.news.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-check-lg me-2"></i>Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
