@extends('biblioteca.layout')

@section('title', 'Gestión de Préstamos - Biblioteca Virtual')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-handshake text-green-600 mr-3"></i>
                Gestión de Préstamos
            </h1>
            <p class="mt-2 text-gray-600">Administra los préstamos de libros de la biblioteca</p>
        </div>
        <a href="{{ route('biblioteca.prestamos.create') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Préstamo
        </a>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" action="{{ route('biblioteca.prestamos.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda por libro -->
                <div>
                    <label for="libro" class="block text-sm font-medium text-gray-700 mb-1">Libro</label>
                    <input type="text" name="libro" id="libro" 
                           value="{{ request('libro') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Buscar por título...">
                </div>

                <!-- Búsqueda por usuario -->
                <div>
                    <label for="usuario" class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                    <input type="text" name="usuario" id="usuario" 
                           value="{{ request('usuario') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Buscar por usuario...">
                </div>

                <!-- Filtro por estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" id="estado" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="ACTIVO" {{ request('estado') == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                        <option value="DEVUELTO" {{ request('estado') == 'DEVUELTO' ? 'selected' : '' }}>Devuelto</option>
                        <option value="VENCIDO" {{ request('estado') == 'VENCIDO' ? 'selected' : '' }}>Vencido</option>
                    </select>
                </div>

                <!-- Filtro por fecha -->
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha Préstamo</label>
                    <input type="date" name="fecha" id="fecha" 
                           value="{{ request('fecha') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Buscar
                </button>
                
                @if(request('libro') || request('usuario') || request('estado') || request('fecha'))
                    <a href="{{ route('biblioteca.prestamos.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar Filtros
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Préstamos -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    @if(isset($prestamos) && count($prestamos) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'libro_titulo', 'order' => request('sort') == 'libro_titulo' && request('order') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="group inline-flex items-center">
                                Libro
                                <i class="fas fa-sort ml-1 text-gray-400 group-hover:text-gray-500"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'usuario_nombre', 'order' => request('sort') == 'usuario_nombre' && request('order') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="group inline-flex items-center">
                                Usuario
                                <i class="fas fa-sort ml-1 text-gray-400 group-hover:text-gray-500"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'fecha_prestamo', 'order' => request('sort') == 'fecha_prestamo' && request('order') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="group inline-flex items-center">
                                Fecha Préstamo
                                <i class="fas fa-sort ml-1 text-gray-400 group-hover:text-gray-500"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Devolución
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($prestamos as $prestamo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-book text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $prestamo['LIBRO_TITULO'] }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $prestamo['LIBRO_AUTOR'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-user text-green-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $prestamo['USUARIO_NOMBRE'] }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $prestamo['USUARIO_CORREO'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($prestamo['FECHA_PRESTAMO'])->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($prestamo['FECHA_DEVOLUCION'])
                                {{ \Carbon\Carbon::parse($prestamo['FECHA_DEVOLUCION'])->format('d/m/Y') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($prestamo['ESTADO'] == 'ACTIVO')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Activo
                                </span>
                            @elseif($prestamo['ESTADO'] == 'DEVUELTO')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-undo mr-1"></i>
                                    Devuelto
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Vencido
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('biblioteca.prestamos.show', $prestamo['ID']) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors"
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($prestamo['ESTADO'] == 'ACTIVO')
                                    <button onclick="confirmarDevolucion({{ $prestamo['ID'] }}, '{{ $prestamo['LIBRO_TITULO'] }}')" 
                                            class="text-green-600 hover:text-green-900 transition-colors"
                                            title="Devolver libro">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @endif
                                
                                <button onclick="confirmarEliminacion({{ $prestamo['ID'] }}, '{{ $prestamo['LIBRO_TITULO'] }}')" 
                                        class="text-red-600 hover:text-red-900 transition-colors"
                                        title="Eliminar préstamo">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Estado vacío -->
        <div class="text-center py-12">
            <i class="fas fa-handshake text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron préstamos</h3>
            <p class="text-gray-500 mb-6">
                @if(request('libro') || request('usuario') || request('estado') || request('fecha'))
                    No hay préstamos que coincidan con los filtros aplicados.
                @else
                    Aún no hay préstamos registrados en la biblioteca.
                @endif
            </p>
            <a href="{{ route('biblioteca.prestamos.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Registrar Primer Préstamo
            </a>
        </div>
    @endif
</div>

<!-- Modal de confirmación de devolución -->
<div id="modalDevolucion" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="fas fa-undo text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirmar Devolución</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    ¿Estás seguro de que quieres devolver el libro "<span id="libroTituloDevolucion"></span>"?
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="formDevolucion" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                        Devolver
                    </button>
                </form>
                <button onclick="cerrarModalDevolucion()" 
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div id="modalEliminar" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirmar Eliminación</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    ¿Estás seguro de que quieres eliminar el préstamo del libro "<span id="libroTitulo"></span>"?
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="formEliminar" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                        Eliminar
                    </button>
                </form>
                <button onclick="cerrarModal()" 
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmarDevolucion(id, titulo) {
    document.getElementById('libroTituloDevolucion').textContent = titulo;
    document.getElementById('formDevolucion').action = `/biblioteca/prestamos/${id}/devolver`;
    document.getElementById('modalDevolucion').classList.remove('hidden');
}

function cerrarModalDevolucion() {
    document.getElementById('modalDevolucion').classList.add('hidden');
}

function confirmarEliminacion(id, titulo) {
    document.getElementById('libroTitulo').textContent = titulo;
    document.getElementById('formEliminar').action = `/biblioteca/prestamos/${id}`;
    document.getElementById('modalEliminar').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalEliminar').classList.add('hidden');
}

// Cerrar modales al hacer clic fuera de ellos
document.getElementById('modalDevolucion').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalDevolucion();
    }
});

document.getElementById('modalEliminar').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
@endsection 