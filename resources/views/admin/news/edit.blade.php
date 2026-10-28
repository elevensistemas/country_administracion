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

            <!-- Scope / Lot Selection -->
            @php
                $currentScope = old('recipients_type', $news->recipients_type ?? 'all');
                $currentLots = old('lot_ids', $selectedLotIds ?? []);
            @endphp
            <div class="ios-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0"><i class="bi bi-people-fill text-success me-2"></i>Destinatarios / Alcance</h5>
                    <span id="selected-badge-pill" class="badge {{ $currentScope === 'specific_lots' ? 'bg-warning-subtle text-dark border border-warning-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-3 py-1">
                        {{ $currentScope === 'specific_lots' ? 'Lotes específicos' : 'Todos los lotes' }}
                    </span>
                </div>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Indique si esta novedad está dirigida a todos los residentes del barrio o exclusivamente a lotes específicos:</p>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="scope-option-card d-block p-3 rounded-4 border {{ $currentScope === 'all' ? 'border-primary bg-primary-subtle bg-opacity-10' : 'border-ios bg-body' }} h-100 cursor-pointer" for="scope_all" style="cursor: pointer; transition: all 0.2s ease;">
                            <div class="form-check m-0">
                                <input class="form-check-input" type="radio" name="recipients_type" id="scope_all" value="all" {{ $currentScope === 'all' ? 'checked' : '' }} onchange="toggleLotSelector(false)">
                                <label class="form-check-label fw-bold ms-2 text-body cursor-pointer" for="scope_all">
                                    <i class="bi bi-globe text-primary me-1"></i> Todos los lotes
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2 ms-4" style="font-size: 0.8rem;">
                                Comunicado general para todos los propietarios e inquilinos del barrio.
                            </small>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="scope-option-card d-block p-3 rounded-4 border {{ $currentScope === 'specific_lots' ? 'border-warning bg-warning-subtle bg-opacity-10' : 'border-ios bg-body' }} h-100 cursor-pointer" for="scope_specific" style="cursor: pointer; transition: all 0.2s ease;">
                            <div class="form-check m-0">
                                <input class="form-check-input" type="radio" name="recipients_type" id="scope_specific" value="specific_lots" {{ $currentScope === 'specific_lots' ? 'checked' : '' }} onchange="toggleLotSelector(true)">
                                <label class="form-check-label fw-bold ms-2 text-body cursor-pointer" for="scope_specific">
                                    <i class="bi bi-geo-alt-fill text-warning me-1"></i> Lotes específicos
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2 ms-4" style="font-size: 0.8rem;">
                                Elegir manualmente uno o varios lotes para segmentar la publicación.
                            </small>
                        </label>
                    </div>
                </div>

                @error('lot_ids')
                    <div class="alert alert-danger py-2 px-3 rounded-3 mb-3" style="font-size: 0.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                    </div>
                @enderror

                <!-- Lot Selector Box -->
                <div id="lot-selector-container" class="{{ $currentScope === 'specific_lots' ? '' : 'd-none' }} pt-3 border-top border-ios">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="flex-grow-1" style="max-width: 360px;">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="lot-search-input" class="form-control form-control-ios form-control-sm border-start-0" style="border-radius: 0 10px 10px 0;" placeholder="Buscar por N° lote o propietario..." oninput="filterLots(this.value)">
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-ios btn-ios-secondary py-1 px-2" onclick="selectAllLots(true)" style="font-size: 0.8rem;">
                                <i class="bi bi-check-all me-1"></i>Seleccionar Todos
                            </button>
                            <button type="button" class="btn btn-sm btn-ios btn-ios-secondary py-1 px-2" onclick="selectAllLots(false)" style="font-size: 0.8rem;">
                                <i class="bi bi-x-circle me-1"></i>Deseleccionar Todos
                            </button>
                            <span id="selected-count-badge" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.8rem;">
                                0 seleccionados
                            </span>
                        </div>
                    </div>

                    <!-- Scrollable lot grid -->
                    <div class="lot-selection-grid p-2 rounded-3 bg-body-tertiary border border-ios" style="max-height: 320px; overflow-y: auto;">
                        <div class="row g-2" id="lots-container">
                            @foreach($lots as $lot)
                                @php
                                    $isSelected = is_array($currentLots) && in_array($lot->id, $currentLots);
                                    $ownerName = $lot->owner ? $lot->owner->full_name : ($lot->name ?: '');
                                    $searchString = strtolower("lote {$lot->number} {$lot->code} {$lot->name} {$ownerName}");
                                @endphp
                                <div class="col-6 col-sm-4 col-md-3 lot-item-col" data-search="{{ $searchString }}">
                                    <label class="lot-card-pill d-flex align-items-center p-2 rounded-3 border h-100 cursor-pointer {{ $isSelected ? 'border-success bg-success-subtle text-success' : 'border-ios bg-body text-body' }}" for="lot_check_{{ $lot->id }}" style="cursor: pointer; transition: all 0.15s ease;">
                                        <input type="checkbox" name="lot_ids[]" id="lot_check_{{ $lot->id }}" value="{{ $lot->id }}" class="form-check-input me-2 m-0 lot-checkbox" {{ $isSelected ? 'checked' : '' }} onchange="onLotCheckboxChange(this)">
                                        <div class="text-truncate">
                                            <span class="fw-bold d-block text-truncate" style="font-size: 0.85rem;">Lote {{ $lot->number }}</span>
                                            @if($ownerName)
                                                <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">{{ $ownerName }}</small>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div id="no-lots-found" class="text-center text-muted py-4 d-none">
                            <i class="bi bi-search fs-4 d-block mb-1"></i>
                            <span style="font-size: 0.85rem;">No se encontraron lotes coincidentes.</span>
                        </div>
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
                <h5 class="fw-bold mb-3"><i class="bi bi-bell-fill text-success me-2"></i>Re-notificar a Destinatarios</h5>
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Si realizaste cambios importantes y deseas enviar nuevamente un aviso a los destinatarios:</p>
                
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="notify_portal" id="notify_portal" value="1">
                    <label class="form-check-label fw-semibold" for="notify_portal" style="font-size: 0.9rem;">
                        <i class="bi bi-app-indicator text-success me-1"></i> Re-enviar aviso a la campanita del Portal
                    </label>
                    <small class="text-muted d-block ms-1" style="font-size: 0.8rem;">Vuelve a colocar una alerta en el panel de los vecinos correspondientes.</small>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="send_email" id="send_email" value="1">
                    <label class="form-check-label fw-semibold" for="send_email" style="font-size: 0.9rem;">
                        <i class="bi bi-envelope-check-fill text-success me-1"></i> Re-enviar por Correo Electrónico
                    </label>
                    <small class="text-muted d-block ms-1" style="font-size: 0.8rem;">Envía la versión actualizada con adjuntos por email a los destinatarios correspondientes.</small>
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

<script>
function toggleLotSelector(isSpecific) {
    const container = document.getElementById('lot-selector-container');
    const badge = document.getElementById('selected-badge-pill');
    const cardAll = document.querySelector('label[for="scope_all"]');
    const cardSpecific = document.querySelector('label[for="scope_specific"]');

    if (isSpecific) {
        container.classList.remove('d-none');
        badge.textContent = 'Lotes específicos';
        badge.className = 'badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1';
        
        cardSpecific.classList.add('border-warning', 'bg-warning-subtle', 'bg-opacity-10');
        cardSpecific.classList.remove('border-ios', 'bg-body');
        
        cardAll.classList.remove('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
        cardAll.classList.add('border-ios', 'bg-body');
    } else {
        container.classList.add('d-none');
        badge.textContent = 'Todos los lotes';
        badge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1';

        cardAll.classList.add('border-primary', 'bg-primary-subtle', 'bg-opacity-10');
        cardAll.classList.remove('border-ios', 'bg-body');

        cardSpecific.classList.remove('border-warning', 'bg-warning-subtle', 'bg-opacity-10');
        cardSpecific.classList.add('border-ios', 'bg-body');
    }
    updateLotCount();
}

function filterLots(query) {
    const q = query.trim().toLowerCase();
    const items = document.querySelectorAll('.lot-item-col');
    let visibleCount = 0;

    items.forEach(item => {
        const text = item.getAttribute('data-search') || '';
        if (q === '' || text.includes(q)) {
            item.classList.remove('d-none');
            visibleCount++;
        } else {
            item.classList.add('d-none');
        }
    });

    const noFound = document.getElementById('no-lots-found');
    if (visibleCount === 0) {
        noFound.classList.remove('d-none');
    } else {
        noFound.classList.add('d-none');
    }
}

function selectAllLots(selectAll) {
    const visibleItems = document.querySelectorAll('.lot-item-col:not(.d-none) .lot-checkbox');
    visibleItems.forEach(cb => {
        cb.checked = selectAll;
        const pill = cb.closest('.lot-card-pill');
        if (selectAll) {
            pill.classList.add('border-success', 'bg-success-subtle', 'text-success');
            pill.classList.remove('border-ios', 'bg-body', 'text-body');
        } else {
            pill.classList.remove('border-success', 'bg-success-subtle', 'text-success');
            pill.classList.add('border-ios', 'bg-body', 'text-body');
        }
    });
    updateLotCount();
}

function onLotCheckboxChange(cb) {
    const pill = cb.closest('.lot-card-pill');
    if (cb.checked) {
        pill.classList.add('border-success', 'bg-success-subtle', 'text-success');
        pill.classList.remove('border-ios', 'bg-body', 'text-body');
    } else {
        pill.classList.remove('border-success', 'bg-success-subtle', 'text-success');
        pill.classList.add('border-ios', 'bg-body', 'text-body');
    }
    updateLotCount();
}

function updateLotCount() {
    const checked = document.querySelectorAll('.lot-checkbox:checked').length;
    const badge = document.getElementById('selected-count-badge');
    badge.textContent = checked + (checked === 1 ? ' lote seleccionado' : ' lotes seleccionados');
    if (checked > 0) {
        badge.className = 'badge bg-success text-white border border-success px-2 py-1';
    } else {
        badge.className = 'badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateLotCount();
});
</script>
@endsection
