@extends('layouts.app')

@section('title', 'Registrar Reclamo')
@section('page_title', 'Registrar Nuevo Reclamo')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-12">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0 text-success"><i class="bi bi-chat-square-text-fill me-2"></i>Registrar Incidente Administrativo</h5>
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-ios btn-ios-secondary">Volver</a>
        </div>

        <div class="ios-card">
            <form method="POST" action="{{ route('admin.tickets.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Lote and Resident -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="lot_id" class="form-label fw-bold" style="font-size: 0.85rem;"><i class="bi bi-house-fill text-success me-1"></i>Lote Relacionado</label>
                        <select name="lot_id" id="lot_id" class="form-select form-control-ios @error('lot_id') is-invalid @enderror" required onchange="filterResidentsByLot(this.value)">
                            <option value="">-- Seleccionar Lote --</option>
                            @foreach($lots as $lot)
                                <option value="{{ $lot->id }}" {{ old('lot_id') == $lot->id ? 'selected' : '' }}>
                                    Lote {{ $lot->number }} - {{ $lot->owner ? $lot->owner->full_name : 'Sin Propietario' }}
                                </option>
                            @endforeach
                        </select>
                        @error('lot_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="user_id" class="form-label fw-bold" style="font-size: 0.85rem;"><i class="bi bi-person-fill text-success me-1"></i>Vecino Reportante</label>
                        <select name="user_id" id="user_id" class="form-select form-control-ios @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Residente --</option>
                            @foreach($users as $u)
                                @php
                                    $lotIds = $u->functionalUnits->pluck('lot_id')->unique()->toArray();
                                @endphp
                                <option value="{{ $u->id }}" data-lots="{{ json_encode($lotIds) }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->full_name }} ({{ $u->email }}) - {{ ucfirst($u->relationship_type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Category, Priority and Channel -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="category_id" class="form-label fw-bold" style="font-size: 0.85rem;">Categoría</label>
                        <select name="category_id" id="category_id" class="form-select form-control-ios @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Seleccionar Categoría --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->display_name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="priority" class="form-label fw-bold" style="font-size: 0.85rem;">Prioridad</label>
                        <select name="priority" id="priority" class="form-select form-control-ios @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Media</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('priority')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="source_channel" class="form-label fw-bold" style="font-size: 0.85rem;">Canal de Entrada</label>
                        <select name="source_channel" id="source_channel" class="form-select form-control-ios @error('source_channel') is-invalid @enderror" required>
                            <option value="phone" {{ old('source_channel', 'phone') == 'phone' ? 'selected' : '' }}>Llamada Telefónica</option>
                            <option value="in_person" {{ old('source_channel') == 'in_person' ? 'selected' : '' }}>Presencial / Oficina</option>
                            <option value="email" {{ old('source_channel') == 'email' ? 'selected' : '' }}>Correo Electrónico</option>
                            <option value="web" {{ old('source_channel') == 'web' ? 'selected' : '' }}>Portal Web</option>
                        </select>
                        @error('source_channel')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Subject and Description -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold" style="font-size: 0.85rem;">Asunto / Título del Reclamo</label>
                    <input type="text" name="title" id="title" class="form-control form-control-ios @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Resumen rápido del incidente (ej: Pérdida de agua calzada principal)">
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold" style="font-size: 0.85rem;">Detalles del Incidente</label>
                    <textarea name="description" id="description" rows="5" class="form-control form-control-ios @error('description') is-invalid @enderror" required placeholder="Describe minuciosamente lo reportado por el vecino...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Attachment -->
                <div class="mb-4">
                    <label for="attachment" class="form-label fw-bold" style="font-size: 0.85rem;"><i class="bi bi-paperclip me-1"></i>Adjuntar Comprobante o Foto (Opcional)</label>
                    <input type="file" name="attachment" id="attachment" class="form-control form-control-ios @error('attachment') is-invalid @enderror">
                    <small class="text-muted">Tamaño máximo permitido: 10 MB.</small>
                    @error('attachment')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-ios btn-ios-primary btn-success">Registrar Reclamo en Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterResidentsByLot(lotId) {
    const userSelect = document.getElementById('user_id');
    const options = userSelect.options;

    // Reset resident selection
    userSelect.value = '';

    if (!lotId) {
        // Show all options
        for (let i = 0; i < options.length; i++) {
            options[i].disabled = false;
            options[i].style.display = '';
        }
        return;
    }

    // Filter options matching selected lot
    for (let i = 1; i < options.length; i++) {
        const option = options[i];
        try {
            const associatedLots = JSON.parse(option.getAttribute('data-lots') || '[]');
            const isAssociated = associatedLots.map(String).includes(String(lotId));

            if (isAssociated) {
                option.disabled = false;
                option.style.display = '';
            } else {
                option.disabled = true;
                option.style.display = 'none';
            }
        } catch (e) {
            console.error("Error parsing associated lots", e);
        }
    }
}
</script>
@endsection
