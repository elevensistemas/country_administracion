@extends('layouts.app')

@section('title', 'Historia del Lote ' . $lot->number)
@section('page_title', 'Historia Integral del Lote ' . $lot->number)

@section('content')
<!-- Lot Summary Header Card -->
<div class="ios-card bg-body-secondary border-0 mb-4">
    <div class="row align-items-center g-3">
        <div class="col-md-3 border-end border-ios text-center py-2">
            <h1 class="display-3 fw-bold m-0 text-success">{{ $lot->number }}</h1>
            <span class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">Código: {{ $lot->code }}</span>
        </div>
        
        <div class="col-md-5 px-md-4 border-end border-ios">
            <div class="mb-2">
                <span class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-person-fill me-1"></i> Propietario Actual</span>
                <span class="fw-bold" style="font-size: 1rem;">
                    @if($lot->owner)
                        <a href="{{ route('admin.owners.show', $lot->owner) }}" class="text-success text-decoration-none">
                            {{ $lot->owner->full_name }}
                        </a>
                    @else
                        <span class="text-muted">Sin propietario</span>
                    @endif
                </span>
            </div>
            <div>
                <span class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-person-badge-fill me-1"></i> Inquilino Ocupante</span>
                <span class="fw-semibold" style="font-size: 0.95rem;">
                    {{ $lot->tenant ? $lot->tenant->full_name : 'No hay inquilino (Habita el propietario)' }}
                </span>
            </div>
        </div>

        <div class="col-md-4 px-md-4 text-end text-md-start">
            <div class="row g-2">
                <div class="col-6">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Saldo de Cuenta</span>
                    <strong class="fs-5 {{ $lot->balance > 0 ? 'text-danger' : ($lot->balance < 0 ? 'text-success' : '') }}">
                        ${{ number_format($lot->balance, 2, ',', '.') }}
                    </strong>
                </div>
                <div class="col-6">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Reclamos Abiertos</span>
                    <strong class="fs-5 text-warning">{{ $pendingTickets }} / {{ $ticketsCount }}</strong>
                </div>
                <div class="col-12 mt-2">
                    <div class="d-flex gap-2">
                        <!-- Add Note Button -->
                        <button type="button" class="btn btn-sm btn-ios btn-ios-primary flex-fill" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="bi bi-journal-plus me-1"></i> Agregar Nota
                        </button>
                        <!-- Export Button -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ios btn-ios-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-download me-1"></i> Exportar
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-ios shadow-sm rounded-3">
                                <li><a class="dropdown-item" href="{{ route('admin.lots.history.export', $lot) }}?format=csv"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Descargar CSV</a></li>
                                <li><a class="dropdown-item" target="_blank" href="{{ route('admin.lots.history.export', $lot) }}?format=print"><i class="bi bi-printer me-2"></i>Imprimir Historial</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Timeline -->
<div class="row">
    <!-- Filter sidebar left -->
    <div class="col-lg-3 mb-4">
        <div class="ios-card sticky-top" style="top: 100px; z-index: 100;">
            <h6 class="fw-bold mb-3"><i class="bi bi-funnel-fill text-success me-2"></i>Filtrar Historial</h6>
            
            <form method="GET" action="{{ route('admin.lots.history', $lot) }}">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size: 0.8rem;">Buscar en descripción</label>
                    <input type="text" name="search" class="form-control form-control-ios" placeholder="Buscar palabras..." value="{{ request('search') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size: 0.8rem;">Categorías</label>
                    <div class="form-check mb-1">
                        <input class="form-check-input text-success" type="radio" name="category" value="" id="cat_all" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label" for="cat_all">Todas</label>
                    </div>
                    @foreach($categories as $cat)
                        <div class="form-check mb-1">
                            <input class="form-check-input text-success" type="radio" name="category" value="{{ $cat->name }}" id="cat_{{ $cat->id }}" {{ request('category') === $cat->name ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label" for="cat_{{ $cat->id }}">{{ $cat->display_name }}</label>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size: 0.8rem;">Orden</label>
                    <select name="order" class="form-select form-control-ios" onchange="this.form.submit()">
                        <option value="desc" {{ request('order') !== 'asc' ? 'selected' : '' }}>Reciente a Antiguo</option>
                        <option value="asc" {{ request('order') === 'asc' ? 'selected' : '' }}>Antiguo a Reciente</option>
                    </select>
                </div>

                <div class="d-grid mt-4">
                    <a href="{{ route('admin.lots.history', $lot) }}" class="btn btn-ios btn-ios-secondary btn-sm">Limpiar Filtros</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Timeline Timeline -->
    <div class="col-lg-9">
        <div class="timeline-ios">
            @forelse($events as $event)
                <div class="timeline-item-ios">
                    <!-- Icon based on category -->
                    <div class="timeline-badge-ios border-success text-success">
                        @if($event->category->name === 'admin')
                            <i class="bi bi-person-fill text-success" style="font-size: 0.65rem;"></i>
                        @elseif($event->category->name === 'finance')
                            <i class="bi bi-wallet2 text-success" style="font-size: 0.65rem;"></i>
                        @elseif($event->category->name === 'security')
                            <i class="bi bi-shield-fill text-success" style="font-size: 0.65rem;"></i>
                        @elseif($event->category->name === 'maintenance')
                            <i class="bi bi-wrench-adjustable text-success" style="font-size: 0.65rem;"></i>
                        @elseif($event->category->name === 'inspections')
                            <i class="bi bi-exclamation-triangle-fill text-success" style="font-size: 0.65rem;"></i>
                        @else
                            <i class="bi bi-info text-success" style="font-size: 0.65rem;"></i>
                        @endif
                    </div>

                    <!-- Event Card -->
                    <div class="ios-card shadow-none border-1 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div>
                                <span class="badge bg-secondary-subtle text-secondary badge-ios me-2 mb-1" style="font-size: 0.65rem;">
                                    {{ $event->category->display_name }}
                                </span>
                                @if($event->is_confidential)
                                    <span class="badge bg-danger-subtle text-danger badge-ios mb-1" style="font-size: 0.65rem;"><i class="bi bi-eye-slash-fill me-1"></i>Interno Confidencial</span>
                                @endif
                                <h6 class="fw-bold m-0 mt-1" style="font-size: 1rem;">{{ $event->title }}</h6>
                            </div>
                            <div class="text-end">
                                <span class="text-muted d-block" style="font-size: 0.8rem;">
                                    {{ $event->event_date->format('d/m/Y H:i') }}
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    Por {{ $event->user ? $event->user->full_name : 'Sistema' }}
                                </small>
                            </div>
                        </div>

                        <!-- Event Description -->
                        <p class="m-0 mt-2 text-muted" style="font-size: 0.9rem; line-height: 1.5;">
                            {{ $event->description }}
                        </p>

                        <!-- Related records link -->
                        @if($event->related_model_type)
                            <div class="mt-3">
                                @php
                                    $linkRoute = null;
                                    $linkText = '';
                                    
                                    if ($event->related_model_type === \App\Models\Payment::class) {
                                        $linkRoute = route('admin.payments.show', $event->related_model_id);
                                        $linkText = 'Ver comprobante del pago';
                                    } elseif ($event->related_model_type === \App\Models\Ticket::class) {
                                        $linkRoute = route('admin.tickets.show', $event->related_model_id);
                                        $linkText = 'Abrir reclamo asociado';
                                    } elseif ($event->related_model_type === \App\Models\Expense::class) {
                                        $linkRoute = route('admin.expenses.index'); // redirect generally to expenses
                                        $linkText = 'Ir al módulo de expensas';
                                    }
                                @endphp
                                @if($linkRoute)
                                    <a href="{{ $linkRoute }}" class="btn btn-sm btn-ios btn-ios-secondary text-success py-1 px-3" style="font-size: 0.8rem;">
                                        <i class="bi bi-link-45deg me-1"></i> {{ $linkText }}
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- Attachments in Event -->
                        @if($event->attachments->count() > 0)
                            <div class="mt-3 border-top border-ios pt-2">
                                <span class="text-muted d-block mb-1" style="font-size: 0.75rem;"><i class="bi bi-paperclip"></i> Adjuntos</span>
                                @foreach($event->attachments as $att)
                                    <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="d-inline-flex align-items-center text-decoration-none text-success me-3 mb-1" style="font-size: 0.85rem;">
                                        <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> {{ $att->file_name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Associated Follow Ups -->
                        @if($event->followUps->count() > 0)
                            <div class="mt-3 bg-body-secondary p-3 rounded-4">
                                @foreach($event->followUps as $fu)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-warning-subtle text-warning badge-ios mb-1" style="font-size: 0.65rem;">Seguimiento Asignado</span>
                                            <h6 class="fw-bold m-0" style="font-size: 0.85rem;">{{ $fu->reason }}</h6>
                                            <small class="text-muted" style="font-size: 0.75rem;">Responsable: {{ $fu->assignee ? $fu->assignee->full_name : 'No asignado' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-secondary-subtle text-secondary badge-ios text-uppercase" style="font-size: 0.65rem;">{{ $fu->status }}</span>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Límite: {{ $fu->due_date->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted fs-1 d-block mb-3"></i>
                    <span class="text-muted">No hay eventos ni notas registradas en este lote.</span>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal: Add Manual Note -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom border-ios p-4">
                <h5 class="modal-title fw-bold" id="addNoteModalLabel"><i class="bi bi-journal-plus text-success me-2"></i>Agregar Nota al Historial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="{{ route('admin.lots.history.note', $lot) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="title" class="form-label fw-semibold" style="font-size: 0.85rem;">Título / Asunto</label>
                            <input type="text" name="title" id="title" class="form-control form-control-ios" required placeholder="Ej. Conversación telefónica con propietario">
                        </div>

                        <div class="col-md-4">
                            <label for="category_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Categoría del Evento</label>
                            <select name="category_id" id="category_id" class="form-select form-control-ios" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold" style="font-size: 0.85rem;">Descripción y Contenido de la Nota</label>
                            <textarea name="description" id="description" rows="5" class="form-control form-control-ios" required placeholder="Ingresa los detalles del acontecimiento o llamada..."></textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="priority" class="form-label fw-semibold" style="font-size: 0.85rem;">Prioridad / Impacto</label>
                            <select name="priority" id="priority" class="form-select form-control-ios" required>
                                <option value="low">Baja</option>
                                <option value="medium" selected>Media</option>
                                <option value="high">Alta</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="visibility" class="form-label fw-semibold" style="font-size: 0.85rem;">Visibilidad</label>
                            <select name="visibility" id="visibility" class="form-select form-control-ios" required>
                                <option value="internal" selected>Solo Interno (Admin y personal)</option>
                                <option value="public">Público (Visible para propietario/inquilino)</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end mb-2">
                            <div class="form-check">
                                <input class="form-check-input text-danger" type="checkbox" name="is_confidential" value="1" id="is_confidential">
                                <label class="form-check-label fw-semibold text-danger" for="is_confidential">
                                    Marcar como Confidencial
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="attachment" class="form-label fw-semibold" style="font-size: 0.85rem;">Adjuntar Archivo / Documento</label>
                            <input type="file" name="attachment" id="attachment" class="form-control form-control-ios">
                        </div>

                        <hr class="border-ios my-3">

                        <!-- Associated Follow Up Block -->
                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input class="form-check-input text-success" type="checkbox" name="create_followup" value="1" id="create_followup" onchange="toggleFollowUpFields(this)">
                                <label class="form-check-label fw-bold" for="create_followup">
                                    Crear una tarea de seguimiento asociada a esta nota
                                </label>
                            </div>

                            <div id="followup-fields" style="display: none;" class="bg-body-secondary p-3 rounded-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="followup_reason" class="form-label fw-semibold" style="font-size: 0.85rem;">Razón o Tarea a realizar</label>
                                        <input type="text" name="followup_reason" id="followup_reason" class="form-control form-control-ios" placeholder="Ej. Volver a llamar para verificar si envió el plano">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="followup_assignee_id" class="form-label fw-semibold" style="font-size: 0.85rem;">Responsable Asignado</label>
                                        <select name="followup_assignee_id" id="followup_assignee_id" class="form-select form-control-ios">
                                            @foreach($operators as $op)
                                                <option value="{{ $op->id }}" {{ $op->id == auth()->id() ? 'selected' : '' }}>
                                                    {{ $op->full_name }} ({{ $op->relationship_type }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="followup_due_date" class="form-label fw-semibold" style="font-size: 0.85rem;">Fecha Límite</label>
                                        <input type="date" name="followup_due_date" id="followup_due_date" class="form-control form-control-ios" min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-top border-ios p-4">
                    <button type="button" class="btn btn-ios btn-ios-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-ios btn-ios-primary px-4">Guardar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleFollowUpFields(checkbox) {
        const fields = document.getElementById('followup-fields');
        const reasonInput = document.getElementById('followup_reason');
        const assigneeSelect = document.getElementById('followup_assignee_id');
        const dateInput = document.getElementById('followup_due_date');

        if (checkbox.checked) {
            fields.style.display = 'block';
            reasonInput.setAttribute('required', 'required');
            assigneeSelect.setAttribute('required', 'required');
            dateInput.setAttribute('required', 'required');
        } else {
            fields.style.display = 'none';
            reasonInput.removeAttribute('required');
            assigneeSelect.removeAttribute('required');
            dateInput.removeAttribute('required');
        }
    }
</script>
@endsection
