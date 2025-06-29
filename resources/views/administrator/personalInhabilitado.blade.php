<!-- resources/views/personalInhabilitado.blade.php -->
@extends('layouts.admin')

@section('title', 'Personal-Inhabilitados')

@section('content')
<main class="content p-3 pagAbiliarPers">
    <div class="container-fluid">
        <h2 class="fw-bold">Personal Inhabilitado</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nros.</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Nombre</th>
                        <th>Celular</th>
                        <th>Tipo de Personal</th>
                        <th>Área de Trabajo</th> 
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $contador = 1;
                    @endphp
                    @foreach ($personales as $personal)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $personal->ap_paterno }}</td>
                            <td>{{ $personal->ap_materno }}</td>
                            <td>{{ $personal->nombre }}</td>
                            <td>{{ $personal->celular }}</td>
                            <td>{{ $personal->tipoPersonal->descripcion_per }}</td>
                            <td>{{ $personal->edificio->sede->nombre }} - {{ $personal->edificio->nombre_edi }}</td> <!-- Se une sede y edificio -->
                            <td>
                                <button type="button" class="btn btn-success btn-sm" onclick="confirmReactivar({{ $personal->id_personal }}, '{{ $personal->nombre }} {{ $personal->ap_paterno }}')">
                                    <i class="bi bi-check-circle"></i> Reactivar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

<!--script para saber si se abilita -->
<script>
    function confirmReactivar(id, nombreCompleto) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Estás a punto de reactivar a " + nombreCompleto,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, reactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('/administrator/personal-inhabilitado/reactivar/') }}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        Swal.fire(
                            'Reactivado',
                            'El personal ha sido reactivado exitosamente.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error',
                            'Hubo un problema al reactivar el personal.',
                            'error'
                        );
                    }
                });
            }
        });
    }

    function mostrarInfo(id) {
        // Aquí puedes agregar lógica para mostrar la información del personal.
        alert('Información del personal ID: ' + id);
    }
</script>
@endsection
