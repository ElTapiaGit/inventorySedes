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
                <label class="form-label"><strong>Informe Inicial</strong></label>
                <textarea name="informe_inicial" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label"><strong>Fecha de Inicio</strong></label>
                <input type="text" class="form-control" value="{{ now()->format('d/m/Y') }}" readonly>
                <input type="hidden" name="fch_inicio" value="{{ now()->toDateString() }}">
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-8">
                    <label class="form-label"><strong>Técnico</strong></label>
                    <select name="TECNICO_id_tecnico" class="form-select" required>
                        <option value="">Seleccione un técnico</option>
                        @foreach ($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id_tecnico }}">{{ $tecnico->nombre }} {{ $tecnico->ap_paterno }}</option>
                        @endforeach
                    </select>
                </div>
                    
                <input type="hidden" name="PERSONAL_id_personal" value="{{ Auth::user()->id_personal }}">
            </div>

            <hr class="mb-3">

            <h6>Artículos en Mantenimiento</h6>
            <div id="articulos-container">
                <div class="row g-2 mb-2 articulo-row">
                <div class="col-md-10">
                    <input type="text" name="cod_articulo[]" class="form-control" placeholder="Código del artículo" required>
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('articulos-container');

        document.getElementById('add-articulo').addEventListener('click', () => {
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
    });
</script>
