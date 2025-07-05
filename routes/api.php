<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliotecaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// =====================================================
// RUTAS API PARA BIBLIOTECA VIRTUAL
// =====================================================

Route::prefix('biblioteca')->group(function () {
    
    // =====================================================
    // API PARA DASHBOARD
    // =====================================================
    
    Route::get('/stats', [BibliotecaController::class, 'getStats']);
    Route::get('/libros-recientes', [BibliotecaController::class, 'getLibrosRecientes']);
    Route::get('/prestamos-recientes', [BibliotecaController::class, 'getPrestamosRecientes']);
    
    // =====================================================
    // API PARA LIBROS
    // =====================================================
    
    Route::prefix('libros')->group(function () {
        Route::get('/', [BibliotecaController::class, 'indexLibros']);
        Route::get('/{id}', [BibliotecaController::class, 'showLibro']);
        Route::post('/', [BibliotecaController::class, 'storeLibro']);
        Route::put('/{id}', [BibliotecaController::class, 'updateLibro']);
        Route::delete('/{id}', [BibliotecaController::class, 'destroyLibro']);
        Route::get('/buscar/termino', [BibliotecaController::class, 'buscarLibros']);
        Route::get('/disponibles/listado', [BibliotecaController::class, 'librosDisponibles']);
        Route::get('/{id}/disponibilidad', [BibliotecaController::class, 'checkDisponibilidad']);
    });
    
    // =====================================================
    // API PARA PRÉSTAMOS
    // =====================================================
    
    Route::prefix('prestamos')->group(function () {
        Route::post('/', [BibliotecaController::class, 'registrarPrestamo']);
        Route::patch('/{id}/devolver', [BibliotecaController::class, 'devolverLibro']);
        Route::get('/{id}', [BibliotecaController::class, 'showPrestamo']);
        Route::get('/usuario/{usuarioId}', [BibliotecaController::class, 'prestamosUsuario']);
        Route::get('/libro/{libroId}', [BibliotecaController::class, 'prestamosLibro']);
        Route::get('/vencidos/listado', [BibliotecaController::class, 'prestamosVencidos']);
        Route::get('/historial/lista', [BibliotecaController::class, 'historialPrestamos']);
        Route::get('/mas-prestados/lista', [BibliotecaController::class, 'librosMasPrestados']);
        Route::put('/actualizar-estados/vencidos', [BibliotecaController::class, 'actualizarEstadosVencidos']);
    });
    
    // =====================================================
    // API PARA USUARIOS
    // =====================================================
    
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [BibliotecaController::class, 'indexUsuarios']);
        Route::get('/{id}', [BibliotecaController::class, 'showUsuario']);
        Route::post('/', [BibliotecaController::class, 'storeUsuario']);
        Route::put('/{id}', [BibliotecaController::class, 'updateUsuario']);
        Route::delete('/{id}', [BibliotecaController::class, 'destroyUsuario']);
        Route::get('/buscar/termino', [BibliotecaController::class, 'buscarUsuarios']);
        Route::get('/{id}/info', [BibliotecaController::class, 'getUsuarioInfo']);
    });
    
    // =====================================================
    // API PARA REPORTES
    // =====================================================
    
    Route::prefix('reportes')->group(function () {
        Route::get('/estadisticas-generales', [BibliotecaController::class, 'getStats']);
        Route::get('/libros-mas-prestados', [BibliotecaController::class, 'librosMasPrestados']);
        Route::get('/historial-prestamos', [BibliotecaController::class, 'historialPrestamos']);
        Route::get('/prestamos-vencidos', [BibliotecaController::class, 'prestamosVencidos']);
        Route::get('/usuarios-activos', [BibliotecaController::class, 'getUsuariosActivos']);
    });
}); 