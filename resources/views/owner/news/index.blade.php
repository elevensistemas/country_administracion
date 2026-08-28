@extends('layouts.app')

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
                            <small class="text-muted">{{ $n->published_at ? $n->published_at->format('d/m/Y') : '' }}</small>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $n->title }}</h5>
                        <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.5;">{{ $n->summary ?? Str::limit(strip_tags($n->content), 120) }}</p>
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
