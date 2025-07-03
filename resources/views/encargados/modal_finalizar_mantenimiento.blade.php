<!-- Modal: Finalizar Mantenimiento -->
<div class="modal fade" id="modalFinalizarMantenimiento" tabindex="-1" aria-labelledby="modalFinalizarMantenimientoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalFinalizarMantenimientoLabel">Finalizar Mantenimiento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form id="formFinalizarMantenimiento" method="POST" action="">
          @csrf
          @method('POST')

          <input type="hidden" name="INICIO_MANTENIMIENTO_id_mantenimiento_ini" id="mantenimientoFinalId">

          <div class="mb-3">
            <label for="informe_final" class="form-label"><strong>Informe Final</strong></label>
            <textarea name="informe_final" class="form-control" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label for="fch_final" class="form-label"><strong>Fecha de Finalización</strong></label>
            <input type="text" class="form-control" value="{{ now()->format('d/m/Y') }}" readonly>
            <input type="hidden" name="fch_final" value="{{ now()->toDateString() }}">
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Guardar Finalización</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Script: Configurar modal dinámicamente -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalFinalizarMantenimiento');

    modal.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      const mantenimientoId = button.getAttribute('data-id');
      const actionUrl = button.getAttribute('data-url');

      document.getElementById('mantenimientoFinalId').value = mantenimientoId;
      document.getElementById('formFinalizarMantenimiento').action = actionUrl;
    });
  });
</script>

@if ($errors->any() && session()->get('_old_input')['informe_final'] ?? false)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('modalFinalizarMantenimiento'));
        modal.show();
    });
</script>
@endif