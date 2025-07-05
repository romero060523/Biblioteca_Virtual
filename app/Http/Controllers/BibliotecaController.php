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

    
    // GESTIÓN DE LIBROS
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
            if (strpos($e->getMessage(), 'bindColumn has not been implemented') !== false) {
                return response()->json([
                    'success' => true,
                    'message' => 'Libro actualizado exitosamente'
                ]);
            }
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
    

    // GESTIÓN DE PRÉSTAMOS
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
            if (strpos($e->getMessage(), 'bindColumn has not been implemented') !== false) {
                return response()->json([
                    'success' => true,
                    'message' => 'Préstamo registrado exitosamente'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar préstamo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Marcar préstamo como devuelto (vista web)
     */
    public function devolverLibro($id)
    {
        try {
            $this->oracleService->executeProcedure(
                'PKG_PRESTAMOS.DEVOLVER_LIBRO',
                [ 'p_prestamo_id' => $id ]
            );
            return redirect()->route('biblioteca.prestamos.show', $id)
                ->with('success', 'El préstamo fue marcado como devuelto y el stock del libro se actualizó.');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'bindColumn has not been implemented') !== false) {
                return redirect()->route('biblioteca.prestamos.show', $id)
                    ->with('success', 'El préstamo fue marcado como devuelto y el stock del libro se actualizó.');
            }
            return redirect()->route('biblioteca.prestamos.show', $id)
                ->with('error', 'Error al marcar como devuelto: ' . $e->getMessage());
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


    // DASHBOARD PRINCIPAL
    public function dashboard()
    {
        try {
            $libros_recientes = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBROS_RECIENTES'
            );
        } catch (\Exception $e) {
            $libros_recientes = [];
        }
        try {
            $prestamos_recientes = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMOS_RECIENTES'
            );
        } catch (\Exception $e) {
            $prestamos_recientes = [];
        }
        return view('biblioteca.dashboard', [
            'libros_recientes' => array_slice($libros_recientes, 0, 3),
            'prestamos_recientes' => $prestamos_recientes
        ]);
    }


    // VISTAS WEB PARA LIBROS
    /**
     * Mostrar vista de lista de libros
     */
    public function indexLibrosView(Request $request)
    {
        try {
            $categoria = $request->get('categoria');
            $autor = $request->get('autor');
            $titulo = $request->get('titulo');

            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.LISTAR_LIBROS',
                [
                    'p_categoria' => $categoria,
                    'p_autor' => $autor
                ]
            );

            // Filtrar por título si se proporciona
            if ($titulo) {
                $result = array_filter($result, function($libro) use ($titulo) {
                    return stripos($libro['TITULO'], $titulo) !== false;
                });
            }

            return view('biblioteca.libros.index', [
                'libros' => $result,
                'filtros' => [
                    'categoria' => $categoria,
                    'autor' => $autor,
                    'titulo' => $titulo
                ]
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.libros.index', [
                'libros' => [],
                'error' => 'Error al cargar libros: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar formulario para crear libro
     */
    public function createLibro()
    {
        return view('biblioteca.libros.create');
    }
    
    /**
     * Mostrar formulario para editar libro
     */
    public function editLibro($id)
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBRO',
                ['p_libro_id' => $id]
            );

            if (empty($result)) {
                return redirect()->route('biblioteca.libros.index')
                    ->with('error', 'Libro no encontrado');
            }

            return view('biblioteca.libros.edit', [
                'libro' => $result[0]
            ]);
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.libros.index')
                ->with('error', 'Error al cargar libro: ' . $e->getMessage());
        }
    }
    
    /**
     * Guardar libro desde formulario web
     */
    public function storeLibroView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'autor' => 'nullable|string|max:100',
            'categoria' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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

            return redirect()->route('biblioteca.libros.index')
                ->with('success', 'Libro creado exitosamente');
        } catch (\Exception $e) {
            // Verificar si el libro fue insertado realmente
            $existe = false;
            try {
                $libro = $this->oracleService->executeProcedureWithCursor(
                    'PKG_LIBROS.BUSCAR_LIBROS',
                    ['p_termino' => $request->titulo]
                );
                foreach ($libro as $item) {
                    if (strtolower(trim($item['TITULO'])) === strtolower(trim($request->titulo))) {
                        $existe = true;
                        break;
                    }
                }
            } catch (\Exception $e2) {
                // No hacer nada, solo evitar que falle la comprobación
            }
            if ($existe) {
                return redirect()->route('biblioteca.libros.index')
                    ->with('warning', 'El libro fue agregado, pero ocurrió un error secundario: ' . $e->getMessage());
            }
            return redirect()->back()
                ->with('error', 'Error al crear libro: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Actualizar libro desde formulario web
     */
    public function updateLibroView(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'autor' => 'nullable|string|max:100',
            'categoria' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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

            return redirect()->route('biblioteca.libros.index')
                ->with('success', 'Libro actualizado exitosamente');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'bindColumn has not been implemented') !== false) {
                return redirect()->route('biblioteca.libros.index')
                    ->with('success', 'Libro actualizado exitosamente');
            }
            return redirect()->back()
                ->with('error', 'Error al actualizar libro: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Eliminar libro desde vista web
     */
    public function destroyLibroView($id)
    {
        try {
            $this->oracleService->executeProcedure(
                'PKG_LIBROS.ELIMINAR_LIBRO',
                ['p_libro_id' => $id]
            );

            return redirect()->route('biblioteca.libros.index')
                ->with('success', 'Libro eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.libros.index')
                ->with('error', 'Error al eliminar libro: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar detalles de un libro
     */
    public function showLibroView($id)
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBRO',
                ['p_libro_id' => $id]
            );

            if (empty($result)) {
                return redirect()->route('biblioteca.libros.index')
                    ->with('error', 'Libro no encontrado');
            }

            return view('biblioteca.libros.show', [
                'libro' => $result[0]
            ]);
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.libros.index')
                ->with('error', 'Error al cargar libro: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar libros disponibles
     */
    public function librosDisponiblesView()
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBROS_DISPONIBLES'
            );

            return view('biblioteca.libros.disponibles', [
                'libros' => $result
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.libros.disponibles', [
                'libros' => [],
                'error' => 'Error al cargar libros disponibles: ' . $e->getMessage()
            ]);
        }
    }


    // VISTAS WEB PARA PRÉSTAMOS
    /**
     * Mostrar vista de lista de préstamos
     */
    public function indexPrestamos(Request $request)
    {
        try {
            $estado = $request->get('estado');
            $usuario = $request->get('usuario');

            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.LISTAR_PRESTAMOS',
                [
                    'p_estado' => $estado
                ]
            );

            return view('biblioteca.prestamos.index', [
                'prestamos' => $result,
                'filtros' => [
                    'estado' => $estado,
                    'usuario' => $usuario
                ]
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.prestamos.index', [
                'prestamos' => [],
                'error' => 'Error al cargar préstamos: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar formulario para crear préstamo
     */
    public function createPrestamo()
    {
        try {
            // Obtener libros disponibles
            $libros = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBROS_DISPONIBLES'
            );
            
            // No cargar usuarios (eliminado)

            return view('biblioteca.prestamos.create', [
                'libros' => $libros
            ]);
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.prestamos.index')
                ->with('error', 'Error al cargar datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar formulario para editar préstamo
     */
    public function editPrestamo($id)
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMO',
                ['p_prestamo_id' => $id]
            );

            if (empty($result)) {
                return redirect()->route('biblioteca.prestamos.index')
                    ->with('error', 'Préstamo no encontrado');
            }

            // Obtener libros disponibles
            $libros = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBROS_DISPONIBLES'
            );
            
            // Obtener usuarios
            $usuarios = $this->oracleService->executeProcedureWithCursor(
                'PKG_USUARIOS.LISTAR_USUARIOS'
            );

            return view('biblioteca.prestamos.edit', [
                'prestamo' => $result[0],
                'libros' => $libros,
                'usuarios' => $usuarios
            ]);
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.prestamos.index')
                ->with('error', 'Error al cargar préstamo: ' . $e->getMessage());
        }
    }
    
    /**
     * Guardar préstamo desde formulario web
     */
    public function storePrestamo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_libro' => 'required|integer',
            'fecha_prestamo' => 'required|date',
            'fecha_devolucion' => 'required|date|after:fecha_prestamo'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Convertir fechas al formato Y-m-d para Oracle
            $p_fecha_prestamo = date('Y-m-d', strtotime($request->fecha_prestamo));
            $p_fecha_devolucion = date('Y-m-d', strtotime($request->fecha_devolucion));

            $this->oracleService->executeProcedure(
                'PKG_PRESTAMOS.REGISTRAR_PRESTAMO',
                [
                    'p_usuario_id' => 3, // ID del usuario administrador
                    'p_libro_id' => $request->id_libro,
                    'p_fecha_prestamo' => $p_fecha_prestamo,
                    'p_fecha_devolucion' => $p_fecha_devolucion,
                    'p_prestamo_id' => null
                ]
            );
            return redirect()->route('biblioteca.prestamos.index')
                ->with('success', 'Préstamo registrado exitosamente');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'bindColumn has not been implemented') !== false) {
                return redirect()->route('biblioteca.prestamos.index')
                    ->with('success', 'Préstamo registrado exitosamente');
            }
            return redirect()->back()
                ->with('error', 'Error al registrar préstamo: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Actualizar préstamo desde formulario web
     */
    public function updatePrestamo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|integer',
            'id_libro' => 'required|integer',
            'fecha_prestamo' => 'required|date',
            'fecha_devolucion' => 'required|date|after:fecha_prestamo',
            'estado' => 'required|in:ACTIVO,DEVUELTO,VENCIDO'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->oracleService->executeProcedure(
                'PKG_PRESTAMOS.ACTUALIZAR_PRESTAMO',
                [
                    'p_prestamo_id' => $id,
                    'p_usuario_id' => $request->id_usuario,
                    'p_libro_id' => $request->id_libro,
                    'p_fecha_prestamo' => $request->fecha_prestamo,
                    'p_fecha_devolucion' => $request->fecha_devolucion,
                    'p_estado' => $request->estado
                ]
            );

            return redirect()->route('biblioteca.prestamos.index')
                ->with('success', 'Préstamo actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar préstamo: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Eliminar préstamo desde vista web
     */
    public function destroyPrestamo($id)
    {
        try {
            // Obtener el préstamo para verificar el estado
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMO',
                ['p_prestamo_id' => $id]
            );
            if (empty($result)) {
                return redirect()->route('biblioteca.prestamos.index')
                    ->with('error', 'Préstamo no encontrado');
            }
            $prestamo = $result[0];
            if ($prestamo['ESTADO'] !== 'DEVUELTO') {
                return redirect()->route('biblioteca.prestamos.index')
                    ->with('error', 'Solo se pueden eliminar préstamos con estado DEVUELTO.');
            }
            // Intentar eliminar el préstamo
            $this->oracleService->executeProcedure(
                'PKG_PRESTAMOS.ELIMINAR_PRESTAMO',
                ['p_prestamo_id' => $id]
            );
            return redirect()->route('biblioteca.prestamos.index')
                ->with('success', 'Préstamo eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.prestamos.index')
                ->with('error', 'Error al eliminar préstamo: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar detalles de un préstamo
     */
    public function showPrestamoView($id)
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMO',
                ['p_prestamo_id' => $id]
            );

            if (empty($result)) {
                return redirect()->route('biblioteca.prestamos.index')
                    ->with('error', 'Préstamo no encontrado');
            }

            return view('biblioteca.prestamos.show', [
                'prestamo' => $result[0]
            ]);
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.prestamos.index')
                ->with('error', 'Error al cargar préstamo: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar préstamos vencidos
     */
    public function prestamosVencidosView()
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMOS_VENCIDOS'
            );

            return view('biblioteca.prestamos.vencidos', [
                'prestamos' => $result
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.prestamos.vencidos', [
                'prestamos' => [],
                'error' => 'Error al cargar préstamos vencidos: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar historial de préstamos
     */
    public function historialPrestamosView(Request $request)
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

            return view('biblioteca.prestamos.historial', [
                'prestamos' => $result,
                'filtros' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                ]
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.prestamos.historial', [
                'prestamos' => [],
                'error' => 'Error al cargar historial: ' . $e->getMessage()
            ]);
        }
    }


    // VISTAS WEB PARA USUARIOS
    /**
     * Mostrar vista de lista de usuarios
     */
    public function indexUsuarios(Request $request)
    {
        try {
            $rol = $request->get('rol');
            $nombre = $request->get('nombre');

            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_USUARIOS.LISTAR_USUARIOS',
                ['p_rol' => $rol]
            );

            // Filtrar por nombre si se proporciona
            if ($nombre) {
                $result = array_filter($result, function($usuario) use ($nombre) {
                    return stripos($usuario['NOMBRE'], $nombre) !== false;
                });
            }

            return view('biblioteca.usuarios.index', [
                'usuarios' => $result,
                'filtros' => [
                    'rol' => $rol,
                    'nombre' => $nombre
                ]
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.usuarios.index', [
                'usuarios' => [],
                'error' => 'Error al cargar usuarios: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar formulario para crear usuario
     */
    public function createUsuario()
    {
        return view('biblioteca.usuarios.create');
    }
    
    /**
     * Mostrar formulario para editar usuario
     */
    public function editUsuario($id)
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_USUARIOS.OBTENER_USUARIO',
                ['p_usuario_id' => $id]
            );

            if (empty($result)) {
                return redirect()->route('biblioteca.usuarios.index')
                    ->with('error', 'Usuario no encontrado');
            }

            return view('biblioteca.usuarios.edit', [
                'usuario' => $result[0]
            ]);
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.usuarios.index')
                ->with('error', 'Error al cargar usuario: ' . $e->getMessage());
        }
    }
    
    /**
     * Guardar usuario desde formulario web
     */
    public function storeUsuario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|max:100',
            'contraseña' => 'required|string|min:6|max:100',
            'rol' => 'required|in:USUARIO,BIBLIOTECARIO'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $result = $this->oracleService->executeProcedure(
                'PKG_USUARIOS.INSERTAR_USUARIO',
                [
                    'p_nombre' => $request->nombre,
                    'p_correo' => $request->correo,
                    'p_contraseña' => $request->contraseña,
                    'p_rol' => $request->rol,
                    'p_usuario_id' => null
                ]
            );

            return redirect()->route('biblioteca.usuarios.index')
                ->with('success', 'Usuario creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear usuario: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Actualizar usuario desde formulario web
     */
    public function updateUsuario(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|max:100',
            'rol' => 'required|in:USUARIO,BIBLIOTECARIO'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->oracleService->executeProcedure(
                'PKG_USUARIOS.ACTUALIZAR_USUARIO',
                [
                    'p_usuario_id' => $id,
                    'p_nombre' => $request->nombre,
                    'p_correo' => $request->correo,
                    'p_rol' => $request->rol
                ]
            );

            return redirect()->route('biblioteca.usuarios.index')
                ->with('success', 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar usuario: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Eliminar usuario desde vista web
     */
    public function destroyUsuario($id)
    {
        try {
            $this->oracleService->executeProcedure(
                'PKG_USUARIOS.ELIMINAR_USUARIO',
                ['p_usuario_id' => $id]
            );

            return redirect()->route('biblioteca.usuarios.index')
                ->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.usuarios.index')
                ->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar detalles de un usuario
     */
    public function showUsuarioView($id)
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_USUARIOS.OBTENER_USUARIO',
                ['p_usuario_id' => $id]
            );

            if (empty($result)) {
                return redirect()->route('biblioteca.usuarios.index')
                    ->with('error', 'Usuario no encontrado');
            }

            return view('biblioteca.usuarios.show', [
                'usuario' => $result[0]
            ]);
        } catch (\Exception $e) {
            return redirect()->route('biblioteca.usuarios.index')
                ->with('error', 'Error al cargar usuario: ' . $e->getMessage());
        }
    }


    // VISTAS WEB PARA REPORTES
    /**
     * Mostrar estadísticas generales
     */
    public function reportesEstadisticas()
    {
        try {
            $stats = $this->oracleService->executeProcedureWithCursor(
                'PKG_REPORTES.OBTENER_ESTADISTICAS_GENERALES'
            );

            return view('biblioteca.reportes.estadisticas', [
                'stats' => $stats[0] ?? []
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.reportes.estadisticas', [
                'stats' => [],
                'error' => 'Error al cargar estadísticas: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar libros más prestados
     */
    public function reportesLibrosMasPrestados()
    {
        try {
            $libros = $this->oracleService->executeProcedureWithCursor(
                'PKG_REPORTES.OBTENER_LIBROS_MAS_PRESTADOS'
            );

            return view('biblioteca.reportes.libros-mas-prestados', [
                'libros' => $libros
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.reportes.libros-mas-prestados', [
                'libros' => [],
                'error' => 'Error al cargar reporte: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar historial de préstamos
     */
    public function reportesHistorial()
    {
        try {
            $prestamos = $this->oracleService->executeProcedureWithCursor(
                'PKG_REPORTES.OBTENER_HISTORIAL_PRESTAMOS'
            );

            return view('biblioteca.reportes.historial', [
                'prestamos' => $prestamos
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.reportes.historial', [
                'prestamos' => [],
                'error' => 'Error al cargar historial: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar préstamos vencidos
     */
    public function reportesPrestamosVencidos()
    {
        try {
            $prestamos = $this->oracleService->executeProcedureWithCursor(
                'PKG_REPORTES.OBTENER_PRESTAMOS_VENCIDOS'
            );

            return view('biblioteca.reportes.prestamos-vencidos', [
                'prestamos' => $prestamos
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.reportes.prestamos-vencidos', [
                'prestamos' => [],
                'error' => 'Error al cargar préstamos vencidos: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mostrar usuarios más activos
     */
    public function reportesUsuariosActivos()
    {
        try {
            $usuarios = $this->oracleService->executeProcedureWithCursor(
                'PKG_REPORTES.OBTENER_USUARIOS_ACTIVOS'
            );

            return view('biblioteca.reportes.usuarios-activos', [
                'usuarios' => $usuarios
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.reportes.usuarios-activos', [
                'usuarios' => [],
                'error' => 'Error al cargar usuarios activos: ' . $e->getMessage()
            ]);
        }
    }


    // VISTAS WEB PARA BÚSQUEDA
    /**
     * Búsqueda general
     */
    public function busquedaGeneral(Request $request)
    {
        $termino = $request->get('termino');
        
        if (!$termino) {
            return view('biblioteca.buscar.general', [
                'resultados' => [],
                'termino' => ''
            ]);
        }

        try {
            $libros = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.BUSCAR_LIBROS',
                ['p_termino' => $termino]
            );
            
            $usuarios = $this->oracleService->executeProcedureWithCursor(
                'PKG_USUARIOS.BUSCAR_USUARIOS',
                ['p_termino' => $termino]
            );
            
            $prestamos = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.BUSCAR_PRESTAMOS',
                ['p_termino' => $termino]
            );

            return view('biblioteca.buscar.general', [
                'resultados' => [
                    'libros' => $libros,
                    'usuarios' => $usuarios,
                    'prestamos' => $prestamos
                ],
                'termino' => $termino
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.buscar.general', [
                'resultados' => [],
                'termino' => $termino,
                'error' => 'Error en la búsqueda: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Búsqueda de libros
     */
    public function busquedaLibros(Request $request)
    {
        $termino = $request->get('termino');
        
        if (!$termino) {
            return view('biblioteca.buscar.libros', [
                'libros' => [],
                'termino' => ''
            ]);
        }

        try {
            $libros = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.BUSCAR_LIBROS',
                ['p_termino' => $termino]
            );

            return view('biblioteca.buscar.libros', [
                'libros' => $libros,
                'termino' => $termino
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.buscar.libros', [
                'libros' => [],
                'termino' => $termino,
                'error' => 'Error en la búsqueda: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Búsqueda de usuarios
     */
    public function busquedaUsuarios(Request $request)
    {
        $termino = $request->get('termino');
        
        if (!$termino) {
            return view('biblioteca.buscar.usuarios', [
                'usuarios' => [],
                'termino' => ''
            ]);
        }

        try {
            $usuarios = $this->oracleService->executeProcedureWithCursor(
                'PKG_USUARIOS.BUSCAR_USUARIOS',
                ['p_termino' => $termino]
            );

            return view('biblioteca.buscar.usuarios', [
                'usuarios' => $usuarios,
                'termino' => $termino
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.buscar.usuarios', [
                'usuarios' => [],
                'termino' => $termino,
                'error' => 'Error en la búsqueda: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Búsqueda de préstamos
     */
    public function busquedaPrestamos(Request $request)
    {
        $termino = $request->get('termino');
        
        if (!$termino) {
            return view('biblioteca.buscar.prestamos', [
                'prestamos' => [],
                'termino' => ''
            ]);
        }

        try {
            $prestamos = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.BUSCAR_PRESTAMOS',
                ['p_termino' => $termino]
            );

            return view('biblioteca.buscar.prestamos', [
                'prestamos' => $prestamos,
                'termino' => $termino
            ]);
        } catch (\Exception $e) {
            return view('biblioteca.buscar.prestamos', [
                'prestamos' => [],
                'termino' => $termino,
                'error' => 'Error en la búsqueda: ' . $e->getMessage()
            ]);
        }
    }


    // API METHODS (AJAX)
    /**
     * Obtener estadísticas para el dashboard
     */
    public function getStats(): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_REPORTES.OBTENER_ESTADISTICAS_GENERALES'
            );

            return response()->json([
                'success' => true,
                'data' => $result[0] ?? [],
                'message' => 'Estadísticas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener libros recientes
     */
    public function getLibrosRecientes(): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBROS_RECIENTES'
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Libros recientes obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener libros recientes: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener préstamos recientes
     */
    public function getPrestamosRecientes(): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMOS_RECIENTES'
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Préstamos recientes obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener préstamos recientes: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verificar disponibilidad de un libro
     */
    public function checkDisponibilidad($id): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_LIBROS.OBTENER_LIBRO',
                ['p_libro_id' => $id]
            );

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Libro no encontrado'
                ], 404);
            }

            $libro = $result[0];
            $disponible = $libro['STOCK'] > 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'disponible' => $disponible,
                    'stock' => $libro['STOCK'],
                    'titulo' => $libro['TITULO']
                ],
                'message' => $disponible ? 'Libro disponible' : 'Libro no disponible'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar disponibilidad: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener información de un usuario
     */
    public function getUsuarioInfo($id): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_USUARIOS.OBTENER_USUARIO',
                ['p_usuario_id' => $id]
            );

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $usuario = $result[0];
            
            // Obtener préstamos activos del usuario
            $prestamosActivos = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.OBTENER_PRESTAMOS_USUARIO',
                [
                    'p_usuario_id' => $id,
                    'p_estado' => 'ACTIVO'
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'usuario' => $usuario,
                    'prestamos_activos' => $prestamosActivos,
                    'cantidad_prestamos_activos' => count($prestamosActivos),
                    'puede_prestar' => count($prestamosActivos) < 3
                ],
                'message' => 'Información de usuario obtenida exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de usuario: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Buscar préstamos (AJAX)
     */
    public function buscarPrestamos(Request $request): JsonResponse
    {
        try {
            $termino = $request->get('termino');
            
            if (!$termino) {
                return response()->json([
                    'success' => false,
                    'message' => 'Término de búsqueda requerido'
                ], 400);
            }

            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_PRESTAMOS.BUSCAR_PRESTAMOS',
                ['p_termino' => $termino]
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
     * Obtener usuarios activos
     */
    public function getUsuariosActivos(): JsonResponse
    {
        try {
            $result = $this->oracleService->executeProcedureWithCursor(
                'PKG_REPORTES.OBTENER_USUARIOS_ACTIVOS'
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Usuarios activos obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios activos: ' . $e->getMessage()
            ], 500);
        }
    }
} 