@extends('layouts.app')

@section('title', 'Nueva Novedad')
@section('page_title', 'Crear Publicación')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- News Core Details -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-megaphone-fill text-success me-2"></i>Detalles del Comunicado</h5>
                
                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label fw-semibold" style="font-size: 0.85rem;">Título de la Publicación <span class="text-danger">*</span></label>
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
                        <label for="content" class="form-label fw-semibold" style="font-size: 0.85rem;">Contenido Completo <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" rows="8" class="form-control form-control-ios @error('content') is-invalid @enderror" required placeholder="Escribe aquí el cuerpo del mensaje...">{{ old('content') }}</textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold" style="font-size: 0.85rem;">Estado Inicial</label>
                        <select name="status" id="status" class="form-select form-control-ios" required>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Borrador</option>
                            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Publicada inmediatamente</option>
                            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archivada</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="visibility" class="form-label fw-semibold" style="font-size: 0.85rem;">Visibilidad</label>
                        <select name="visibility" id="visibility" class="form-select form-control-ios" required>
                            <option value="public" {{ old('visibility', 'public') === 'public' ? 'selected' : '' }}>Pública (Visible en Portal Vecinos)</option>
                            <option value="internal" {{ old('visibility') === 'internal' ? 'selected' : '' }}>Interna (Solo personal administrativo)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="published_at" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha Programación (Opcional)</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control form-control-ios" value="{{ old('published_at') }}">
                    </div>
                </div>
            </div>

            <!-- Attachments & Media -->
            <div class="ios-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-paperclip text-success me-2"></i>Archivos Adjuntos e Imágenes</h5>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Puedes adjuntar un documento (PDF, Word, Excel, ZIP, etc.) o una imagen de portada para la publicación:</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="file" class="form-label fw-semibold" style="font-size: 0.85rem;">
                            <i class="bi bi-file-earmark-arrow-up text-primary me-1"></i> Documento / Archivo Adjunto
                        </label>
                        <input type="file" name="file" id="file" class="form-control form-control-ios @error('file') is-invalid @enderror">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Formatos admitidos: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR, JPG, PNG (Máx. 25MB)</small>
                        @error('file')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="image" class="form-label fw-semibold" style="font-size: 0.85rem;">
                            <i class="bi bi-image text-success me-1"></i> Imagen de Portada / Banner (Opcional)
                        </label>
                        <input type="file" name="image" id="image" accept="image/*" class="form-control form-control-ios @error('image') is-invalid @enderror">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Formatos: JPG, PNG, WEBP (Máx. 10MB)</small>
                        @error('image')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Notification Options -->
            <div class="ios-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-bell-fill text-success me-2"></i>Notificaciones a Propietarios</h5>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Configura los canales de aviso a los propietarios cuando el estado sea "Publicada":</p>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="notify_portal" id="notify_portal" value="1" checked>
                    <label class="form-check-label fw-semibold" for="notify_portal" style="font-size: 0.9rem;">
                        <i class="bi bi-app-indicator text-success me-1"></i> Notificar en la campanita del Portal
                    </label>
                    <small class="text-muted d-block ms-1" style="font-size: 0.8rem;">Genera un aviso inmediato en el panel web y móvil de cada propietario.</small>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="send_email" id="send_email" value="1" checked>
                    <label class="form-check-label fw-semibold" for="send_email" style="font-size: 0.9rem;">
                        <i class="bi bi-envelope-check-fill text-success me-1"></i> Enviar aviso por Correo Electrónico
                    </label>
                    <small class="text-muted d-block ms-1" style="font-size: 0.8rem;">Envía el comunicado completo por email con el archivo adjunto a todos los propietarios activos.</small>
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
