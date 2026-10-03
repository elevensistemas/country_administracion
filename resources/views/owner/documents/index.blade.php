@extends('layouts.owner')

@section('title', 'Documentos del Barrio')

@section('content')
<div class="row g-3">
    <!-- Header -->
    <div class="col-12 mb-1">
        <h4 class="fw-bold m-0 text-success"><i class="bi bi-file-earmark-text me-2"></i>Documentos y Reglamentos</h4>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Boletines oficiales, reglamentos internos y actas del Club de Campo La Ranita.</p>
    </div>

    <!-- Search -->
    <div class="col-12">
        <div class="ios-card p-3">
            <form method="GET" action="{{ route('owner.documents.index') }}" class="row g-2 align-items-center">
                <div class="col-9 col-md-10">
                    <input type="text" name="search" class="form-control form-control-ios" placeholder="Buscar por nombre, palabra clave..." value="{{ request('search') }}">
                </div>
                <div class="col-3 col-md-2 d-grid">
                    <button type="submit" class="btn btn-ios btn-ios-primary"><i class="bi bi-search me-1"></i>Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents grid -->
    <div class="col-12">
        <div class="row g-3">
            @forelse($documents as $doc)
                <div class="col-md-6 col-lg-4">
                    <div class="ios-card bg-body-tertiary h-100 d-flex flex-column justify-content-between p-3">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-success-subtle text-success badge-ios" style="font-size: 0.7rem;">PÚBLICO</span>
                                @if($doc->versions->count() > 0)
                                    <span class="badge bg-secondary-subtle text-secondary badge-ios" style="font-size: 0.7rem;">v{{ $doc->versions->first()->version }}</span>
                                @endif
                            </div>
                            <h6 class="fw-bold text-dark m-0" style="font-size: 0.95rem;">{{ $doc->name }}</h6>
                            <small class="text-success fw-semibold d-block mt-1" style="font-size: 0.78rem;">{{ $doc->category->display_name ?? 'General' }}</small>
                            
                            @if($doc->description)
                                <p class="text-muted m-0 mt-2" style="font-size: 0.82rem; line-height: 1.4;">{{ $doc->description }}</p>
                            @endif
                        </div>
                        
                        <div class="mt-3 pt-2 border-top border-ios">
                            @if($doc->versions->count() > 0)
                                @php
                                    $currentVersion = $doc->versions->first();
                                @endphp
                                <a href="{{ route('owner.documents.download-version', $currentVersion) }}" class="btn btn-sm btn-ios btn-ios-primary w-100">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Descargar Documento
                                </a>
                            @else
                                <button class="btn btn-sm btn-ios btn-ios-secondary w-100 disabled" disabled>Sin archivo adjunto</button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="ios-card p-4">
                        <i class="bi bi-file-earmark-x text-muted fs-1 d-block mb-2"></i>
                        <span class="text-muted">No se encontraron documentos disponibles en esta sección.</span>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $documents->links() }}
        </div>
    </div>
</div>
@endsection
