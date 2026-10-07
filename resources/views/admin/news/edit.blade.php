@extends('layouts.app')

@section('title', 'Editar Novedad')
@section('page_title', 'Modificar Publicación')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data">
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
                        <label for="title" class="form-label fw-semibold" style="font-size: 0.85rem;">Título de la Publicación <span class="text-danger">*</span></label>
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
                        <label for="content" class="form-label fw-semibold" style="font-size: 0.85rem;">Contenido Completo <span class="text-danger">*</span></label>
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

            <!-- Attachments & Media -->
            <div class="ios-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-paperclip text-success me-2"></i>Archivos Adjuntos e Imágenes</h5>
                
                <div class="row g-3">
                    <!-- File Attachment -->
                    <div class="col-md-6">
                        <label for="file" class="form-label fw-semibold" style="font-size: 0.85rem;">
                            <i class="bi bi-file-earmark-arrow-up text-primary me-1"></i> Documento / Archivo Adjunto
                        </label>

                        @if($news->file_path)
                            <div class="p-2 mb-2 bg-body-tertiary rounded-3 border border-ios d-flex align-items-center justify-content-between">
                                <div class="text-truncate me-2" style="font-size: 0.82rem;">
                                    <i class="bi bi-paperclip text-primary me-1"></i>
                                    <strong>{{ basename($news->file_path) }}</strong>
                                </div>
                                <a href="{{ asset('storage/' . $news->file_path) }}" target="_blank" class="btn btn-sm btn-ios btn-ios-secondary py-1 px-2" title="Descargar actual">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="remove_file" id="remove_file" value="1">
                                <label class="form-check-label text-danger" for="remove_file" style="font-size: 0.8rem;">
                                    Eliminar archivo adjunto actual
                                </label>
                            </div>
                        @endif

                        <input type="file" name="file" id="file" class="form-control form-control-ios @error('file') is-invalid @enderror">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">{{ $news->file_path ? 'Subir un nuevo archivo para reemplazar el existente.' : 'Formatos: PDF, DOCX, XLSX, ZIP, etc. (Máx. 25MB)' }}</small>
                        @error('file')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Cover Image -->
                    <div class="col-md-6">
                        <label for="image" class="form-label fw-semibold" style="font-size: 0.85rem;">
                            <i class="bi bi-image text-success me-1"></i> Imagen de Portada / Banner
                        </label>

                        @if($news->image_path)
                            <div class="p-2 mb-2 bg-body-tertiary rounded-3 border border-ios d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('storage/' . $news->image_path) }}" alt="Preview" class="rounded" style="width: 36px; height: 36px; object-fit: cover;">
                                    <span class="text-truncate" style="font-size: 0.82rem;">{{ basename($news->image_path) }}</span>
                                </div>
                                <a href="{{ asset('storage/' . $news->image_path) }}" target="_blank" class="btn btn-sm btn-ios btn-ios-secondary py-1 px-2" title="Ver imagen actual">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                <label class="form-check-label text-danger" for="remove_image" style="font-size: 0.8rem;">
                                    Eliminar imagen de portada actual
                                </label>
                            </div>
                        @endif

                        <input type="file" name="image" id="image" accept="image/*" class="form-control form-control-ios @error('image') is-invalid @enderror">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">{{ $news->image_path ? 'Subir una nueva imagen para reemplazar la existente.' : 'Formatos: JPG, PNG, WEBP (Máx. 10MB)' }}</small>
                        @error('image')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
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
                    <small class="text-muted d-block ms-1" style="font-size: 0.8rem;">Envía la versión actualizada con adjuntos por email a todos los propietarios.</small>
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
