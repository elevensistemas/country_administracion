@extends('layouts.app')

@section('title', 'Nueva Comunicación')
@section('page_title', 'Crear Envío de Correo')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.comms.store') }}">
            @csrf

            <!-- Campaign Settings -->
            <div class="ios-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-send-fill text-success me-2"></i>Nueva Campaña de Correo</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label fw-semibold" style="font-size: 0.85rem;">Nombre de la Campaña (Interno)</label>
                        <input type="text" name="title" id="title" class="form-control form-control-ios @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Ej: Convocatoria Asamblea Ordinaria Octubre">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="communication_template_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Plantilla HTML Base (Opcional)</label>
                        <select name="communication_template_id" id="communication_template_id" class="form-select form-control-ios" onchange="applyTemplate(this)">
                            <option value="">Texto sin plantilla / Plano</option>
                            @foreach($templates as $temp)
                                <option value="{{ $temp->id }}" data-subject="{{ $temp->subject_template }}" data-body="{{ $temp->body_template }}">
                                    {{ $temp->name }} ({{ $temp->type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="target_type" class="form-label fw-semibold" style="font-size: 0.85rem;">Segmento Destinatario</label>
                        <select name="target_type" id="target_type" class="form-select form-control-ios" required onchange="toggleLotSelect(this)">
                            <option value="all_owners">Todos los Propietarios Registrados</option>
                            <option value="all_tenants">Todos los Inquilinos Registrados</option>
                            <option value="board">Miembros del Consejo / Directorio</option>
                            <option value="specific_lot">Propietarios de un Lote Específico</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="lot-select-block" style="display: none;">
                        <label for="lot_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Lote de Destino</label>
                        <select name="lot_id" id="lot_id" class="form-select form-control-ios">
                            <option value="">Selecciona el lote...</option>
                            @foreach($lots as $lot)
                                <option value="{{ $lot->id }}">Lote {{ $lot->number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="subject" class="form-label fw-semibold" style="font-size: 0.85rem;">Asunto del Correo (Subject)</label>
                        <input type="text" name="subject" id="subject" class="form-control form-control-ios @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required placeholder="Ej: Convocatoria Asamblea General Barrio La Ranita">
                        @error('subject')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="content" class="form-label fw-semibold" style="font-size: 0.85rem;">Contenido del Mensaje</label>
                        <textarea name="content" id="content" rows="10" class="form-control form-control-ios @error('content') is-invalid @enderror" required placeholder="Escribe aquí el cuerpo del correo. Puedes utilizar etiquetas dinámicas como {nombre_usuario} o {lote} que serán reemplazadas automáticamente."></textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="{{ route('admin.comms.index') }}" class="btn btn-ios btn-ios-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
                <button type="submit" class="btn btn-ios btn-ios-primary px-4"><i class="bi bi-send-fill me-2"></i>Enviar Campaña</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
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
        const subjectInput = document.getElementById('subject');
        const contentArea = document.getElementById('content');

        if (option.value) {
            subjectInput.value = option.getAttribute('data-subject');
            contentArea.value = option.getAttribute('data-body');
        }
    }
</script>
@endsection
