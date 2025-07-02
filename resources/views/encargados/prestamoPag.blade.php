@extends('layouts.encargado')

@section('title', 'Registro de Préstamos')

@section('content')
<div class="content mt-4">

    <h1 class="text-center mb-4">Registro de Préstamos</h1>

    <!-- Botón de registrar préstamo -->
    <div class="d-flex justify-content-center mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrarPrestamo">
            <i class="bi bi-plus-circle me-1"></i> Registrar Préstamo
        </button>
    </div>

    <!-- Tabla de préstamos -->
    <div class="table-responsive">
        <table class="table table-striped table-hover border rounded shadow-sm">
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
                @php $nro = 1; @endphp
                @foreach($prestamos as $prestamo) 
                <tr>
                    <td>{{ $nro++ }}</td>
                    <td>{{ $prestamo->nombre_solicitante }}</td>
                    <td>{{ $prestamo->descripcion_prestamo }}</td>
                    <td>{{ \Carbon\Carbon::parse($prestamo->fch_prestamo)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($prestamo->hora_prestamo)->format('H:i') }}</td>
                    <td>{{ $prestamo->personal->nombre }}</td>
                    <td>
                        <button 
                            type="button"
                            class="btn btn-outline-secondary btn-sm agregar-detalles-btn" 
                            data-id="{{ encrypt($prestamo->id_prestamo) }}" 
                            data-original-id="{{ $prestamo->id_prestamo }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#agregarDetallesModal"
                            title="Agregar Detalles">
                            <i class="bi bi-plus-circle"></i> Detalles
                        </button>
                        <a href="#" class="btn btn-outline-info btn-sm" title="Ver Detalles">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

<!-- Modal para Registrar Préstamo -->
<div class="modal fade" id="modalRegistrarPrestamo" tabindex="-1" aria-labelledby="modalRegistrarPrestamoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Registrar Préstamo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('encargado.prestamo.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre del Solicitante</label>
                            <input type="text" name="nombre_solicitante" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Responsable</label>
                            <select name="PERSONAL_id_personal" class="form-select" required>
                                <option value="">Seleccione</option>
                                @foreach ($personal as $p)
                                    <option value="{{ $p->id_personal }}">{{ $p->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion_prestamo" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fch_prestamo" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora</label>
                            <input type="time" name="hora_prestamo" class="form-control" value="{{ now()->format('H:i') }}" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check2-circle me-1"></i> Registrar Préstamo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Detalles al Préstamo -->
<div class="modal fade" id="agregarDetallesModal" tabindex="-1" aria-labelledby="agregarDetallesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content shadow rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="agregarDetallesModalLabel">Agregar Detalles al Préstamo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formAgregarDetalles" method="POST" action="">
            @csrf
            <input type="hidden" name="PRESTAMO_id_prestamo" id="inputPrestamoId">

            <div id="detalles-container">
                <div class="detalle-row row g-2 mb-2">
                    <div class="col-md-5">
                        <input type="text" name="cod_articulo[]" class="form-control" placeholder="Código del artículo" required>
                    </div>
                    <div class="col-md-5">
                        <textarea name="observacion_detalle[]" class="form-control" placeholder="Observación" rows="1"></textarea>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-detalle w-100"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between my-3">
                <button type="button" class="btn btn-outline-primary" id="add-detalle"><i class="bi bi-plus-circle"></i> Agregar Otro</button>
                <button type="submit" class="btn btn-success">Guardar Detalles</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('agregarDetallesModal');
        const form = document.getElementById('formAgregarDetalles');
        const inputPrestamoId = document.getElementById('inputPrestamoId');

        // Cargar el ID y generar ruta cuando se abre el modal
        document.querySelectorAll('.agregar-detalles-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const idEncriptado = this.dataset.id;
                const idOriginal = this.dataset.originalId;

                inputPrestamoId.value = idOriginal;
                form.action = `/prestamos/detalle/${idEncriptado}`;
            });
        });

        // Agregar nuevas filas
        document.getElementById('add-detalle').addEventListener('click', function () {
            const container = document.getElementById('detalles-container');
            const row = container.querySelector('.detalle-row').cloneNode(true);
            row.querySelectorAll('input, textarea').forEach(el => el.value = '');
            container.appendChild(row);
        });

        // Eliminar fila
        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-detalle')) {
                const rows = document.querySelectorAll('.detalle-row');
                if (rows.length > 1) {
                    e.target.closest('.detalle-row').remove();
                }
            }
        });
    });
</script>


@endsection
