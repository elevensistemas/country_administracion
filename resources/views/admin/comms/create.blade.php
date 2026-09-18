@extends('layouts.app')

@section('title', 'Nueva Comunicación')
@section('page_title', 'Crear y Enviar Comunicación')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form method="POST" action="{{ route('admin.comms.store') }}">
            @csrf

            <!-- Campaign Settings -->
            <div class="ios-card mb-4">
                <h5 class="fw-bold mb-4 text-body"><i class="bi bi-send-fill text-success me-2"></i>Redactar Comunicación</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label fw-semibold text-body" style="font-size: 0.85rem;">Asunto / Título de la Comunicación</label>
                        <input type="text" name="title" id="title" class="form-control form-control-ios @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Ej: Convocatoria a Asamblea Ordinaria">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="communication_template_id" class="form-label fw-semibold text-body" style="font-size: 0.85rem;">Plantilla Base (Opcional)</label>
                        <select name="communication_template_id" id="communication_template_id" class="form-select form-control-ios" onchange="applyTemplate(this)">
                            <option value="">Texto sin plantilla / Personalizado</option>
                            @foreach($templates as $temp)
                                <option value="{{ $temp->id }}" data-subject="{{ $temp->subject ?? $temp->name }}" data-body="{{ $temp->body }}">
                                    {{ $temp->name }} ({{ strtoupper($temp->channels ?? 'Email') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="target_type" class="form-label fw-semibold text-body" style="font-size: 0.85rem;">Destinatarios / Segmento</label>
                        <select name="target_type" id="target_type" class="form-select form-control-ios" required onchange="toggleLotSelect(this)">
                            <option value="all_owners">Todos los Propietarios Registrados</option>
                            <option value="all_tenants">Todos los Inquilinos Registrados</option>
                            <option value="board">Miembros del Consejo de Administración</option>
                            <option value="specific_lot">Residentes de un Lote Específico</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="lot-select-block" style="display: none;">
                        <label for="lot_id" class="form-label fw-semibold text-body" style="font-size: 0.85rem;">Lote de Destino</label>
                        <select name="lot_id" id="lot_id" class="form-select form-control-ios">
                            <option value="">Selecciona el lote...</option>
                            @foreach($lots as $lot)
                                <option value="{{ $lot->id }}">Lote {{ $lot->number }} - {{ $lot->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="content" class="form-label fw-semibold text-body" style="font-size: 0.85rem;">Cuerpo del Mensaje</label>
                        <textarea name="content" id="content" rows="10" class="form-control form-control-ios @error('content') is-invalid @enderror" required placeholder="Escribe aquí el cuerpo del mensaje o comunicado..."></textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.comms.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-send-fill me-2"></i>Enviar Comunicación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleLotSelect(select) {
        const block = document.getElementById('lot-select-block');
        const lotInput = document.getElementById('lot_id');
        
        if (select.value === 'specific_lot') {
            block.style.display = 'block';
            lotInput.setAttribute('required', 'required');
        } else {
            block.style.display = 'none';
            lotInput.removeAttribute('required');
        }
    }

    function applyTemplate(select) {
        const option = select.options[select.selectedIndex];
        const titleInput = document.getElementById('title');
        const contentArea = document.getElementById('content');

        if (option.value) {
            if (option.getAttribute('data-subject')) {
                titleInput.value = option.getAttribute('data-subject');
            }
            if (option.getAttribute('data-body')) {
                contentArea.value = option.getAttribute('data-body');
            }
        }
    }
</script>
@endpush
