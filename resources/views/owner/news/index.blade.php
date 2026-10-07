@extends('layouts.owner')

@section('title', 'Novedades')
@section('page_title', 'Novedades y Anuncios')

@section('content')
<div class="ios-card">
    <h5 class="fw-bold mb-4">Novedades del Barrio</h5>

    <div class="row g-4">
        @forelse($news as $n)
            <div class="col-md-6">
                <div class="border-ios p-4 rounded-4 bg-body-tertiary h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-secondary-subtle text-secondary badge-ios">COMUNICADO</span>
                            <small class="text-muted">{{ $n->published_at ? $n->published_at->format('d/m/Y') : ($n->created_at ? $n->created_at->format('d/m/Y') : '') }}</small>
                        </div>
                        
                        @if($n->image_path)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $n->image_path) }}" alt="{{ $n->title }}" class="img-fluid rounded-3" style="max-height: 160px; width: 100%; object-fit: cover;">
                            </div>
                        @endif

                        <h5 class="fw-bold mb-2">{{ $n->title }}</h5>
                        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5;">{{ $n->summary ?? Str::limit(strip_tags($n->content), 120) }}</p>
                        
                        @if($n->file_path)
                            <div class="mb-3">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-paperclip me-1"></i> Contiene archivo adjunto ({{ strtoupper(pathinfo($n->file_path, PATHINFO_EXTENSION)) }})
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('owner.news.show', $n) }}" class="btn btn-ios btn-ios-secondary">Leer Completo</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-megaphone fs-1 d-block mb-3"></i>
                <span>No hay anuncios o novedades publicadas recientemente.</span>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $news->links() }}
    </div>
</div>
@endsection
