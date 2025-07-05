<?php

namespace App\Http\Controllers;

use App\Services\OracleService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $oracleService;

    public function __construct(OracleService $oracleService)
    {
        $this->oracleService = $oracleService;
    }

    public function testConnection()
    {
        try {
            $isConnected = $this->oracleService->testConnection();
            $dbInfo = $this->oracleService->getDatabaseInfo();
            
            return response()->json([
                'success' => $isConnected,
                'connection' => $isConnected ? 'Conectado' : 'No conectado',
                'database_info' => $dbInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function testProcedures()
    {
        try {
            // Probar procedimiento de listar libros
            $libros = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.LISTAR_LIBROS',
                [
                    'p_categoria' => null,
                    'p_autor' => null,
                    'p_cursor' => null
                ]
            );

            // Probar procedimiento de libros disponibles
            $disponibles = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBROS_DISPONIBLES',
                ['p_cursor' => null]
            );

            return response()->json([
                'success' => true,
                'message' => 'Procedimientos funcionando correctamente',
                'data' => [
                    'total_libros' => count($libros),
                    'libros_disponibles' => count($disponibles),
                    'muestra_libros' => array_slice($libros, 0, 3)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function testData()
    {
        try {
            // Verificar datos de prueba
            $usuarios = $this->oracleService->executeQuery(
                'SELECT COUNT(*) as total FROM usuarios'
            );

            $libros = $this->oracleService->executeQuery(
                'SELECT COUNT(*) as total FROM libros'
            );

            $prestamos = $this->oracleService->executeQuery(
                'SELECT COUNT(*) as total FROM prestamos'
            );

            return response()->json([
                'success' => true,
                'message' => 'Datos de prueba verificados',
                'data' => [
                    'usuarios' => $usuarios[0]['TOTAL'] ?? 0,
                    'libros' => $libros[0]['TOTAL'] ?? 0,
                    'prestamos' => $prestamos[0]['TOTAL'] ?? 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 