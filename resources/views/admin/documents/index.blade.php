@extends('layouts.app')

@section('title', 'Documentos')
@section('page_title', 'Repositorio de Documentos')

@section('content')
<div class="row">
    <!-- Documents List (Left) -->
    <div class="col-lg-8 mb-4">
        <!-- Search -->
        <div class="ios-card mb-4">
            <form method="GET" action="{{ route('admin.documents.index') }}" class="row g-3 align-items-center">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control form-control-ios" placeholder="Buscar por título, código..." value="{{ request('search') }}">
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <select name="category_id" class="form-select form-control-ios" onchange="this.form.submit()">
                        <option value="">Todas las Categorías</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->display_name }}
                            </option>
                        @endforeach
                    </select>
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>

        <!-- Documents Table -->
        <div class="ios-card">
            <h5 class="fw-bold mb-4">Documentos y Actas Publicadas</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="border-bottom border-ios">
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">CÓDIGO</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">TÍTULO / CATEGORÍA</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 12%;">VERSIÓN</th>
                            <th class="text-muted" style="font-size: 0.85rem; font-weight: 600; width: 15%;">VISIBILIDAD</th>
                            <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 25%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr class="border-bottom border-ios">
                                <td class="fw-bold">{{ $doc->code }}</td>
                                <td>
                                    <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $doc->title }}</h6>
                                    <small class="badge bg-secondary-subtle text-secondary badge-ios mt-1" style="font-size: 0.65rem;">{{ $doc->category->display_name }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success badge-ios">V{{ $doc->current_version }}</span>
                                </td>
                                <td>
                                    @if($doc->is_public)
                                        <span class="badge bg-success text-white badge-ios"><i class="bi bi-globe me-1"></i>Público</span>
                                    @else
                                        <span class="badge bg-danger text-white badge-ios"><i class="bi bi-lock me-1"></i>Interno</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Download Current Version -->
                                        @if($doc->versions->count() > 0)
                                            <a href="{{ route('admin.documents.download-version', $doc->versions->sortByDesc('version_number')->first()) }}" class="btn btn-sm btn-ios btn-ios-secondary text-success" title="Descargar Versión Actual">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif

                                        <!-- Upload New Version Trigger -->
                                        <button type="button" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Subir Nueva Versión" data-bs-toggle="modal" data-bs-target="#newVersionModal-{{ $doc->id }}">
                                            <i class="bi bi-upload"></i> V+
                                        </button>

                                        <!-- Archive -->
                                        @if($doc->status === 'active')
                                            <form action="{{ route('admin.documents.archive', $doc) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger" title="Archivar Documento">
                                                    <i class="bi bi-archive-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal: Upload New Version -->
                            <div class="modal fade" id="newVersionModal-{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4">
                                        <div class="modal-header border-bottom border-ios p-4">
                                            <h5 class="modal-title fw-bold">Subir Nueva Versión (V{{ $doc->current_version + 1 }})</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <form method="POST" action="{{ route('admin.documents.version', $doc) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label for="file" class="form-label fw-semibold" style="font-size: 0.85rem;">Archivo de Documento</label>
                                                    <input type="file" name="file" class="form-control form-control-ios" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="change_log" class="form-label fw-semibold" style="font-size: 0.85rem;">Registro de Cambios (Change Log)</label>
                                                    <input type="text" name="change_log" class="form-control form-control-ios" required placeholder="Ej: Corrección de artículos en reglamento interno...">
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
                                    <span class="text-muted">No se encontraron documentos.</span>
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
                    <label for="code" class="form-label fw-semibold" style="font-size: 0.85rem;">Código de Documento</label>
                    <input type="text" name="code" id="code" class="form-control form-control-ios" required placeholder="Ej: REG-INT-01">
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold" style="font-size: 0.85rem;">Título / Nombre</label>
                    <input type="text" name="title" id="title" class="form-control form-control-ios" required placeholder="Ej: Reglamento Interno 2026">
                </div>

                <div class="mb-3">
                    <label for="document_category_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Categoría del Archivo</label>
                    <select name="document_category_id" id="document_category_id" class="form-select form-control-ios" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="is_public" class="form-label fw-semibold" style="font-size: 0.85rem;">Visibilidad Portal</label>
                    <select name="is_public" id="is_public" class="form-select form-control-ios" required>
                        <option value="1">Público (Vecinos pueden descargarlo)</option>
                        <option value="0">Interno (Solo personal administrativo)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="file" class="form-label fw-semibold" style="font-size: 0.85rem;">Seleccionar Archivo (V1)</label>
                    <input type="file" name="file" id="file" class="form-control form-control-ios" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold" style="font-size: 0.85rem;">Descripción Corta</label>
                    <textarea name="description" id="description" rows="3" class="form-control form-control-ios" placeholder="Detalle rápido del alcance del documento..."></textarea>
                </div>

                <button type="submit" class="btn btn-ios btn-ios-primary w-100"><i class="bi bi-check-lg me-1"></i>Publicar Documento</button>
            </form>
        </div>
    </div>
</div>
@endsection
