@extends('layouts.encargado')

@section('title', 'Dashboard Operativo')

@section('content')
<div class="container-fluid px-lg-4 py-3">
    <!-- Cabecera de Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 14px; background: linear-gradient(135deg, #ffffff 0%, #F8FAFC 100%); border-left: 5px solid #1B4B82 !important;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h2 class="fw-bold mb-1" style="color: #0E0D39;">
                            Bienvenido, {{ Auth::user()->nombre_completo ?? 'Encargado' }}
                        </h2>
                        <p class="text-muted mb-0">
                            Panel de control operativo para la administración de laboratorios, inventario, préstamos y mantenimientos de la Universidad Latinoamericana.
                        </p>
                    </div>
                    <div class="text-md-end">
                        <span class="badge bg-light text-primary border px-3 py-2 fs-6">
                            <i class="bi bi-calendar3 me-2"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Tarjetas de Metricas Operativas (KPIs) -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Ambientes en Uso -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('movimiento.ambiente.index') }}" class="dashboard-kpi-card kpi-ambientes">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="kpi-label">Ambientes en Uso</p>
                        <h3 class="kpi-number">{{ $ambientesEnUso ?? 0 }}</h3>
                    </div>
                    <div class="kpi-icon-wrapper kpi-icon-ambientes">
                        <i class="bi bi-door-open"></i>
                    </div>
                </div>
                <div class="kpi-link-hint">
                    <span>Ver uso de laboratorios</span>
                    <i class="bi bi-arrow-right"></i>
                </div>
            </a>
        </div>

        <!-- KPI 2: Prestamos Pendientes -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('encargado.prestamo') }}" class="dashboard-kpi-card kpi-prestamos">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="kpi-label">Préstamos Pendientes</p>
                        <h3 class="kpi-number">{{ $prestamosPendientes ?? 0 }}</h3>
                    </div>
                    <div class="kpi-icon-wrapper kpi-icon-prestamos">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                </div>
                <div class="kpi-link-hint">
                    <span>Gestionar préstamos</span>
                    <i class="bi bi-arrow-right"></i>
                </div>
            </a>
        </div>

        <!-- KPI 3: Mantenimientos en Proceso -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('encargado.mantenimiento') }}" class="dashboard-kpi-card kpi-mantenimiento">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="kpi-label">Mantenimientos Activos</p>
                        <h3 class="kpi-number">{{ $mantenimientosEnProceso ?? 0 }}</h3>
                    </div>
                    <div class="kpi-icon-wrapper kpi-icon-mantenimiento">
                        <i class="bi bi-wrench-adjustable"></i>
                    </div>
                </div>
                <div class="kpi-link-hint">
                    <span>Ver mantenimientos</span>
                    <i class="bi bi-arrow-right"></i>
                </div>
            </a>
        </div>

        <!-- KPI 4: Total de Usuarios -->
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('encargado.usuarios') }}" class="dashboard-kpi-card kpi-usuarios">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="kpi-label">Usuarios Registrados</p>
                        <h3 class="kpi-number">{{ $totalUsuarios ?? 0 }}</h3>
                    </div>
                    <div class="kpi-icon-wrapper kpi-icon-usuarios">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="kpi-link-hint">
                    <span>Ver directorio de usuarios</span>
                    <i class="bi bi-arrow-right"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Seccion de Accesos Rapidos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Accesos Rápidos Operativos
                </h4>
            </div>
        </div>
        
        <!-- Accion 1: Registrar Uso de Ambiente -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <a href="{{ route('movimiento.ambiente.index') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <div>
                    <div>Registrar Uso</div>
                    <small class="text-muted fw-normal">Laboratorios y Ambientes</small>
                </div>
            </a>
        </div>

        <!-- Accion 2: Registrar Prestamo -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <a href="{{ route('encargado.prestamo') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <div>
                    <div>Registrar Préstamo</div>
                    <small class="text-muted fw-normal">Entrega de Artículos</small>
                </div>
            </a>
        </div>

        <!-- Accion 3: Registrar Mantenimiento -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <a href="{{ route('encargado.mantenimiento') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <div>Mantenimiento</div>
                    <small class="text-muted fw-normal">Fichas de Reparación</small>
                </div>
            </a>
        </div>

        <!-- Accion 4: Registrar Usuario -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <a href="{{ route('encargado.usuarios') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <div>Registrar Usuario</div>
                    <small class="text-muted fw-normal">Docentes y Alumnos</small>
                </div>
            </a>
        </div>

        <!-- Accion 5: Registrar Equipo -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <a href="{{ route('equipo.create') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-pc-display"></i>
                </div>
                <div>
                    <div>Registrar Equipo</div>
                    <small class="text-muted fw-normal">Cómputo y Dispositivos</small>
                </div>
            </a>
        </div>

        <!-- Accion 6: Registrar Mobiliario -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <a href="{{ route('mobiliario.create') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-inboxes-fill"></i>
                </div>
                <div>
                    <div>Registrar Mobiliario</div>
                    <small class="text-muted fw-normal">Mesas, Sillas, Muebles</small>
                </div>
            </a>
        </div>

        <!-- Accion 7: Registrar Material -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <a href="{{ route('material.create') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div>Registrar Material</div>
                    <small class="text-muted fw-normal">Consumibles e Insumos</small>
                </div>
            </a>
        </div>

        <!-- Accion 8: Registrar Accesorio -->
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <a href="{{ route('accesorio.create') }}" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="bi bi-mouse-fill"></i>
                </div>
                <div>
                    <div>Registrar Accesorio</div>
                    <small class="text-muted fw-normal">Periféricos y Cables</small>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Mostrar mensajes de error -->
    @if($errors->any())
        <script>
            var errorMessages = [];
            @foreach($errors->all() as $error)
                errorMessages.push('{{ $error }}');
            @endforeach
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: '<ul>' + errorMessages.map(msg => `<li>${msg}</li>`).join('') + '</ul>',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif
@endpush
