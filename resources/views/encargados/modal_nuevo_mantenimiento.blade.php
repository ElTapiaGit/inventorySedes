<!-- Modal: Nuevo Mantenimiento -->
<div class="modal fade" id="modalNuevoMantenimiento" tabindex="-1" aria-labelledby="modalNuevoMantenimientoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content shadow rounded-4">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalNuevoMantenimientoLabel">Registrar Mantenimiento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form id="formNuevoMantenimiento" method="POST" action="{{ route('encargado.mantenimiento.store') }}">
            @csrf
            <div class="mb-3">
                <label for="informe_inicial" class="form-label"><strong>Informe Inicial</strong></label>
                <textarea id="informe_inicial" name="informe_inicial" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="fch_inicio_display" class="form-label"><strong>Fecha de Inicio</strong></label>
                <input id="fch_inicio_display" type="text" class="form-control" value="{{ now()->format('d/m/Y') }}" readonly>
                <input type="hidden" name="fch_inicio" value="{{ now()->toDateString() }}">
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-7">
                    <label class="form-label" for="selectTecnicoMantenimiento"><strong>Técnico <span class="text-danger">*</span></strong></label>
                    <div class="input-group">
                        <select name="TECNICO_id_tecnico" id="selectTecnicoMantenimiento" class="form-select" required>
                            <option value="">Seleccione un técnico</option>
                            @foreach ($tecnicos as $tecnico)
                                <option value="{{ $tecnico->id_tecnico }}">{{ trim($tecnico->nombre . ' ' . $tecnico->ap_paterno . ' ' . $tecnico->ap_materno) }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" id="btnAbrirModalTecnico" title="Registrar nuevo tecnico">
                            <i class="bi bi-person-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="responsable_mantenimiento_display" class="form-label"><strong>Responsable</strong></label>
                    <input id="responsable_mantenimiento_display" type="text" class="form-control bg-light" value="{{ Auth::user()->nombre_completo ?? (Auth::user()->nombre . ' ' . Auth::user()->ap_paterno) }}" readonly>
                    <input type="hidden" name="PERSONAL_id_personal" value="{{ Auth::user()->id_personal }}">
                </div>
            </div>

            <hr class="mb-3">

            <h6>Artículos en Mantenimiento</h6>
            <div id="articulos-container">
                <div class="row g-2 mb-2 articulo-row">
                <div class="col-md-10">
                    <input type="text" name="cod_articulo[]" class="form-control" placeholder="Código del artículo" aria-label="Código del artículo" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger w-100 remove-articulo" title="Eliminar"><i class="bi bi-trash"></i></button>
                </div>
                </div>
            </div>

            <div class="d-flex justify-content-between my-3">
                <button type="button" class="btn btn-outline-success" id="add-articulo"><i class="bi bi-plus-circle"></i> Agregar Artículo</button>
                <button type="submit" class="btn btn-success">Registrar Mantenimiento</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Script: Agregar/Eliminar artículos -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('articulos-container');
        const addBtn = document.getElementById('add-articulo');

        if (container && addBtn) {
            addBtn.addEventListener('click', () => {
                const row = container.querySelector('.articulo-row').cloneNode(true);
                row.querySelector('input').value = '';
                container.appendChild(row);
            });

            document.addEventListener('click', function (e) {
                if (e.target.closest('.remove-articulo')) {
                    const rows = container.querySelectorAll('.articulo-row');
                    if (rows.length > 1) {
                        e.target.closest('.articulo-row').remove();
                    }
                }
            });
        }
    });
</script>
@endpush
