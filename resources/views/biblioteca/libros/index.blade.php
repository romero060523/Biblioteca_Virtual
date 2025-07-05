@extends('biblioteca.layout')

@section('title', 'Gestión de Libros - Biblioteca Virtual')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-books text-blue-600 mr-3"></i>
                Gestión de Libros
            </h1>
            <p class="mt-2 text-gray-600">Administra el catálogo de libros de la biblioteca</p>
        </div>
        <a href="{{ route('biblioteca.libros.create') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Nuevo Libro
        </a>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" action="{{ route('biblioteca.libros.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda por título -->
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="titulo" id="titulo" 
                           value="{{ request('titulo') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Buscar por título...">
                </div>

                <!-- Búsqueda por autor -->
                <div>
                    <label for="autor" class="block text-sm font-medium text-gray-700 mb-1">Autor</label>
                    <input type="text" name="autor" id="autor" 
                           value="{{ request('autor') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Buscar por autor...">
                </div>

                <!-- Filtro por categoría -->
                <div>
                    <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select name="categoria" id="categoria" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todas las categorías</option>
                        <option value="Ficción" {{ request('categoria') == 'Ficción' ? 'selected' : '' }}>Ficción</option>
                        <option value="No Ficción" {{ request('categoria') == 'No Ficción' ? 'selected' : '' }}>No Ficción</option>
                        <option value="Ciencia" {{ request('categoria') == 'Ciencia' ? 'selected' : '' }}>Ciencia</option>
                        <option value="Tecnología" {{ request('categoria') == 'Tecnología' ? 'selected' : '' }}>Tecnología</option>
                        <option value="Historia" {{ request('categoria') == 'Historia' ? 'selected' : '' }}>Historia</option>
                        <option value="Literatura" {{ request('categoria') == 'Literatura' ? 'selected' : '' }}>Literatura</option>
                    </select>
                </div>

                <!-- Filtro por disponibilidad -->
                <div>
                    <label for="disponible" class="block text-sm font-medium text-gray-700 mb-1">Disponibilidad</label>
                    <select name="disponible" id="disponible" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos</option>
                        <option value="1" {{ request('disponible') == '1' ? 'selected' : '' }}>Disponible</option>
                        <option value="0" {{ request('disponible') == '0' ? 'selected' : '' }}>No Disponible</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Buscar
                </button>
                
                @if(request('titulo') || request('autor') || request('categoria') || request('disponible'))
                    <a href="{{ route('biblioteca.libros.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar Filtros
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Tabla de Libros -->
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    @if(isset($libros) && count($libros) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'titulo', 'order' => request('sort') == 'titulo' && request('order') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="group inline-flex items-center">
                                Título
                                <i class="fas fa-sort ml-1 text-gray-400 group-hover:text-gray-500"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'autor', 'order' => request('sort') == 'autor' && request('order') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="group inline-flex items-center">
                                Autor
                                <i class="fas fa-sort ml-1 text-gray-400 group-hover:text-gray-500"></i>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Categoría
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'stock', 'order' => request('sort') == 'stock' && request('order') == 'asc' ? 'desc' : 'asc']) }}" 
                               class="group inline-flex items-center">
                                Stock
                                <i class="fas fa-sort ml-1 text-gray-400 group-hover:text-gray-500"></i>
                            </a>
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
                    @foreach($libros as $libro)
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
                                        {{ $libro['TITULO'] }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ISBN: {{ $libro['ISBN'] }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $libro['AUTOR'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $libro['CATEGORIA'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $libro['STOCK'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($libro['STOCK'] > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Disponible
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    No Disponible
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('biblioteca.libros.show', $libro['ID']) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors"
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('biblioteca.libros.edit', $libro['ID']) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmarEliminacion({{ $libro['ID'] }}, '{{ $libro['TITULO'] }}')" 
                                        class="text-red-600 hover:text-red-900 transition-colors"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if(isset($libros) && method_exists($libros, 'links'))
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $libros->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <!-- Estado vacío -->
        <div class="text-center py-12">
            <i class="fas fa-books text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron libros</h3>
            <p class="text-gray-500 mb-6">
                @if(request('titulo') || request('autor') || request('categoria') || request('disponible'))
                    No hay libros que coincidan con los filtros aplicados.
                @else
                    Aún no hay libros registrados en la biblioteca.
                @endif
            </p>
            <a href="{{ route('biblioteca.libros.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Agregar Primer Libro
            </a>
        </div>
    @endif
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
                    ¿Estás seguro de que quieres eliminar el libro "<span id="libroTitulo"></span>"?
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
function confirmarEliminacion(id, titulo) {
    document.getElementById('libroTitulo').textContent = titulo;
    document.getElementById('formEliminar').action = `/biblioteca/libros/${id}`;
    document.getElementById('modalEliminar').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalEliminar').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('modalEliminar').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
@endsection 