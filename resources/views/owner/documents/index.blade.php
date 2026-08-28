@extends('layouts.app')

@section('title', 'Documentos')
@section('page_title', 'Repositorio de Documentos')

@section('content')
<!-- Search -->
<div class="ios-card mb-4">
    <form method="GET" action="{{ route('owner.documents.index') }}" class="row g-3 align-items-center">
        <div class="col-md-9">
            <input type="text" name="search" class="form-control form-control-ios" placeholder="Buscar por título, palabra clave..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3 d-grid">
            <button type="submit" class="btn btn-ios btn-ios-primary">Buscar</button>
        </div>
    </form>
</div>

<!-- Documents grid -->
<div class="ios-card">
    <h5 class="fw-bold mb-4">Reglamentos, Actas e Informes</h5>

    <div class="row g-4">
        @forelse($documents as $doc)
            <div class="col-md-6 col-lg-4">
                <div class="border-ios p-3 rounded-4 bg-body-tertiary h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success-subtle text-success badge-ios" style="font-size: 0.65rem;">PÚBLICO</span>
                            <span class="badge bg-secondary-subtle text-secondary badge-ios" style="font-size: 0.65rem;">V{{ $doc->current_version }}</span>
                        </div>
                        <h6 class="fw-bold m-0" style="font-size: 0.95rem;">{{ $doc->title }}</h6>
                        <small class="text-muted d-block mt-1">{{ $doc->category->display_name }}</small>
                        
                        @if($doc->description)
                            <p class="text-muted m-0 mt-3" style="font-size: 0.8rem; line-height: 1.4;">{{ $doc->description }}</p>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        @if($doc->versions->count() > 0)
                            @php
                                $currentVersion = $doc->versions->sortByDesc('version_number')->first();
                            @endphp
                            <a href="{{ route('owner.documents.download-version', $currentVersion) }}" class="btn btn-sm btn-ios btn-ios-primary w-100">
                                <i class="bi bi-download me-1"></i> Descargar ({{ number_format($currentVersion->file_size / 1024, 1) }} KB)
                            </a>
                        @else
                            <button class="btn btn-sm btn-ios btn-ios-secondary w-100 disabled" disabled>Sin archivo cargado</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                <span>No se encontraron documentos públicos en el repositorio.</span>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $documents->links() }}
    </div>
</div>
@endsection
