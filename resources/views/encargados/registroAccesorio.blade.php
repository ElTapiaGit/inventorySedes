@extends('layouts.encargado')

@section('title', 'Gestion Accesorios')

@section('content')
<div class="container-fluid">
    <div class="container mt-2">
        <h2 class="text-center">Registro de Accesorios</h2>

        <!-- Formulario para registrar la foto -->
        <div id="fotoForm" class="card mb-4">
            <div class="card-header">
                <h4>Registrar Foto</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('foto.storeAccesorio') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="foto" class="form-label">Selecciona una foto (.jpg, .jpeg, .png)</label>
                        <input type="file" name="foto" id="foto" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Subir Foto</button>
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

        <!-- Formulario para registrar el accesorio, inicialmente oculto -->
        <div id="accesorioForm" class="card" style="display: none;">
            <div class="card-header">
                <h4>Registrar Accesorio</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('accesorio.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cod_accesorio" class="form-label">Código Accesorio</label>
                                <input type="text" class="form-control" id="cod_accesorio" name="cod_accesorio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_acce" class="form-label">Nombre del Accesorio</label>
                                <input type="text" class="form-control" id="nombre_acce" name="nombre_acce" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="descripcion_acce" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion_acce" name="descripcion_acce" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observacion_ace" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observacion_ace" name="observacion_ace" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="estado_acce" class="form-label">Estado del Accesorio</label>
                                <input type="text" class="form-control" id="estado_acce" name="estado_acce" maxlength="45" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="vida_util" class="form-label">Vida Útil</label>
                                <input type="text" class="form-control" id="vida_util" name="vida_util" maxlength="45" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" oninput="validateUbicacion()" required>
                                <div id="ubicacion-error" class="text-danger" style="display: none;">Solo se registrarán 120 caracteres</div>
                            </div>
                        </div>
                    </div>

                    <!-- Previsualización de la foto y el botón de registro -->
                    <div class="mb-3">
                        <label for="FOTO_id_foto" class="form-label">Foto del Equipo</label>
                        <input type="hidden" id="FOTO_id_foto" name="FOTO_id_foto" value="{{ session('foto_id') }}">
                    </div>
                    <div class="mb-3 d-flex justify-content-center">
                        <!-- Imagen que se actualiza según la selección -->
                        <img id="fotoPreview" src="{{ asset(session('ruta_foto') ?? 'img/accesorios-img/default.jpg') }}" alt="Vista previa de la foto" style="max-width: 100%; height: auto;">
                    </div>
                    <!-- Script para actualizar la vista previa de la foto -->
                    <script>
                        function mostrarFotoSeleccionada() {
                            var rutaFoto = document.getElementById('FOTO_id_foto').value;
                            document.getElementById('fotoPreview').src = '{{ asset('') }}' + rutaFoto;
                        }
                    </script>

                    <button type="submit" class="btn btn-success">Registrar Accesorio</button>
                </form>
            </div>
            <!--script para validar ubicacion-->
            <script>
                function validateUbicacion() {
                    const ubicacionInput = document.getElementById('ubicacion');
                    const errorDiv = document.getElementById('ubicacion-error');
                    
                    // Si el valor es mayor a 50 caracteres, muestra el mensaje de error
                    if (ubicacionInput.value.length > 120) {
                        errorDiv.style.display = 'block';
                    } else {
                        errorDiv.style.display = 'none';
                    }
                }
            </script>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('foto_id'))
                document.getElementById('fotoForm').style.display = 'none';
                document.getElementById('accesorioForm').style.display = 'block';
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

<!-- SweetAlert for Success/Error Messages -->
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

