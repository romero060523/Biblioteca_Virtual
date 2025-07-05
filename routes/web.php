<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return redirect()->route('biblioteca.dashboard');
});


// RUTAS DE PRUEBA
Route::prefix('test')->group(function () {
    Route::get('/connection', [TestController::class, 'testConnection']);
    Route::get('/procedures', [TestController::class, 'testProcedures']);
    Route::get('/data', [TestController::class, 'testData']);
});


// RUTAS PARA BIBLIOTECA VIRTUAL
Route::prefix('biblioteca')->name('biblioteca.')->group(function () {
    
    // DASHBOARD PRINCIPAL
    Route::get('/', [BibliotecaController::class, 'dashboard'])->name('dashboard');
    

    // RUTAS PARA GESTIÓN DE LIBROS
    Route::prefix('libros')->name('libros.')->group(function () {
        // Lista de libros
        Route::get('/', [BibliotecaController::class, 'indexLibrosView'])->name('index');
        
        // Crear libro (formulario)
        Route::get('/create', [BibliotecaController::class, 'createLibro'])->name('create');
        
        // Guardar libro
        Route::post('/', [BibliotecaController::class, 'storeLibroView'])->name('store');
        
        // Ver libro específico
        Route::get('/{id}', [BibliotecaController::class, 'showLibroView'])->name('show');
        
        // Editar libro (formulario)
        Route::get('/{id}/edit', [BibliotecaController::class, 'editLibro'])->name('edit');
        
        // Actualizar libro
        Route::put('/{id}', [BibliotecaController::class, 'updateLibroView'])->name('update');
        
        // Eliminar libro
        Route::delete('/{id}', [BibliotecaController::class, 'destroyLibroView'])->name('destroy');
        
        // Buscar libros (AJAX)
        Route::get('/buscar/termino', [BibliotecaController::class, 'buscarLibros'])->name('buscar');
        
        // Obtener libros disponibles
        Route::get('/disponibles', [BibliotecaController::class, 'librosDisponiblesView'])->name('disponibles');
    });
    

    // RUTAS PARA GESTIÓN DE PRÉSTAMOS
    Route::prefix('prestamos')->name('prestamos.')->group(function () {
        // Lista de préstamos
        Route::get('/', [BibliotecaController::class, 'indexPrestamos'])->name('index');
        
        // Crear préstamo (formulario)
        Route::get('/create', [BibliotecaController::class, 'createPrestamo'])->name('create');
        
        // Guardar préstamo
        Route::post('/', [BibliotecaController::class, 'storePrestamo'])->name('store');
        
        // Ver préstamo específico
        Route::get('/{id}', [BibliotecaController::class, 'showPrestamoView'])->name('show');
        
        // Editar préstamo (formulario)
        Route::get('/{id}/edit', [BibliotecaController::class, 'editPrestamo'])->name('edit');
        
        // Actualizar préstamo
        Route::put('/{id}', [BibliotecaController::class, 'updatePrestamo'])->name('update');
        
        // Eliminar préstamo
        Route::delete('/{id}', [BibliotecaController::class, 'destroyPrestamo'])->name('destroy');
        
        // Devolver libro
        Route::patch('/{id}/devolver', [BibliotecaController::class, 'devolverLibro'])->name('devolver');
        
        // Obtener préstamos de un usuario
        Route::get('/usuario/{usuarioId}', [BibliotecaController::class, 'prestamosUsuario'])->name('usuario');
        
        // Obtener préstamos de un libro
        Route::get('/libro/{libroId}', [BibliotecaController::class, 'prestamosLibro'])->name('libro');
        
        // Obtener préstamos vencidos
        Route::get('/vencidos', [BibliotecaController::class, 'prestamosVencidosView'])->name('vencidos');
        
        // Obtener historial de préstamos
        Route::get('/historial', [BibliotecaController::class, 'historialPrestamosView'])->name('historial');
        
        // Obtener libros más prestados
        Route::get('/mas-prestados', [BibliotecaController::class, 'librosMasPrestados'])->name('mas-prestados');
        
        // Actualizar estados vencidos
        Route::put('/actualizar-estados/vencidos', [BibliotecaController::class, 'actualizarEstadosVencidos'])->name('actualizar-estados');
    });
    

    // RUTAS PARA GESTIÓN DE USUARIOS
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        // Lista de usuarios
        Route::get('/', [BibliotecaController::class, 'indexUsuarios'])->name('index');
        
        // Crear usuario (formulario)
        Route::get('/create', [BibliotecaController::class, 'createUsuario'])->name('create');
        
        // Guardar usuario
        Route::post('/', [BibliotecaController::class, 'storeUsuario'])->name('store');
        
        // Ver usuario específico
        Route::get('/{id}', [BibliotecaController::class, 'showUsuarioView'])->name('show');
        
        // Editar usuario (formulario)
        Route::get('/{id}/edit', [BibliotecaController::class, 'editUsuario'])->name('edit');
        
        // Actualizar usuario
        Route::put('/{id}', [BibliotecaController::class, 'updateUsuario'])->name('update');
        
        // Eliminar usuario
        Route::delete('/{id}', [BibliotecaController::class, 'destroyUsuario'])->name('destroy');
        
        // Buscar usuarios (AJAX)
        Route::get('/buscar/termino', [BibliotecaController::class, 'buscarUsuarios'])->name('buscar');
    });
    

    // RUTAS PARA REPORTES
    Route::prefix('reportes')->name('reportes.')->group(function () {
        // Reporte de libros más prestados
        Route::get('/libros-mas-prestados', [BibliotecaController::class, 'reportesLibrosMasPrestados'])->name('libros-mas-prestados');
        
        // Reporte de historial de préstamos
        Route::get('/historial', [BibliotecaController::class, 'reportesHistorial'])->name('historial');
        
        // Reporte de estadísticas generales
        Route::get('/estadisticas', [BibliotecaController::class, 'reportesEstadisticas'])->name('estadisticas');
        
        // Reporte de préstamos vencidos
        Route::get('/prestamos-vencidos', [BibliotecaController::class, 'reportesPrestamosVencidos'])->name('prestamos-vencidos');
        
        // Reporte de usuarios más activos
        Route::get('/usuarios-activos', [BibliotecaController::class, 'reportesUsuariosActivos'])->name('usuarios-activos');
    });
    
 
    // RUTAS PARA BÚSQUEDA AVANZADA
    Route::prefix('buscar')->name('buscar.')->group(function () {
        // Búsqueda general
        Route::get('/', [BibliotecaController::class, 'busquedaGeneral'])->name('general');
        
        // Búsqueda de libros
        Route::get('/libros', [BibliotecaController::class, 'busquedaLibros'])->name('libros');
        
        // Búsqueda de usuarios
        Route::get('/usuarios', [BibliotecaController::class, 'busquedaUsuarios'])->name('usuarios');
        
        // Búsqueda de préstamos
        Route::get('/prestamos', [BibliotecaController::class, 'busquedaPrestamos'])->name('prestamos');
    });
    

    // RUTAS PARA API (AJAX)
    Route::prefix('api')->name('api.')->group(function () {
        // Estadísticas del dashboard
        Route::get('/stats', [BibliotecaController::class, 'getStats'])->name('stats');
        
        // Libros recientes
        Route::get('/libros-recientes', [BibliotecaController::class, 'getLibrosRecientes'])->name('libros-recientes');
        
        // Préstamos recientes
        Route::get('/prestamos-recientes', [BibliotecaController::class, 'getPrestamosRecientes'])->name('prestamos-recientes');
        
        // Verificar disponibilidad de libro
        Route::get('/libro/{id}/disponibilidad', [BibliotecaController::class, 'checkDisponibilidad'])->name('disponibilidad');
        
        // Obtener información de usuario
        Route::get('/usuario/{id}/info', [BibliotecaController::class, 'getUsuarioInfo'])->name('usuario-info');
    });
});


// RUTAS DE FALLBACK
Route::fallback(function () {
    return redirect()->route('biblioteca.dashboard');
});
