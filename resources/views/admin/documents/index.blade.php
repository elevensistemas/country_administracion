@extends('layouts.app')

@section('title', 'Documentos')
@section('page_title', 'Repositorio de Documentos')

@section('content')
<div class="row">
    <!-- Documents List (Left) -->
    <div class="col-lg-8 mb-4">
        <!-- Search & Filter Card -->
        <div class="ios-card mb-4">
            <form method="GET" action="{{ route('admin.documents.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por nombre o descripción..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="category_id" class="form-select form-control-ios" onchange="this.form.submit()">
                        <option value="">Todas las Categorías</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-ios btn-ios-secondary" title="Restablecer filtros">Limpiar</a>
                </div>
            </form>
        </div>

        <!-- Documents Table -->
        <div class="ios-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Documentos y Actas Publicadas</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">DOCUMENTO / CATEGORÍA</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">VERSIÓN</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 18%;">VISIBILIDAD</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">ESTADO</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 22%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            @php
                                $latestVer = $doc->versions->sortByDesc('id')->first();
                            @endphp
                            <tr class="border-bottom border-ios">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="fs-4 text-primary">
                                            @if($latestVer && str_ends_with(strtolower($latestVer->file_name), '.pdf'))
                                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                                            @elseif($latestVer && (str_ends_with(strtolower($latestVer->file_name), '.doc') || str_ends_with(strtolower($latestVer->file_name), '.docx')))
                                                <i class="bi bi-file-earmark-word-fill text-primary"></i>
                                            @elseif($latestVer && (str_ends_with(strtolower($latestVer->file_name), '.xls') || str_ends_with(strtolower($latestVer->file_name), '.xlsx')))
                                                <i class="bi bi-file-earmark-excel-fill text-success"></i>
                                            @else
                                                <i class="bi bi-file-earmark-text-fill text-secondary"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $doc->name }}</h6>
                                            <small class="badge bg-secondary-subtle text-secondary badge-ios mt-1" style="font-size: 0.65rem;">{{ $doc->category->display_name ?? 'General' }}</small>
                                            @if($latestVer)
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                    {{ $latestVer->file_name }} ({{ number_format(($latestVer->file_size ?? 0) / 1024, 1) }} KB)
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($latestVer)
                                        <span class="badge bg-success-subtle text-success badge-ios">v{{ $latestVer->version }}</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary badge-ios">v1.0</span>
                                    @endif
                                </td>
                                <td>
                                    @if($doc->visibility === 'public')
                                        <span class="badge bg-success text-white badge-ios"><i class="bi bi-globe me-1"></i>Público</span>
                                    @elseif($doc->visibility === 'owners_only')
                                        <span class="badge bg-primary text-white badge-ios"><i class="bi bi-person-check me-1"></i>Propietarios</span>
                                    @elseif($doc->visibility === 'board_only')
                                        <span class="badge bg-warning text-dark badge-ios"><i class="bi bi-shield me-1"></i>Consejo</span>
                                    @else
                                        <span class="badge bg-danger text-white badge-ios"><i class="bi bi-lock me-1"></i>Interno</span>
                                    @endif
                                </td>
                                <td>
                                    @if($doc->is_archived)
                                        <span class="badge bg-secondary text-white badge-ios">Archivado</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success badge-ios">Activo</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Download Current Version -->
                                        @if($latestVer)
                                            <a href="{{ route('admin.documents.download-version', $latestVer) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Descargar Versión Actual">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif

                                        <!-- Upload New Version Trigger -->
                                        <button type="button" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Subir Nueva Versión" data-bs-toggle="modal" data-bs-target="#newVersionModal-{{ $doc->id }}">
                                            <i class="bi bi-upload"></i>
                                        </button>

                                        <!-- Archive / Unarchive -->
                                        @if(!$doc->is_archived)
                                            <form action="{{ route('admin.documents.archive', $doc) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-warning" title="Archivar Documento">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.documents.unarchive', $doc) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Restaurar Documento">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Delete -->
                                        <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar permanentemente este documento y sus versiones?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger" title="Eliminar">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal: Upload New Version -->
                            <div class="modal fade" id="newVersionModal-{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4">
                                        <div class="modal-header border-bottom border-ios p-4">
                                            <h5 class="modal-title fw-bold">Subir Nueva Versión de "{{ $doc->name }}"</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <form method="POST" action="{{ route('admin.documents.version', $doc) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label for="version" class="form-label fw-semibold" style="font-size: 0.85rem;">Etiqueta de Versión (Opcional)</label>
                                                    <input type="text" name="version" class="form-control form-control-ios" placeholder="Ej: 1.1, 2.0 (Dejar vacío para auto-incrementar)">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="file" class="form-label fw-semibold" style="font-size: 0.85rem;">Archivo de Documento <span class="text-danger">*</span></label>
                                                    <input type="file" name="file" class="form-control form-control-ios" required>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Formatos: PDF, Word, Excel, etc. (Máx. 25MB)</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="notes" class="form-label fw-semibold" style="font-size: 0.85rem;">Notas de la Versión (Opcional)</label>
                                                    <input type="text" name="notes" class="form-control form-control-ios" placeholder="Ej: Corrección de artículos en reglamento interno...">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top border-ios p-4">
                                                <button type="button" class="btn btn-ios btn-ios-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-ios btn-ios-primary">Cargar Nueva Versión</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-file-earmark-x text-muted fs-1 d-block mb-3"></i>
                                    <span class="text-muted">No se encontraron documentos registrados.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        </div>
    </div>

    <!-- Upload Document Form (Right) -->
    <div class="col-lg-4">
        <div class="ios-card">
            <h6 class="fw-bold mb-4"><i class="bi bi-file-earmark-arrow-up-fill text-success me-2"></i>Registrar Documento</h6>
            
            <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre del Documento <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control form-control-ios @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Ej: Reglamento Interno de Convivencia">
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Categoría <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select form-control-ios @error('category_id') is-invalid @enderror" required>
                        <option value="">Seleccionar categoría...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->display_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="visibility" class="form-label fw-semibold" style="font-size: 0.85rem;">Visibilidad</label>
                    <select name="visibility" id="visibility" class="form-select form-control-ios" required>
                        <option value="public" {{ old('visibility', 'public') === 'public' ? 'selected' : '' }}>Público (Vecinos y residentes)</option>
                        <option value="owners_only" {{ old('visibility') === 'owners_only' ? 'selected' : '' }}>Solo Propietarios</option>
                        <option value="board_only" {{ old('visibility') === 'board_only' ? 'selected' : '' }}>Solo Consejo de Administración</option>
                        <option value="internal" {{ old('visibility') === 'internal' ? 'selected' : '' }}>Interno (Administración)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="file" class="form-label fw-semibold" style="font-size: 0.85rem;">Archivo Digital (PDF, Word, Excel) <span class="text-danger">*</span></label>
                    <input type="file" name="file" id="file" class="form-control form-control-ios @error('file') is-invalid @enderror" required>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Formatos admitidos: PDF, DOCX, XLSX, etc. (Máx. 25MB)</small>
                    @error('file')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="version" class="form-label fw-semibold" style="font-size: 0.85rem;">Versión Inicial (Opcional)</label>
                    <input type="text" name="version" id="version" class="form-control form-control-ios" value="{{ old('version', '1.0') }}" placeholder="1.0">
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold" style="font-size: 0.85rem;">Descripción o Resumen</label>
                    <textarea name="description" id="description" rows="3" class="form-control form-control-ios" placeholder="Detalle rápido del alcance del documento...">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="btn btn-ios btn-ios-primary w-100"><i class="bi bi-check-lg me-1"></i>Publicar Documento</button>
            </form>
        </div>
    </div>
</div>
@endsection
