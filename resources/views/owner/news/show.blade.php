@extends('layouts.owner')

@section('title', $news->title)
@section('page_title', 'Comunicado Oficial')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ios-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-ios pb-3">
                <div>
                    <span class="badge bg-secondary-subtle text-secondary badge-ios">ANUNCIO</span>
                    <small class="text-muted ms-2">Publicado el {{ $news->published_at ? $news->published_at->format('d/m/Y H:i') : ($news->created_at ? $news->created_at->format('d/m/Y H:i') : '') }}</small>
                </div>
                <a href="{{ route('owner.news.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Volver</a>
            </div>

            <!-- Cover Image if exists -->
            @if($news->image_path)
                <div class="mb-4 text-center">
                    <img src="{{ asset('storage/' . $news->image_path) }}" alt="{{ $news->title }}" class="img-fluid rounded-4 shadow-sm" style="max-height: 380px; width: 100%; object-fit: cover;">
                </div>
            @endif

            <h3 class="fw-bold mb-3">{{ $news->title }}</h3>
            
            @if($news->summary)
                <p class="fs-5 text-muted mb-4 fw-semibold" style="line-height: 1.4;">{{ $news->summary }}</p>
            @endif

            <div class="news-content text-body" style="font-size: 1.05rem; line-height: 1.6; white-space: pre-line;">
                {{ $news->content }}
            </div>

            <!-- Attachment Download Box if exists -->
            @if($news->file_path)
                @php
                    $ext = strtolower(pathinfo($news->file_path, PATHINFO_EXTENSION));
                    $filename = basename($news->file_path);
                    $icon = 'bi-file-earmark-arrow-down-fill text-secondary';
                    if (in_array($ext, ['pdf'])) {
                        $icon = 'bi-file-earmark-pdf-fill text-danger';
                    } elseif (in_array($ext, ['doc', 'docx'])) {
                        $icon = 'bi-file-earmark-word-fill text-primary';
                    } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                        $icon = 'bi-file-earmark-excel-fill text-success';
                    } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $icon = 'bi-file-earmark-image-fill text-info';
                    } elseif (in_array($ext, ['zip', 'rar'])) {
                        $icon = 'bi-file-earmark-zip-fill text-warning';
                    }
                @endphp
                <div class="mt-4 p-3 rounded-4 bg-body-tertiary border border-ios d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-1 d-flex align-items-center justify-content-center">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <div>
                            <span class="fw-bold d-block text-body">{{ $filename }}</span>
                            <small class="text-muted text-uppercase">Documento Adjunto ({{ strtoupper($ext) }})</small>
                        </div>
                    </div>
                    <div>
                        <a href="{{ asset('storage/' . $news->file_path) }}" target="_blank" download class="btn btn-ios btn-ios-primary">
                            <i class="bi bi-download me-1"></i> Descargar Adjunto
                        </a>
                    </div>
                </div>
            @endif

            <div class="mt-5 border-top border-ios pt-4 d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 0.85rem;">Consorcio de Propietarios La Ranita</span>
                <a href="{{ route('owner.news.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver a Novedades</a>
            </div>
        </div>
    </div>
</div>
@endsection
