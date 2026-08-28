@extends('layouts.app')

@section('title', $news->title)
@section('page_title', 'Comunicado Oficial')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ios-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-ios pb-3">
                <div>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios">ANUNCIO</span>
                    <small class="text-muted ms-2">Publicado el {{ $news->published_at ? $news->published_at->format('d/m/Y H:i') : '' }}</small>
                </div>
                <a href="{{ route('owner.news.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Volver</a>
            </div>

            <h3 class="fw-bold mb-3">{{ $news->title }}</h3>
            
            @if($news->summary)
                <p class="fs-5 text-muted mb-4 fw-semibold" style="line-height: 1.4;">{{ $news->summary }}</p>
            @endif

            <div class="news-content text-body" style="font-size: 1.05rem; line-height: 1.6; white-space: pre-line;">
                {{ $news->content }}
            </div>

            <div class="mt-5 border-top border-ios pt-4 d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 0.85rem;">Consorcio de Propietarios La Ranita</span>
                <a href="{{ route('owner.news.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver a Novedades</a>
            </div>
        </div>
    </div>
</div>
@endsection
