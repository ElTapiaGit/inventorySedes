<?php

use Illuminate\Support\Facades\Route;

/*CONTROLADORES DEL COORDINADOR*/ 
use App\Http\Controllers\Coordinator\CoodinatorController;
use App\Http\Controllers\Coordinator\BuscarLaboController;
use App\Http\Controllers\Coordinator\DetallesLaboratorioController;
use App\Http\Controllers\Coordinator\EquipoAmbienteController;
use App\Http\Controllers\Coordinator\MaterialAmbienteController;
use App\Http\Controllers\Coordinator\MobiliarioAmbienteController;
use App\Http\Controllers\Coordinator\DetallesEquipoController;
use App\Http\Controllers\Coordinator\DetallesMaterialController;
use App\Http\Controllers\Coordinator\DetallesMobiliarioController;
use App\Http\Controllers\Coordinator\AccesorioController;
use App\Http\Controllers\Coordinator\MantenimientoController;
use App\Http\Controllers\Coordinator\HistorialMantenimientoController;
use App\Http\Controllers\Coordinator\MovimientosController;
use App\Http\Controllers\Coordinator\PrestamosController;
use App\Http\Controllers\Coordinator\AmbienteClinicaController;
use App\Http\Controllers\Coordinator\ContenidoAmbienteController;

use App\Http\Controllers\Coordinator\ReporteClinicaController;

/*CONTROLADORES DEL ADMINISTRADOR*/ 
use App\Http\Controllers\Administrador\Inicioontroller;
use App\Http\Controllers\Administrador\SedesController;
use App\Http\Controllers\Administrador\PisosController;
use App\Http\Controllers\Administrador\AmbientesController;
use App\Http\Controllers\Administrador\ContenidAmbienteController;
use App\Http\Controllers\Administrador\ContenidoEquiposController;
use App\Http\Controllers\Administrador\ContenidoMaterialController;
use App\Http\Controllers\Administrador\ContenidoMobiliarioController;
use App\Http\Controllers\Administrador\EquiposDetallesController;
use App\Http\Controllers\Administrador\MaterialDetallesController;
use App\Http\Controllers\Administrador\MobiliarioAdminDetallesController;
use App\Http\Controllers\Administrador\TipoAmbientesController;
use App\Http\Controllers\Administrador\AmbienteController;
use App\Http\Controllers\Administrador\PersonalsController;
use App\Http\Controllers\Administrador\InhabilitadosController;
use App\Http\Controllers\Administrador\UsuariosController;
use App\Http\Controllers\Administrador\MantenimientosAdminController;
use App\Http\Controllers\Administrador\HistorialManteAdminController;
use App\Http\Controllers\Administrador\DetallesManteAdminController;
use App\Http\Controllers\Administrador\MovimientosAdminController;
use App\Http\Controllers\Administrador\DetallesMovAdminController;
use App\Http\Controllers\Administrador\PrestamosAdminController;
use App\Http\Controllers\Administrador\DetallesPrestAdminController;
use App\Http\Controllers\Administrador\ReporteController;

//controlador para reportes
use App\Http\Controllers\Reportes\ReportesController;
use App\Http\Controllers\Reportes\ReporteOdontoController;

use App\Http\Controllers\Administrador\DescartesController;
use App\Http\Controllers\Administrador\AccesoriosController;

//CONTROLADOR PARA LOS ENCARGADOS
use App\Http\Controllers\Encargado\PrincipalController;
use App\Http\Controllers\Encargado\EquipoRController;
use App\Http\Controllers\Encargado\MaterialController;
use App\Http\Controllers\Encargado\MobiliarioController;
use App\Http\Controllers\Encargado\AccesorioRController;
use App\Http\Controllers\Encargado\MovimientoAmbienteController;
use App\Http\Controllers\Encargado\UsuariosEncargController;
use App\Http\Controllers\Encargado\PrestamopController;
use App\Http\Controllers\Encargado\DetallePrestamoController;
use App\Http\Controllers\Encargado\MantenimientopController;

//para el inicio de sesion
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
// Ruta para la página de inicio de sesión
Route::get('/', function () {
    return redirect()->route('login.index');
});

// Ruta para mostrar el formulario de inicio de sesión
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.index');

// Ruta para manejar el envío del formulario de inicio de sesión
Route::post('/login/credencial', [AuthController::class, 'login'])->name('login');
// Ruta para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Rutas para administrador
Route::prefix('administrator')->middleware(['auth', 'role:Administrador'])->group(function () {

    Route::get('/inicio/admin', [Inicioontroller::class, 'inicioadmin'])->name('admin.index');

    //pargina de sedes
    Route::get('/sedes', [SedesController::class, 'index'])->name('sedes.index');
    Route::get('/sedes/{id_sede}', [SedesController::class, 'show'])->name('sedes.show');
    Route::resource('sedes', SedesController::class)->except(['create', 'edit', 'show']);
    Route::post('/sedes', [SedesController::class, 'storeSede'])->name('sedes.store');
    
    Route::post('/edificios/guardar', [SedesController::class, 'storeEdificio'])->name('edificios.store');
    Route::put('/edificios/edit/{id_edificio}', [SedesController::class, 'updateEdificio'])->name('edificios.update');
    Route::delete('/edificios/delete/{id_edificio}', [SedesController::class, 'destroyEdificio'])->name('edificios.destroy');
    Route::post('/pisos/guardar', [SedesController::class, 'storePisos'])->name('pisos.store');

    Route::get('/pisos', [PisosController::class, 'index'])->name('pisos.index');
    Route::put('/pisos/update', [PisosController::class, 'edit'])->name('pisos.update');
    Route::delete('/pisos/{id}', [PisosController::class, 'destroy'])->name('pisos.destroy');
    
    Route::get('/ambientes', [AmbientesController::class, 'index'])->name('ambiente.index');

    Route::get('/ambientes/tipoAmbientes', [TipoAmbientesController::class, 'index'])->name('tipoambiente.index');
    Route::post('/ambientes/tipoambientes/insert', [TipoAmbientesController::class, 'store'])->name('tipoambiente.store');
    Route::put('/ambientes/tipoambientes/update/{id}', [TipoAmbientesController::class, 'update'])->name('tipoambiente.update');
    Route::delete('/ambientes/tipoambientes/{id}', [TipoAmbientesController::class, 'destroy'])->name('tipoambiente.destroy');

    //para el contenido de ambiente
    Route::get('/ambiente/contenido/{id_ambiente}', [ContenidAmbienteController::class, 'show'])->name('ambienteAdmin.contenido');
    Route::get('/ambiente/contenido/equipos/{token}', [ContenidoEquiposController::class, 'index'])->name('contenidoequipos.index');
    Route::get('/ambiente/contenido/material/{token}', [ContenidoMaterialController::class, 'show'])->name('materialesAdmin.show');
    Route::get('/ambiente/contenido/mobiliario/{token}', [ContenidoMobiliarioController::class, 'index'])->name('contenidomobiliario.index');

    //detalles de equipo
    Route::get('/ambiente/contenido/equipos/detalles/{token}', [EquiposDetallesController::class, 'show'])->name('equiposAdmin.detalles');
    // Rutas para la foto del accesorio
    Route::post('/ambiente/contenido/equipos/detalles/foto-accesori', [EquiposDetallesController::class, 'mostrarFotoAccesorio'])->name('foto-accesorio');
    //detalles Material
    Route::get('/ambiente/contenido/material/detalles/{token}', [MaterialDetallesController::class, 'detallesMaterialAdmin'])->name('maeterialAdmin.detalles');
    //detalles Mobiliarios
    Route::get('/ambiente/contenido/mobiliario/detalles/{cod_mueble}', [MobiliarioAdminDetallesController::class, 'detallesMobiliarioAdmin'])->name('mobiliarioAdmin.detalles');


    // Ruta para mostrar la lista de ambientes
    Route::get('/ambiente/vista', [AmbienteController::class, 'index'])->name('ambientes.index');
    // Ruta para almacenar un nuevo ambiente
    Route::post('/ambiente/store', [AmbienteController::class, 'store'])->name('ambientes.store');
    Route::put('/ambiente/update', [AmbienteController::class, 'update'])->name('ambientes.update');
    Route::delete('/ambientes/eliminar/{id}', [AmbienteController::class, 'destroy'])->name('ambientes.destroy');

    Route::get('/personal', [PersonalsController::class, 'index'])->name('personal.index');
    Route::post('/personal', [PersonalsController::class, 'store'])->name('personal.store');
    Route::get('/personal/{id}/edit', [PersonalsController::class, 'edit'])->name('personal.edit');
    Route::put('/personal/{id}', [PersonalsController::class, 'update'])->name('personal.update');
    Route::delete('/personal/{id}', [PersonalsController::class, 'destroy'])->name('personal.destroy');

    // Ruta para asignar acceso
    Route::post('/personal/asignar-acceso', [PersonalsController::class, 'asignarAcceso'])->name('personal.asignarAcceso');
    //para la pagina de personal Inhabilidatos
    Route::get('/personal-inhabilitado', [InhabilitadosController::class, 'index'])->name('inhabilitados.index');
    Route::put('/personal-inhabilitado/reactivar/{id}', [InhabilitadosController::class, 'reactivar'])->name('inhabilitados.reactivar');
    //pagina de usuarios
    Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuario.index');

    //para la pagina de mantenimientos
    Route::get('/mantenimiento', [MantenimientosAdminController::class, 'index'])->name('mantenimientosAdmin.index');
    Route::get('/mantenimiento/Historial', [HistorialManteAdminController::class, 'index'])->name('historialAdmin.index');
    //detalles mantenimiento
    Route::get('/matenimiento/historial/detalles/{id}', [DetallesManteAdminController::class, 'detallesManteAdmin'])->name('detallesManteAdmin.index');

    //pagina movimientos
    Route::get('/movimientos', [MovimientosAdminController::class, 'index'])->name('movimientosAdmin.index');
    Route::post('/movimientos/buscar', [MovimientosAdminController::class, 'buscar'])->name('movimientosAdmin.buscar');
    Route::get('/movimientos/detalles/{id_uso_ambiente}', [DetallesMovAdminController::class, 'detalleMivimientoAdmin'])->name('movimientosAdmin.detalles');

    //pagina prestamos
    Route::get('/prestamos', [PrestamosAdminController::class, 'index'])->name('prestamosAdmin.index');
    // Buscar préstamos
    Route::get('/prestamos/buscar', [PrestamosAdminController::class, 'buscar'])->name('prestamosAdmin.buscar');
    // Ruta para mostrar los detalles del préstamo
    Route::get('/prestamos/detalles/{id_prestamo}', [DetallesPrestAdminController::class, 'show'])->name('prestamosAdmin.detalles');

    //ACCESORIOS
    Route::get('/accesorios', [AccesoriosController::class, 'index'])->name('accesoriosadmin.index');
    Route::get('/accesorios/unicos', [AccesoriosController::class, 'accesoriosUnicos'])->name('accesoriosadmin.unicos');
    Route::get('/accesorios/detalles/{codigo}', [AccesoriosController::class, 'show'])->name('accesoriosadmin.detalles');
    Route::get('/accesorios/equipos', [AccesoriosController::class, 'accesoriosConEquipo'])->name('accesoriosadmin.conEquipo');

    //pagina de REPORTES
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/equipos-adquirido', [ReportesController::class, 'reportesEquiposAdquiridos'])->name('reportes.equipos-adquiridos');
    Route::get('/reportes/ambientes', [ReportesController::class, 'reporteAmbientes'])->name('reportes.ambientes');
    Route::get('/reportes/mobiliario-material', [ReportesController::class, 'reportMobiliarioMaterial'])->name('reportes.mobiliarioMate');
    Route::get('/reportes/accesorios-adquiridos', [ReportesController::class, 'accesoriosadquirido'])->name('reportes.accesorioAdquirido');
    Route::get('/reportes/accesorios-repocisiones', [ReportesController::class, 'reportAccesorios'])->name('reportes.accesoriosMasRepuesto');
    Route::get('/reportes/personal', [ReportesController::class, 'reportePersonal'])->name('reportes.personal');
    Route::get('/reportes/articulos-descartados', [ReportesController::class, 'reportArticulosDescartados'])->name('reportes.articulosDescartados');
    Route::get('/reportes/estado-equipos', [ReportesController::class, 'estadoequipos'])->name('reportes.estado.equipos');
    Route::get('/reportes/mantenimientos', [ReportesController::class, 'reportMantenimiento'])->name('reportes.mantenimeinotos');

    //PARA LA PAGINA DE DESCARTES
    Route::get('/descartes', [DescartesController::class, 'index'])->name('descartes.index');

});

Route::prefix('coordinator')->middleware(['auth', 'role:Coordinador Odontologia'])->group(function () {
    Route::get('/inicio', [CoodinatorController::class, 'inicio'])->name('coordinator.inicio');

    // Ruta para mostrar la página de laboratorios
    Route::get('/laboratorios', [BuscarLaboController::class, 'index'])->name('laboratorios.index');
    // Ruta para manejar la búsqueda de laboratorios
    Route::post('/laboratorios/buscar', [BuscarLaboController::class, 'buscar'])->name('laboratorios.buscar');

    //para ver el inventario del laboratorio
    Route::get('/ambiente/{id}', [DetallesLaboratorioController::class, 'show'])->name('ambiente.show');
    //para la pagina equipos de laboratorio
    Route::get('/ambiente/equipos/{token}', [EquipoAmbienteController::class, 'show'])->name('equipos.show');
    //para la pagina materiales de laboratorio
    Route::get('/ambiente/materiales/{token}', [MaterialAmbienteController::class, 'mostrarMateriales'])->name('materiales.show');
    //para la pagina mobiliario de laboratorio
    Route::get('/ambiente/mobiliario/{token}', [MobiliarioAmbienteController::class, 'show'])->name('mobiliarios.show');

    //para la pagina detalles de un equipo
    Route::get('/equipo/detalles/{token}', [DetallesEquipoController::class, 'index'])->name('equipo.detalles');
    Route::post('/equipo/accesorio/foto', [DetallesEquipoController::class, 'fotoaccesorioEdiCen'])->name('equipo.access.foto');
    //para la pagina detalles de un material
    Route::get('/material/detalles/{token}', [DetallesMaterialController::class, 'show'])->name('material.detalles');
    //para lapagina detalles de un muble
    Route::get('/mobiliario/detalles/{token}', [DetallesMobiliarioController::class, 'show'])->name('mobiliario.detalles');

    //para la pagina de accesorios
    Route::get('/accesorios', [AccesorioController::class, 'index'])->name('accesorios.index');
    Route::get('/accesorios/equipo', [AccesorioController::class, 'accesoriosConEquipo'])->name('accesorios.equipo');
    Route::get('/accesorios/unicos', [AccesorioController::class, 'accesoriosUnicos'])->name('accesorios.unicos');
    Route::get('/accesorios/buscarCodigo', [AccesorioController::class, 'buscarPorCodigo'])->name('accesorios.buscarCodigo');
    Route::get('/accesorios/buscarNombre', [AccesorioController::class, 'buscarPorNombre'])->name('accesorios.buscarNombre');
    Route::get('/accesorios/detalles/{cod_accesorio}', [AccesorioController::class, 'show'])->name('accesorios.show');

    //para la pagina de mantenimientos
    Route::get('/mantenimiento', [MantenimientoController::class, 'index'])->name('mantenimiento.index');
    //para la pagina de historial de mantenimientos
    Route::get('/historial-mantenimiento', [HistorialMantenimientoController::class, 'show'])->name('historialmantenimientos.index');
    Route::get('/historial-mantenimiento/{id}', [HistorialMantenimientoController::class, 'mostrar'])->name('detallesmantenimientos.mostrar');

    //para la pagina de movimientos o prestamos
    Route::get('/movimientos', [MovimientosController::class, 'index'])->name('movimientos.index');
    Route::post('/movimientos/buscar', [MovimientosController::class, 'buscar'])->name('movimientos.buscar');
    Route::get('/movimientos/detalles/{id}', [MovimientosController::class, 'detalles'])->name('movimientos.detalles');

    //para la paginas prestamos
    Route::get('/prestamos', [PrestamosController::class, 'index'])->name('prestamos.index');
    Route::get('/prestamos/buscar', [PrestamosController::class, 'index'])->name('prestamos.buscar');
    Route::get('/prestamos/detalles/{token}', [PrestamosController::class, 'detalles'])->name('prestamos.detalles');

    //pagina de reportes edificio principal
    Route::get('/reportes-edificio-prinicipal', [ReporteClinicaController::class, 'index'])->name('reporte.index');
    
    Route::get('/reportes/equipos-odontologia', [ReporteOdontoController::class, 'reportEquiposOdont'])->name('reporte.equiposOdont');
    Route::get('/reporte/ambientes-odontologia', [ReporteOdontoController::class, 'reportAmbienteOdontologia'])->name('reporte.ambiente.odontologia');
    Route::get('/reportes/equipos-descartados', [ReporteOdontoController::class, 'reportEquipoDescartado'])->name('reporte.equiposDescartadOdont');
    Route::get('/reportes/accesorio-cambiados', [ReporteOdontoController::class, 'reportAccesorioCambio'])->name('reporte.accesorioCambiadOdont');
    Route::get('/reportes/estado/equipos', [ReporteOdontoController::class, 'reportEstadoEquipos'])->name('report.estado.equipos');

    ////////////PAGINAS DE LA CLINICA ODONTOLOGIA/////////////////////////////////////////////////////////////////////////////////////////

    Route::get('/clinica', [CoodinatorController::class, 'home'])->name('coordinator.clinica.inicio');
    //para la pagina niveles
    Route::get('/busquedas', [BuscarLaboController::class, 'buscarRapido'])->name('nivel.index');

    Route::post('/busquedas/buscar-equipo', [BuscarLaboController::class, 'buscarEquipo'])->name('niveles.buscarEquipo');
    Route::post('/busquedas/buscar-mobiliario', [BuscarLaboController::class, 'buscarMobiliario'])->name('niveles.buscarMobiliario');
    Route::post('/busquedas/buscar-material', [BuscarLaboController::class, 'buscarMaterial'])->name('niveles.buscarMaterial');
    Route::post('/busquedas/buscar-accesorios', [BuscarLaboController::class, 'buscarAccesorio'])->name('niveles.buscarAccesorio');

    //para ver la pagina del piso
    Route::get('/niveles/ambiente/{id}', [AmbienteClinicaController::class, 'show'])->name('niveles.ambiente.show');
    
    //PARA LA PAGINAS DE AMBIENTES
    Route::get('/clinica/ambientes', [ContenidoAmbienteController::class, 'index'])->name('coordinator.clinica.ambientes');

    Route::get('/clinica/ambiente/contenido/{id_ambiente}', [ContenidoAmbienteController::class, 'show'])->name('coordinator.clinica.contenido');
    //paginas para los botonde de equipos, materiales y mobiliarios de la clinica
    Route::get('/clinica/ambiente/equipos/{id_ambiente}', [ContenidoAmbienteController::class, 'showEquipos'])->name('coordinator.clinica.equipos');

    Route::get('/clinica/ambiente/materiales/{id_ambiente}', [ContenidoAmbienteController::class, 'showMateriales'])->name('coordinator.clinica.materiales');
    Route::get('/clinica/ambiente/mobiliarios/{id_ambiente}', [ContenidoAmbienteController::class, 'showMobiliarios'])->name('coordinator.clinica.mobiliarios');

    //para la pagina detalles de un equipo
    Route::get('/clinica/ambiente/equipo/detalles/{token}', [DetallesEquipoController::class, 'detalleEquipo'])->name('clinica.equipo.detalles');
    Route::post('/clinica/ambiente/equipos/foto-Accesorio', [DetallesEquipoController::class, 'fotoaccesorio'])->name('clinica.equiposAccefoto');
    Route::get('/clinica/ambiente/material/detalles/{token}', [DetallesMaterialController::class, 'detalleMaterial'])->name('clinica.material.detalles');
    Route::get('/clinica/ambiente/mobiliario/detalles/{token}', [DetallesMobiliarioController::class, 'detalleMobiliario'])->name('clinica.mobiliario.detalles');

    //PARA LAS PAGINAS DEL ACCESORIO DE L ACLINICA ODONTOLOGICA
    //para la pagina de accesorios
    Route::get('/clinica/accesorios', [AccesorioController::class, 'obtenerAccesoriosClinica'])->name('clinica.accesorios.index');
    Route::get('/clinica/accesorios/equipo', [AccesorioController::class, 'accesoriosEquipo'])->name('clinica.accesorios.equipo');
    Route::get('/clinica/accesorios/unicos', [AccesorioController::class, 'accesoriosUnicosClin'])->name('clinica.accesorios.unicos');
    Route::get('/clinica/accesorios/buscarCodigo', [AccesorioController::class, 'buscarCodigoCli'])->name('clinica.accesorios.buscarCodigo');
    Route::get('/clinica/accesorios/buscarNombre', [AccesorioController::class, 'buscarNombreCli'])->name('clinica.accesorios.buscarNombre');
    Route::get('/clinica/accesorios/{cod_accesorio}', [AccesorioController::class, 'mostrar'])->name('clinica.accesorios.show');

    //para la pagina de mantenimientos de la clinica
    Route::get('/clinica/mantenimientos', [MantenimientoController::class, 'obtenerMantenimientoClinica'])->name('clinica.mantenimiento.index');
    //para la pagina de historial de mantenimientos
    Route::get('/clinica/historial-mantenimiento', [HistorialMantenimientoController::class, 'historial'])->name('clinica.historialmantenimientos.index');
    Route::get('/clinica/historial-mantenimiento/{id}', [HistorialMantenimientoController::class, 'detallesHistorial'])->name('clinica.detallesmantenimientos.mostrar');

    //para la pagina de movimientos o prestamos
    Route::get('/clinica/movimientos', [MovimientosController::class, 'indexx'])->name('clinica.movimientos.index');
    Route::post('/clinica/movimientos/buscar', [MovimientosController::class, 'buscarUserData'])->name('clinica.movimientos.buscar');
    Route::get('/clinica/movimientos/detalles/{id}', [MovimientosController::class, 'detallesClinica'])->name('clinica.movimientos.detalles');

    //para la paginas prestamos de la clinica
    Route::get('/clinica/prestamos', [PrestamosController::class, 'ObtenerPrestamosClinica'])->name('clinica.prestamos.index');
    Route::get('/clinica/prestamos/buscar', [PrestamosController::class, 'ObtenerPrestamosClinica'])->name('clinica.prestamos.buscar');
    Route::get('/clinica/prestamos/detalles/{token}', [PrestamosController::class, 'detallesPrestamos'])->name('clinica.prestamos.detalles');

    //pagina de reportes edificio principal
    Route::get('/reportes-clinica-Odontologia', [ReporteClinicaController::class, 'indexClinica'])->name('reporte.clinica.index');
    Route::get('/reportes/equipos/clinica-Odontologia', [ReporteOdontoController::class, 'reportEquiposClinica'])->name('reporte.clinica.equipos');
    Route::get('/reportes/contenido/ambientes-Odontologia', [ReporteOdontoController::class, 'reportMobiliarioMaterialClinica'])->name('reporte.clinica.contenido');
    Route::get('/reportes/clinica/estado/equipos', [ReporteOdontoController::class, 'reportEstadoEquiClinica'])->name('report.estado.equiposClinica');

    //para los reportes
    Route::get('/print-equipos', [MantenimientoController::class, 'report'])->name('print.equipos');
    
});


Route::prefix('encargado')->middleware(['auth', 'role:Encargado'])->group(function () {


    Route::get('/principal', [PrincipalController::class, 'principal'])->name('encargado.inicio');

    Route::get('/registro/equipo', [EquipoRController::class, 'create'])->name('equipo.create');
    Route::post('/foto/store', [EquipoRController::class, 'store'])->name('foto.store');
    Route::post('/equipo/store', [EquipoRController::class, 'storeEquipo'])->name('equipo.store');
    //registrar componentes y accesorios
    Route::post('/componente/store', [EquipoRController::class, 'storeComponente'])->name('componente.store');
    Route::post('/registro/foto/accesorio', [EquipoRController::class, 'storeFotoAccesorio'])->name('foto.accesorio');
    Route::post('/accesorio/equipo/store', [EquipoRController::class, 'storeAccesorioEquipo'])->name('equipo.accesorios.store');
    Route::get('/equipo/{cod_equipo}', [EquipoRController::class, 'show'])->name('equipo.show');

    //registrar material
    // Rutas para materiales
    Route::get('/material/crear', [MaterialController::class, 'create'])->name('material.create');
    Route::post('/material/guardar-foto', [MaterialController::class, 'storeFoto'])->name('foto.storeMaterial');
    Route::post('/material/guardar', [MaterialController::class, 'storeMaterial'])->name('material.store');

    //registro de mobiliario
    Route::get('/mobiliario/create', [MobiliarioController::class, 'create'])->name('mobiliario.create');
    Route::post('/tipo_mobiliario/store', [MobiliarioController::class, 'storeTipoMobiliario'])->name('tipo_mobiliario.store');
    Route::post('/foto/storeMobiliario', [MobiliarioController::class, 'storeFoto'])->name('foto.storeMobiliario');
    Route::post('/mobiliario/store', [MobiliarioController::class, 'storeMobiliario'])->name('mobiliario.store');

    //registro de accesorios
    Route::get('/accesorio/create', [AccesorioRController::class, 'create'])->name('accesorio.create');
    Route::post('/accesorio/storeFoto', [AccesorioRController::class, 'storeFoto'])->name('foto.storeAccesorio');
    Route::post('/accesorio/store', [AccesorioRController::class, 'storeAccesorio'])->name('accesorio.store');

    //pagina de uso de ambientes
    Route::get('/ambientes', [MovimientoAmbienteController::class, 'index'])->name('movimiento.ambiente.index');
    Route::post('/usos/registrar', [MovimientoAmbienteController::class, 'registrarUso'])->name('usos.store');
    Route::get('/movimientos/detalles/{id_uso_ambiente}', [MovimientoAmbienteController::class, 'detalleMov'])->name('uso.detalles');
    Route::post('/ambientes/finalizar-uso', [MovimientoAmbienteController::class, 'finalizarUso'])->name('uso.finalizarUso');

    Route::post('/usuarios/registrar', [MovimientoAmbienteController::class, 'registrarUser'])->name('uso.user.registrar');
    //pagina registrar usuarios y tipo de usuarios
    Route::get('/usuarios', [UsuariosEncargController::class, 'usuarios'])->name('encargado.usuarios');
    Route::post('/usuarios/register', [UsuariosEncargController::class, 'registrarUsuario'])->name('encargado.register.usuarios');
    Route::post('/tipousuarios/register', [UsuariosEncargController::class, 'registrarTipoUsuario'])->name('encargado.register.tipousuarios');
    // Actualizar un usuario
    Route::put('/usuarios/{id}', [UsuariosEncargController::class, 'actualizarUsuario'])->name('encargado.actualizarUsuario');

    // Eliminar un usuario
    Route::delete('/usuarios/{id}', [UsuariosEncargController::class, 'eliminarUsuario'])->name('encargado.eliminarUsuario');

    // Préstamos
    Route::get('/prestamos', [PrestamopController::class, 'index'])->name('encargado.prestamo');
    // Registro de nuevo préstamo
    Route::post('/prestamos', [PrestamopController::class, 'store'])->name('encargado.prestamo.store');
    //pagina detalles
    Route::get('/prestamos/{id}', [PrestamopController::class, 'show'])->name('encargado.prestamo.show');
    //finalizar prestamo
    Route::post('/prestamos/devolucion/{id}', [PrestamopController::class, 'registrarDevolucion'])->name('encargado.prestamo.devolucion');

    // Mantenimiento
    Route::get('/mantenimientos', [MantenimientopController::class, 'index'])->name('encargado.mantenimiento');
    Route::post('/mantenimientos', [MantenimientopController::class, 'store'])->name('encargado.mantenimiento.store');
    Route::get('/mantenimientos/{id}', [MantenimientopController::class, 'show'])->name('encargado.mantenimiento.show');
    Route::post('/mantenimientos/finalizar/{id}', [MantenimientopController::class, 'finalizar'])->name('encargado.mantenimiento.finalizar');

});

