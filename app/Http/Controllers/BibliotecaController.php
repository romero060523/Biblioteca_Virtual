<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Services\OracleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BibliotecaController extends Controller
{
    protected $oracleService;
    
    public function __construct(OracleService $oracleService)
    {
        $this->oracleService = $oracleService;
    }
    
    // =====================================================
    // GESTIÓN DE LIBROS
    // =====================================================
    
    /**
     * Obtener todos los libros
     */
    public function indexLibros(Request $request): JsonResponse
    {
        try {
            $categoria = $request->get('categoria');
            $autor = $request->get('autor');

            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.LISTAR_LIBROS',
                [
                    'p_categoria' => $categoria,
                    'p_autor' => $autor
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Libros obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener libros: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener un libro específico
     */
    public function showLibro($id): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBRO',
                [
                    'p_libro_id' => $id
                ]
            );

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Libro no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result[0],
                'message' => 'Libro obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener libro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Crear un nuevo libro
     */
    public function storeLibro(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'autor' => 'nullable|string|max:100',
            'categoria' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->oracleService->executeProcedure(
                'PKG_LIBROS.INSERTAR_LIBRO',
                [
                    'p_titulo' => $request->titulo,
                    'p_autor' => $request->autor,
                    'p_categoria' => $request->categoria,
                    'p_stock' => $request->stock,
                    'p_libro_id' => null
                ]
            );

            return response()->json([
                'success' => true,
                'data' => ['libro_id' => $result['p_libro_id']],
                'message' => 'Libro creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear libro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar un libro
     */
    public function updateLibro(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'autor' => 'nullable|string|max:100',
            'categoria' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->oracleService->executeProcedure(
                'PKG_LIBROS.ACTUALIZAR_LIBRO',
                [
                    'p_libro_id' => $id,
                    'p_titulo' => $request->titulo,
                    'p_autor' => $request->autor,
                    'p_categoria' => $request->categoria,
                    'p_stock' => $request->stock
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Libro actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar libro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Eliminar un libro
     */
    public function destroyLibro($id): JsonResponse
    {
        try {
            $this->oracleService->executeProcedure(
                'PKG_LIBROS.ELIMINAR_LIBRO',
                ['p_libro_id' => $id]
            );

            return response()->json([
                'success' => true,
                'message' => 'Libro eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar libro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Buscar libros
     */
    public function buscarLibros(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'termino' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Término de búsqueda requerido (mínimo 2 caracteres)',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.BUSCAR_LIBROS',
                [
                    'p_termino' => $request->termino
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Búsqueda completada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener libros disponibles
     */
    public function librosDisponibles(): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBROS_DISPONIBLES',
                []
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Libros disponibles obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener libros disponibles: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // =====================================================
    // GESTIÓN DE PRÉSTAMOS
    // =====================================================
    
    /**
     * Registrar un nuevo préstamo
     */
    public function registrarPrestamo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'usuario_id' => 'required|integer|exists:usuarios,id',
            'libro_id' => 'required|integer|exists:libros,id',
            'dias_prestamo' => 'integer|min:1|max:30'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->oracleService->executeProcedure(
                'PKG_PRESTAMOS.REGISTRAR_PRESTAMO',
                [
                    'p_usuario_id' => $request->usuario_id,
                    'p_libro_id' => $request->libro_id,
                    'p_dias_prestamo' => $request->get('dias_prestamo', 15),
                    'p_prestamo_id' => null
                ]
            );

            return response()->json([
                'success' => true,
                'data' => ['prestamo_id' => $result['p_prestamo_id']],
                'message' => 'Préstamo registrado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar préstamo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Devolver un libro
     */
    public function devolverLibro($id): JsonResponse
    {
        try {
            $this->oracleService->executeProcedure(
                'PKG_PRESTAMOS.DEVOLVER_LIBRO',
                ['p_prestamo_id' => $id]
            );

            return response()->json([
                'success' => true,
                'message' => 'Libro devuelto exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al devolver libro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener un préstamo específico
     */
    public function showPrestamo($id): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMO',
                [
                    'p_prestamo_id' => $id
                ]
            );

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Préstamo no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result[0],
                'message' => 'Préstamo obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener préstamo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener préstamos de un usuario
     */
    public function prestamosUsuario(Request $request, $usuarioId): JsonResponse
    {
        try {
            $estado = $request->get('estado');

            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.LISTAR_PRESTAMOS_USUARIO',
                [
                    'p_usuario_id' => $usuarioId,
                    'p_estado' => $estado
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Préstamos del usuario obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener préstamos del usuario: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener préstamos de un libro
     */
    public function prestamosLibro($libroId): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.LISTAR_PRESTAMOS_LIBRO',
                [
                    'p_libro_id' => $libroId
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Préstamos del libro obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener préstamos del libro: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener préstamos vencidos
     */
    public function prestamosVencidos(): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMOS_VENCIDOS',
                []
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Préstamos vencidos obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener préstamos vencidos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener historial de préstamos
     */
    public function historialPrestamos(Request $request): JsonResponse
    {
        try {
            $fechaInicio = $request->get('fecha_inicio');
            $fechaFin = $request->get('fecha_fin');

            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_HISTORIAL_PRESTAMOS',
                [
                    'p_fecha_inicio' => $fechaInicio,
                    'p_fecha_fin' => $fechaFin
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Historial de préstamos obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener historial de préstamos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener libros más prestados
     */
    public function librosMasPrestados(Request $request): JsonResponse
    {
        try {
            $limite = $request->get('limite', 10);

            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_LIBROS_MAS_PRESTADOS',
                [
                    'p_limite' => $limite
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Libros más prestados obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener libros más prestados: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar estados vencidos
     */
    public function actualizarEstadosVencidos(): JsonResponse
    {
        try {
            $this->oracleService->executeProcedure(
                'PKG_PRESTAMOS.ACTUALIZAR_ESTADOS_VENCIDOS',
                []
            );

            return response()->json([
                'success' => true,
                'message' => 'Estados vencidos actualizados exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estados: ' . $e->getMessage()
            ], 500);
        }
    }
} 