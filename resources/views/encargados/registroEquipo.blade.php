@extends('layouts.encargado')

@section('title', 'Gestion Equipos')

@section('content')
<div class="container-fluid">
    <div class="container mt-2">
        <h2 class="text-center">Registro de Equipos</h2>
        
        <!-- Formulario para registrar la foto -->
        <div id="fotoForm" class="card mb-4">
            <div class="card-header">
                <h4>Registrar Foto</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('foto.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="foto" class="form-label">Selecciona una foto (.jpg, .jpeg, .png)</label>
                        <input type="file" name="foto" id="foto" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Subir Foto </button>
                </form>
            </div>
            <div class="d-flex justify-content-center">
                <div id="previewContainer" class="d-none px-5">
                    <h5>Previsualización:</h5>
                    <img id="photoPreview" src="" alt="Previsualización de la Foto" class="img-fluid border">
                </div>
            </div>
            <script>
                document.getElementById('foto').addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const photoPreview = document.getElementById('photoPreview');
                            const previewContainer = document.getElementById('previewContainer');
                            
                            photoPreview.src = e.target.result;
                            previewContainer.classList.remove('d-none');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            </script>
        </div>
    
        <!-- Formulario para registrar el equipo, inicialmente oculto -->
        <div id="equipoForm" class="card" style="display: none;">
            <div class="card-header">
                <h4>Datos Equipo</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('equipo.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Agrupación de Código, Nombre, Marca y Modelo en la misma línea -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="cod_equipo" class="form-label">Codigo Equipo</label>
                                <input type="text" class="form-control" id="cod_equipo" name="cod_equipo" required oninput="actualizarRutaImagen()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="nombre_equi" class="form-label">Nombre del Equipo</label>
                                <input type="text" class="form-control" id="nombre_equi" name="nombre_equi" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca" maxlength="20" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" maxlength="50" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Agrupación de Descripción y Observaciones en la misma línea -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="descripcion_equi" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion_equi" name="descripcion_equi" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones_equi" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones_equi" name="observaciones_equi" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Agrupación de Empotrado, Estado, Vida Útil y Ambiente en la misma línea -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="empotrado" class="form-label">¿Empotrado?</label>
                                <select class="form-select" id="empotrado" name="empotrado" required>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="estado_equi" class="form-label">Estado del Equipo</label>
                                <input type="text" class="form-control" id="estado_equi" name="estado_equi" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="vida_util" class="form-label">Vida Útil</label>
                                <input type="text" class="form-control" id="vida_util" name="vida_util" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="ambiente" class="form-label">Ambiente</label>
                                <input list="ambientes" class="form-control" id="ambiente" name="ambiente_nombre" required>
                                <input type="hidden" id="AMBIENTE_id_ambiente" name="AMBIENTE_id_ambiente">
                                <datalist id="ambientes">
                                    @foreach($ambientes as $ambiente)
                                        <option value="{{ $ambiente->nombre }}" data-id="{{ $ambiente->id_ambiente }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <!--script para obtener el id oculto-->
                            <script>
                                document.getElementById('ambiente').addEventListener('input', function() {
                                    var input = this;
                                    var selectedOption = Array.from(document.getElementById('ambientes').options)
                                        .find(option => option.value === input.value);
                            
                                    if (selectedOption) {
                                        document.getElementById('AMBIENTE_id_ambiente').value = selectedOption.getAttribute('data-id');
                                    } else {
                                        document.getElementById('AMBIENTE_id_ambiente').value = '';
                                    }
                                });
                            </script>                            
                        </div>
                    </div>
                    
                    <!-- Previsualización de la foto y el botón de registro -->
                    <div class="mb-3">
                        <label for="FOTO_id_foto" class="form-label">Foto del Equipo</label>
                        <input type="hidden" id="FOTO_id_foto" name="FOTO_id_foto" value="{{ session('foto_id') }}">
                    </div>
                    <div class="mb-3 d-flex justify-content-center">
                        <!-- Imagen que se actualiza según la selección -->
                        <img id="fotoPreview" src="{{ asset(session('ruta_foto') ?? 'img/equipos-img/default.jpg') }}" alt="Vista previa de la foto" style="max-width: 100%; height: auto;">
                    </div>
                    <!-- Script para actualizar la vista previa de la foto -->
                    <script>
                        function mostrarFotoSeleccionada() {
                            var rutaFoto = document.getElementById('FOTO_id_foto').value;
                            document.getElementById('fotoPreview').src = '{{ asset('') }}' + rutaFoto;
                        }
                    </script>

                    <button type="submit" class="btn btn-success">Registrar Equipo</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('foto_id'))
                document.getElementById('fotoForm').style.display = 'none';
                document.getElementById('equipoForm').style.display = 'block';
            @endif
        });
    </script>
</div>

@if(session('aviso'))
    <script>
        //mensaje cuando la foto ya existe
        Swal.fire({
            icon: 'info',
            text: '{{ session('aviso') }}',
            showConfirmButton: false,
            timer: 1500,
        });
    </script>
@endif
@if(session('info'))
    <script>
        //mensaje cuando el equipo ya existe
        Swal.fire({
            icon: 'info',
            title: 'Registro No Valido',
            text: '{{ session('info') }}',
        });
    </script>
@endif

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: '{{ session('success') }}',
        });
    </script>
@endif


@if(session('errordata'))
    <script>
        Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('errordata') }}',
        });
    </script>
@endif

@endsection


