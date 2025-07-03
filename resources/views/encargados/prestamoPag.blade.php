@extends('layouts.encargado')

@section('title', 'Gestion de Prestamos')

@section('content')
<div class="content mt-4">

    <h1 class="text-center mb-4">Gestion de Préstamos</h1>

    <!-- Botón de registrar préstamo -->
    <div class="mb-3 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoPrestamo">
            <i class="bi bi-plus-circle me-1"></i> Registrar Préstamo
        </button>
    </div>

    <!-- Tabla de préstamos -->
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered rounded shadow-sm">
            <thead class="table-primary text-center">
                <tr>
                    <th>N°</th>
                    <th>Solicitante</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Responsable</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center align-middle">
                <!-- Aquí irá el loop con los préstamos (por ahora se puede dejar como ejemplo) -->
                @forelse($prestamos as $index => $prestamo) 
                <tr>
                    <td>{{ $prestamos->firstItem() + $index }}</td>
                    <td>{{ $prestamo->nombre_solicitante }}</td>
                    <td>{{ $prestamo->descripcion_prestamo }}</td>
                    <td>{{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($prestamo->hora_prestamo)->format('H:i') }}</td>
                    <td>{{ $prestamo->personal->nombre }}</td>
                    <td>
                        <!-- Botón Detalles -->
                        <a href="{{ route('encargado.prestamo.show', encrypt($prestamo->id_prestamo)) }}" class="btn btn-outline-info btn-sm" title="Ver Detalles">
                            <i class="bi bi-eye"></i>
                        </a>
                        <!-- Botón Devolución (solo si no se ha registrado aún) -->
                        @if(!$prestamo->devolucion)
                            <button 
                                class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalRegistrarDevolucion"
                                data-id="{{ $prestamo->id_prestamo }}"
                                data-url="{{ route('encargado.prestamo.devolucion', $prestamo->id_prestamo) }}"
                                title="Registrar Devolución"
                            >
                                <i class="bi bi-box-arrow-in-down"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="7">No hay préstamos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $prestamos->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal: Nuevo Préstamo -->
<div class="modal fade" id="modalNuevoPrestamo" tabindex="-1" aria-labelledby="modalNuevoPrestamoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow rounded-4">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalNuevoPrestamoLabel">Registrar Préstamo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formNuevoPrestamo" method="POST" action="{{ route('encargado.prestamo.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label"><strong>Nombre del Solicitante</strong></label>
                        <input type="text" name="nombre_solicitante" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Descripción</strong></label>
                        <textarea name="descripcion_prestamo" class="form-control" rows="3" required></textarea>
                    </div>
                    @php
                        $fechaHoraActual = \Carbon\Carbon::now()->timezone(config('app.timezone'));
                    @endphp
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Fecha del Préstamo</strong></label>
                            <input type="text" class="form-control" value="{{ $fechaHoraActual->format('d/m/Y') }}" readonly>
                            <input type="hidden" name="fch_prestamo" value="{{ $fechaHoraActual->toDateString() }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Hora del Préstamo</strong></label>
                            <input type="text" class="form-control" value="{{ $fechaHoraActual->format('H:i') }}" readonly>
                            <input type="hidden" name="hora_prestamo" value="{{ $fechaHoraActual->format('H:i') }}">
                        </div>
                    </div>
                    
                    <input type="hidden" name="PERSONAL_id_personal" value="{{ Auth::user()->id_personal }}">

                    <hr class="mb-3">

                    <h6>Artículos a Prestar</h6>
                    <div id="articulos-prestamo-container">
                        <div class="row g-2 mb-2 articulo-row">
                            <div class="col-md-5">
                            <input type="text" name="cod_articulo[]" class="form-control" placeholder="Código del artículo" required>
                            </div>
                            <div class="col-md-5">
                            <input type="text" name="observacion_detalle[]" class="form-control" placeholder="Observación (Opcional)">
                            </div>
                            <div class="col-md-2">
                            <button type="button" class="btn btn-danger w-100 remove-articulo" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between my-3">
                        <button type="button" class="btn btn-outline-success" id="add-articulo-prestamo"><i class="bi bi-plus-circle"></i> Agregar Artículo</button>
                        <button type="submit" class="btn btn-success">Registrar Préstamo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script: Agregar/Eliminar artículos -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('articulos-prestamo-container');

    document.getElementById('add-articulo-prestamo').addEventListener('click', () => {
      const row = container.querySelector('.articulo-row').cloneNode(true);
      row.querySelectorAll('input').forEach(input => input.value = '');
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

<!-- Modal: Registrar Devolución -->
<div class="modal fade" id="modalRegistrarDevolucion" tabindex="-1" aria-labelledby="modalRegistrarDevolucionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalRegistrarDevolucionLabel">Registrar Devolución</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="formRegistrarDevolucion" method="POST" action="">
                    @csrf
                    <input type="hidden" name="PRESTAMO_id_prestamo" id="prestamoDevolucionId">
                    <input type="hidden" name="PERSONAL_id_personal" value="{{ Auth::user()->id_personal }}">

                    <div class="mb-3">
                        <label for="descripcion_devolucion" class="form-label"><strong>Descripción de la Devolución</strong></label>
                        <textarea name="descripcion_devolucion" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label for="fch_devolucion" class="form-label"><strong>Fecha de Devolución</strong></label>
                            <input type="text" class="form-control" value="{{ now()->format('d/m/Y') }}" readonly>
                            <input type="hidden" name="fch_devolucion" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-6">
                            <label for="hora_devolucion" class="form-label"><strong>Hora de Devolución</strong></label>
                            <input type="text" class="form-control" value="{{ now()->format('H:i') }}" readonly>
                            <input type="hidden" name="hora_devolucion" value="{{ now()->format('H:i') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Registrar Devolución</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script: Configurar acción dinámica -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalRegistrarDevolucion');

    modal.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      const prestamoId = button.getAttribute('data-id');
      const actionUrl = button.getAttribute('data-url');

      document.getElementById('prestamoDevolucionId').value = prestamoId;
      document.getElementById('formRegistrarDevolucion').action = actionUrl;
    });
  });
</script>


<!-- SweetAlert2 Éxito -->
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

<!-- SweetAlert2 Error -->
@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif

<!-- SweetAlert2 Validaciones -->
@if ($errors->any())
<script>
    Swal.fire({
        icon: 'warning',
        title: 'Errores de Validación',
        html: `{!! implode('<br>', $errors->all()) !!}`,
        confirmButtonColor: '#f0ad4e',
        confirmButtonText: 'Revisar'
    });
</script>
@endif

@endsection
