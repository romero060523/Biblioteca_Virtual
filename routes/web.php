<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return view('welcome');
});

// =====================================================
// RUTAS DE PRUEBA
// =====================================================

Route::prefix('test')->group(function () {
    Route::get('/connection', [TestController::class, 'testConnection']);
    Route::get('/procedures', [TestController::class, 'testProcedures']);
    Route::get('/data', [TestController::class, 'testData']);
});

// =====================================================
// RUTAS PARA BIBLIOTECA VIRTUAL
// =====================================================

Route::prefix('biblioteca')->group(function () {
    // =====================================================
    // RUTAS PARA GESTIÓN DE LIBROS
    // =====================================================
    
    Route::prefix('libros')->group(function () {
        // Obtener todos los libros
        Route::get('/', [BibliotecaController::class, 'indexLibros']);
        
        // Obtener un libro específico
        Route::get('/{id}', [BibliotecaController::class, 'showLibro']);
        
        // Crear un nuevo libro
        Route::post('/', [BibliotecaController::class, 'storeLibro']);
        
        // Actualizar un libro
        Route::put('/{id}', [BibliotecaController::class, 'updateLibro']);
        
        // Eliminar un libro
        Route::delete('/{id}', [BibliotecaController::class, 'destroyLibro']);
        
        // Buscar libros
        Route::get('/buscar/termino', [BibliotecaController::class, 'buscarLibros']);
        
        // Obtener libros disponibles
        Route::get('/disponibles/listado', [BibliotecaController::class, 'librosDisponibles']);
    });
    
    // =====================================================
    // RUTAS PARA GESTIÓN DE PRÉSTAMOS
    // =====================================================
    
    Route::prefix('prestamos')->group(function () {
        // Registrar un nuevo préstamo
        Route::post('/', [BibliotecaController::class, 'registrarPrestamo']);
        
        // Devolver un libro
        Route::put('/{id}/devolver', [BibliotecaController::class, 'devolverLibro']);
        
        // Obtener un préstamo específico
        Route::get('/{id}', [BibliotecaController::class, 'showPrestamo']);
        
        // Obtener préstamos de un usuario
        Route::get('/usuario/{usuarioId}', [BibliotecaController::class, 'prestamosUsuario']);
        
        // Obtener préstamos de un libro
        Route::get('/libro/{libroId}', [BibliotecaController::class, 'prestamosLibro']);
        
        // Obtener préstamos vencidos
        Route::get('/vencidos/listado', [BibliotecaController::class, 'prestamosVencidos']);
        
        // Obtener historial de préstamos
        Route::get('/historial/lista', [BibliotecaController::class, 'historialPrestamos']);
        
        // Obtener libros más prestados
        Route::get('/mas-prestados/lista', [BibliotecaController::class, 'librosMasPrestados']);
        
        // Actualizar estados vencidos
        Route::put('/actualizar-estados/vencidos', [BibliotecaController::class, 'actualizarEstadosVencidos']);
    });
});
