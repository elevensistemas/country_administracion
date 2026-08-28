@extends('layouts.app')

@section('title', 'Nuevo Reclamo')
@section('page_title', 'Crear Reclamo o Sugerencia')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('owner.tickets.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Ticket Core Details -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-chat-left-text-fill text-success me-2"></i>Detalles del Reclamo</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="lot_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Lote de Origen</label>
                        <select name="lot_id" id="lot_id" class="form-select form-control-ios @error('lot_id') is-invalid @enderror" required>
                            @foreach($lots as $lot)
                                <option value="{{ $lot->id }}" {{ old('lot_id') == $lot->id ? 'selected' : '' }}>
                                    Lote {{ $lot->number }}
                                </option>
                            @endforeach
                        </select>
                        @error('lot_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="ticket_category_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Categoría del Reclamo</label>
                        <select name="ticket_category_id" id="ticket_category_id" class="form-select form-control-ios @error('ticket_category_id') is-invalid @enderror" required>
                            <option value="">Selecciona la categoría</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('ticket_category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('ticket_category_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label for="title" class="form-label fw-semibold" style="font-size: 0.85rem;">Asunto / Título</label>
                        <input type="text" name="title" id="title" class="form-control form-control-ios @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Ej: Luminaria apagada en entrada">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="priority" class="form-label fw-semibold" style="font-size: 0.85rem;">Prioridad Estimada</label>
                        <select name="priority" id="priority" class="form-select form-control-ios @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Baja</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }} selected>Media</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                        </select>
                        @error('priority')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold" style="font-size: 0.85rem;">Descripción Detallada</label>
                        <textarea name="description" id="description" rows="5" class="form-control form-control-ios @error('description') is-invalid @enderror" required placeholder="Escribe aquí los detalles del inconveniente o sugerencia para que la administración lo investigue...">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-paperclip text-success me-2"></i>Adjuntar Foto o Archivo (Opcional)</h5>
                <p class="text-muted mb-4" style="font-size: 0.85rem;">Sube una foto del incidente si lo consideras útil (Límite 10MB).</p>
                <div class="col-12">
                    <input type="file" name="attachment" id="attachment" class="form-control form-control-ios">
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('owner.tickets.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4">Registrar Reclamo</button>
            </div>
        </form>
    </div>
</div>
@endsection
