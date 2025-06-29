@extends('layouts.admin')

@section('title', 'tipo-Ambientes')

@section('content')
    <main class="content p-3 pagTipoAmbiente">  
        <div class="container-fluid">
            <div class="mb-3">
                <h3 class="mb-4 fw-bold">Lista de Ambientes</h3>
                <div class="row">
                    <div class="col-md-4">
                        <table class="table table-striped ">
                            <thead>
                                <tr>
                                    <th>Nros.</th>
                                    <th>Tipo de Ambiente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $contador = 1;
                                @endphp
                                @foreach ($tipoAmbientes as $tipoAmbiente)
                                <tr>
                                    <td>{{ $contador++ }}</td>
                                    <td>{{ $tipoAmbiente->nombre_amb }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-2" title="Editar" data-bs-toggle="modal" data-bs-target="#editTipoAmbienteModal" data-id="{{ $tipoAmbiente->id_tipoambiente }}" data-nombre="{{ $tipoAmbiente->nombre_amb }}"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar" onclick="eliminarTipoAmbiente('{{Crypt::encryptString($tipoAmbiente->id_tipoambiente) }}')"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>

                    <!-- Aagregar Tipo de Ambiente -->
                    <div class="col-md-5 ">
                        <div class="card">
                            <div class="card-body" >
                                <h3 class="card-title">Agregar Nuevo Tipo de Ambiente</h3>
                                <form action="{{ route('tipoambiente.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-2" >
                                        <input type="text" class="form-control w-50" id="tipo" name="tipo" placeholder="Tipo de Ambiente" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <!-- Modal para Editar Tipo de Ambiente -->
        <div class="modal fade" id="editTipoAmbienteModal" tabindex="-1" aria-labelledby="editTipoAmbienteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editTipoAmbienteForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTipoAmbienteModalLabel">Editar Tipo de Ambiente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editTipo" class="form-label">Tipo de Ambiente</label>
                                <input autofocus type="text" class="form-control" id="editTipo" name="tipo" required>
                            </div>
                            <div class="m-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <!--script para eliminar y mostrar-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar modal de edición con los datos correctos
            var modalEditarTipoAmbiente = document.getElementById('editTipoAmbienteModal');
            modalEditarTipoAmbiente.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var nombre = button.getAttribute('data-nombre');

                var inputTipo = document.getElementById('editTipo');
                inputTipo.value = nombre;

                // Establecer la acción del formulario para la edición
                var formEditarTipoAmbiente = document.getElementById('editTipoAmbienteForm');
                formEditarTipoAmbiente.action = "{{ route('tipoambiente.update', '') }}/" + id;
            });
        });

        // Función para eliminar tipo ambiente
        window.eliminarTipoAmbiente = function(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Este tipo de ambiente se eliminará permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.action = '{{ route('tipoambiente.destroy', '') }}/' + id;
                    form.method = 'POST';
                    form.innerHTML = '@csrf @method("DELETE")';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    <!--mensaje de registro exitoso-->
    @if(session('successregister'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('successregister') }}',
            });
        </script>
    @endif

    <!--Menasje de error al registrar -->
    @if(session('errorregister'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Registro ya existente',
                text: '{{ session('errorregister') }}',
            });
        </script>
    @endif

    <!--mensaje de exitoso-->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
            });
        </script>
    @endif

    <!--Menasje de error -->
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
    @if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: '{!! implode('', $errors->all('<div>:message</div>')) !!}',
        });
    </script>
    @endif
@endsection

