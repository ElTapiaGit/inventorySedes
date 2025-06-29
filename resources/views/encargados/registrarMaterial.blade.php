@extends('layouts.encargado')

@section('title', 'Gestion Material')

@section('content')
<div class="container-fluid">
    <div class="container mt-2">
        <h2 class="text-center">Registro de Materiales</h2>
        
        <!-- Formulario para registrar la foto -->
        <div id="fotoForm" class="card mb-4">
            <div class="card-header">
                <h4>Registrar Foto</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('foto.storeMaterial') }}" method="POST" enctype="multipart/form-data">
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
    
        <!-- Formulario para registrar el material, inicialmente oculto -->
        <div id="materialForm" class="card" style="display: none;">
            <div class="card-header">
                <h4>Registrar Material</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('material.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cod_mate" class="form-label">Código Material</label>
                                <input type="text" class="form-control" id="cod_mate" name="cod_mate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_mate" class="form-label">Tipo de Material</label>
                                <input type="text" class="form-control" id="tipo_mate" name="tipo_mate" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="descripcion_mate" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion_mate" name="descripcion_mate" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observacion_mate" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observacion_mate" name="observacion_mate" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado_mate" class="form-label">Estado del Material</label>
                                <input type="text" class="form-control" id="estado_mate" name="estado_mate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ambiente" class="form-label">Ambiente</label>
                                <input list="ambientes" class="form-control" id="ambiente" name="AMBIENTE_id_ambiente" required>
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
                        <img id="fotoPreview" src="{{ asset(session('ruta_foto') ?? 'img/materiales-img/default.jpg') }}" alt="Vista previa de la foto" style="max-width: 100%; height: auto;">
                    </div>
                    <!-- Script para actualizar la vista previa de la foto -->
                    <script>
                        function mostrarFotoSeleccionada() {
                            var rutaFoto = document.getElementById('FOTO_id_foto').value;
                            document.getElementById('fotoPreview').src = '{{ asset('') }}' + rutaFoto;
                        }
                    </script>

                    <button type="submit" class="btn btn-success">Registrar Material</button>
                </form>
            </div>
        </div>
    
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('foto_id'))
                document.getElementById('fotoForm').style.display = 'none';
                document.getElementById('materialForm').style.display = 'block';
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
        //mensaje cuando el mobiliario ya existe
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

