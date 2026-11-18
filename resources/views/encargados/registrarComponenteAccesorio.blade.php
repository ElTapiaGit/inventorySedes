@extends('layouts.encargado')

@section('title', 'Registrar Componentes y Accesorios')

@section('content')
<div class="container-fluid">
    <h2 class="text-center">Registrar Componentes y Accesorios para el Equipo {{ $equipo->nombre_equi }}</h2>

    <!-- Formulario para registrar componentes -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Registrar Componente</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('componente.store') }}" method="POST">
                @csrf
                <input type="hidden" name="cod_equipo" value="{{ $equipo->cod_equipo }}">
                <div class="mb-3">
                    <label for="nombre_compo" class="form-label">Nombre del Componente</label>
                    <input type="text" class="form-control" id="nombre_compo" name="nombre_compo" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion_compo" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion_compo" name="descripcion_compo" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="estado_compo" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado_compo" name="estado_compo" required>
                </div>
                <button type="submit" class="btn btn-success">Registrar Componente</button>
            </form>
        </div>
    </div>

    <!-- Botones para mostrar formularios -->
    <div class="text-center mb-4">
        <button id="showPhotoForm" class="btn btn-primary">Registrar Foto de Accesorio</button>
        <button id="showAccessoryForm" class="btn btn-primary">Registrar Accesorio</button>

        <a href="{{ route('encargado.inicio') }}" class="btn btn-danger">Finalizar Registro</a>
    </div>

    <!-- Formulario para registrar la foto del accesorio -->
    <div id="photoForm" class="card mb-4 d-none">
        <div class="card-header">
            <h4>Registrar Foto del Accesorio</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('foto.accesorio') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="foto" class="form-label">Selecciona una foto</label>
                    <input type="file" name="foto" id="foto" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Subir Foto</button>
            </form>
        </div>
        <div class="col-md-10">
            <div id="previewContainer" class="d-none px-5">
                <h5>Previsualización:</h5>
                <img id="photoPreview" src="" alt="Previsualización de la Foto" class="img-fluid border">
            </div>
        </div>
    </div>

    <!-- Formulario para registrar un nuevo accesorio -->
    <div id="accessoryForm" class="card mb-4 d-none">
        <div class="card-header">
            <h4>Registrar Nuevo Accesorio</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('equipo.accesorios.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="cod_accesorio" class="form-label">Código del Accesorio</label>
                            <input type="text" class="form-control" id="cod_accesorio" name="cod_accesorio" oninput="actualizarRutaImagen()" required>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                            <textarea class="form-control" id="descripcion_acce" name="descripcion_acce"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="observacion_ace" class="form-label">Observación</label>
                            <textarea class="form-control" id="observacion_ace" name="observacion_ace"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="estado_acce" class="form-label">Estado</label>
                            <input type="text" class="form-control" id="estado_acce" name="estado_acce" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="vida_util" class="form-label">Vida Útil</label>
                            <input type="text" class="form-control" id="vida_util" name="vida_util" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="ubicacion" name="ubicacion" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="FOTO_id_foto" class="form-label">Ruta de la Foto</label>
                    <select class="form-select d-none" id="FOTO_id_foto" name="FOTO_id_foto" onchange="mostrarFotoSeleccionada()" required>
                        @foreach($fotos as $foto)
                            <option value="{{ $foto->id_foto }}" data-ruta="{{ $foto->ruta_foto }}">{{ $foto->ruta_foto }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Imagen para la previsualización de la foto -->
                <div class="mb-3">
                    <img id="fotoPreview" src="" alt="Vista previa de la foto" style="max-width: 50%; height: auto;">
                </div>
                <input type="hidden" name="cod_equipo" value="{{ $equipo->cod_equipo }}">
                <button type="submit" class="btn btn-primary">Registrar Accesorio</button>
            </form>
        </div>
    </div>	
</div>
@endsection

@push('scripts')
    <!-- Script para tomar el codigo y ponerlo a la ruta de la imagen -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function actualizarRutaImagen() {
                var codigo = document.getElementById('cod_accesorio').value;

                if (codigo) {
                    var rutaCompleta = "{{ asset('img/accesorios-img') }}/" + codigo + ".jpg";
                    document.getElementById('fotoPreview').src = rutaCompleta;

                    var selectFoto = document.getElementById('FOTO_id_foto');
                    for (var i = 0; i < selectFoto.options.length; i++) {
                        if (selectFoto.options[i].dataset.ruta === 'img/accesorios-img/' + codigo + '.jpg') {
                            selectFoto.selectedIndex = i;
                            break;
                        } else {
                            selectFoto.selectedIndex = -1;
                        }
                    }

                    var idFotoText = document.getElementById('idFotoText');
                    if (selectFoto.selectedIndex >= 0) {
                        idFotoText.textContent = "ID de la Foto: " + selectFoto.value;
                    } else {
                        idFotoText.textContent = "ID de la Foto: No seleccionada";
                    }
                } else {
                    document.getElementById('fotoPreview').src = "";
                    document.getElementById('FOTO_id_foto').selectedIndex = -1;
                }
            }

            function mostrarFotoSeleccionada() {
                var selectFoto = document.getElementById('FOTO_id_foto');
                var rutaFoto = selectFoto.options[selectFoto.selectedIndex].dataset.ruta;

                var rutaCompleta = "{{ asset('') }}" + rutaFoto;
                document.getElementById('fotoPreview').src = rutaCompleta;

                document.getElementById('idFotoText').textContent = "ID de la Foto: " + selectFoto.value;
            }

            const codAcc = document.getElementById('cod_accesorio');
            const fotoId = document.getElementById('FOTO_id_foto');
            if (codAcc) codAcc.addEventListener('input', actualizarRutaImagen);
            if (fotoId) fotoId.addEventListener('change', mostrarFotoSeleccionada);
        });

        const showPhotoBtn = document.getElementById('showPhotoForm');
        if (showPhotoBtn) {
            showPhotoBtn.addEventListener('click', function() {
                document.getElementById('photoForm').classList.remove('d-none');
                document.getElementById('accessoryForm').classList.add('d-none');
            });
        }

        const showAccBtn = document.getElementById('showAccessoryForm');
        if (showAccBtn) {
            showAccBtn.addEventListener('click', function() {
                document.getElementById('photoForm').classList.add('d-none');
                document.getElementById('accessoryForm').classList.remove('d-none');
            });
        }

        const fotoInput = document.getElementById('foto');
        if (fotoInput) {
            fotoInput.addEventListener('change', function(event) {
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
        }

        const formFoto = document.getElementById('formFoto');
        if (formFoto) {
            formFoto.addEventListener('submit', function(event) {
                event.preventDefault();
                
                let formData = new FormData(this);
                
                fetch('{{ route('foto.accesorio') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('photoForm').classList.add('d-none');
                        document.getElementById('accessoryForm').classList.remove('d-none');
                    } else {
                        console.error('Error:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
    </script>

    @if(session('aviso'))
        <script>
            Swal.fire({
                icon: 'info',
                text: '{{ session('aviso') }}',
                showConfirmButton: false,
                timer: 1500,
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
@endpush
