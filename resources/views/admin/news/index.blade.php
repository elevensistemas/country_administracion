@extends('layouts.app')

@section('title', 'Novedades')
@section('page_title', 'Administración de Novedades')

@section('content')
<!-- Search & filters -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('admin.news.index') }}" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted" style="border-radius: 12px 0 0 12px; border-color: var(--ios-border);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-ios border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Buscar por título, contenido..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-4">
            <select name="status" class="form-select form-control-ios" onchange="this.form.submit()">
                <option value="">Todos los Estados</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Borrador</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicadas</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archivadas</option>
            </select>
        </div>

        <div class="col-md-3 d-grid">
            <a href="{{ route('admin.news.index') }}" class="btn btn-ios btn-ios-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- News List -->
<div class="ios-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Lista de Novedades</h5>
        <a href="{{ route('admin.news.create') }}" class="btn btn-ios btn-ios-primary"><i class="bi bi-plus-circle me-2"></i>Nueva Publicación</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="border-bottom border-ios">
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">FECHA PUB.</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">TITULO</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">RESUMEN</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">VISIBILIDAD</th>
                    <th class="text-muted" style="font-size: 0.85rem; font-weight: 600;">ESTADO</th>
                    <th class="text-muted text-end" style="font-size: 0.85rem; font-weight: 600; width: 15%;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($news as $n)
                    <tr class="border-bottom border-ios">
                        <td style="font-size: 0.9rem;">
                            {{ $n->published_at ? $n->published_at->format('d/m/Y H:i') : 'Programada' }}
                        </td>
                        <td>
                            <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $n->title }}</h6>
                        </td>
                        <td class="text-muted text-truncate" style="font-size: 0.85rem; max-width: 300px;">
                            {{ $n->summary ?? 'Sin resumen' }}
                        </td>
                        <td>
                            @if($n->visibility === 'public')
                                <span class="badge bg-success-subtle text-success badge-ios">Público (Vecinos)</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger badge-ios">Interno (Administrativo)</span>
                            @endif
                        </td>
                        <td>
                            @if($n->status === 'published')
                                <span class="badge bg-success text-white badge-ios">Publicada</span>
                            @elseif($n->status === 'draft')
                                <span class="badge bg-warning text-dark badge-ios">Borrador</span>
                            @else
                                <span class="badge bg-secondary text-white badge-ios">Archivada</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <!-- Edit -->
                                <a href="{{ route('admin.news.edit', $n) }}" class="btn btn-sm btn-ios btn-ios-secondary text-primary" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.news.destroy', $n) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta novedad?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ios btn-ios-secondary text-danger" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-megaphone text-muted fs-1 d-block mb-3"></i>
                            <span class="text-muted">No se encontraron novedades.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $news->links() }}
    </div>
</div>
@endsection
