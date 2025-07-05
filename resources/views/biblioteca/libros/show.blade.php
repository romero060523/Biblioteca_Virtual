@extends('biblioteca.layout')

@section('title', $libro['TITULO'] . ' - Biblioteca Virtual')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-book text-blue-600 mr-3"></i>
                {{ $libro['TITULO'] }}
            </h1>
            <p class="mt-2 text-gray-600">Detalles del libro</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('biblioteca.libros.edit', $libro['ID']) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Editar
            </a>
            <a href="{{ route('biblioteca.libros.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Información Principal -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Estado del Libro -->
                <div class="mb-6">
                    @if($libro['STOCK'] > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            Disponible ({{ $libro['STOCK'] }} ejemplares)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-2"></i>
                            No Disponible
                        </span>
                    @endif
                </div>

                <!-- Información Básica -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Información Básica
                        </h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Título</dt>
                                <dd class="text-sm text-gray-900">{{ $libro['TITULO'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Autor</dt>
                                <dd class="text-sm text-gray-900">{{ $libro['AUTOR'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $libro['CATEGORIA'] }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                            Información Adicional
                        </h3>
                        <dl class="space-y-3">
                            @if(isset($libro['EDITORIAL']) && $libro['EDITORIAL'])
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Editorial</dt>
                                    <dd class="text-sm text-gray-900">{{ $libro['EDITORIAL'] }}</dd>
                                </div>
                            @endif
                            @if(isset($libro['ANIO_PUBLICACION']) && $libro['ANIO_PUBLICACION'])
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Año de Publicación</dt>
                                    <dd class="text-sm text-gray-900">{{ $libro['ANIO_PUBLICACION'] }}</dd>
                                </div>
                            @endif
                            @if(isset($libro['PRECIO']) && $libro['PRECIO'])
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Precio</dt>
                                    <dd class="text-sm text-gray-900">${{ number_format($libro['PRECIO'], 2) }} USD</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Stock Actual</dt>
                                <dd class="text-sm text-gray-900 font-semibold">{{ $libro['STOCK'] }} ejemplares</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Descripción -->
                @if(isset($libro['DESCRIPCION']) && $libro['DESCRIPCION'])
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">
                            <i class="fas fa-align-left text-purple-600 mr-2"></i>
                            Descripción
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700 leading-relaxed">
                                {{ $libro['DESCRIPCION'] }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Estadísticas del Libro -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-chart-bar text-yellow-600 mr-2"></i>
                        Estadísticas
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_prestamos'] ?? 0 }}</div>
                            <div class="text-sm text-blue-700">Total Préstamos</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['prestamos_activos'] ?? 0 }}</div>
                            <div class="text-sm text-green-700">Préstamos Activos</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['prestamos_vencidos'] ?? 0 }}</div>
                            <div class="text-sm text-purple-700">Préstamos Vencidos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Lateral -->
    <div class="lg:col-span-1">
        <!-- Acciones Rápidas -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-bolt text-blue-600 mr-2"></i>
                    Acciones Rápidas
                </h3>
                <div class="space-y-3">
                    @if($libro['STOCK'] > 0)
                        <a href="{{ route('biblioteca.prestamos.create', ['libro_id' => $libro['ID']]) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <i class="fas fa-handshake mr-2"></i>
                            Prestar Libro
                        </a>
                    @else
                        <button disabled 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            <i class="fas fa-handshake mr-2"></i>
                            No Disponible
                        </button>
                    @endif
                    
                    <a href="{{ route('biblioteca.libros.edit', $libro['ID']) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Información
                    </a>
                    
                    <button onclick="confirmarEliminacion({{ $libro['ID'] }}, '{{ $libro['TITULO'] }}')" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Eliminar Libro
                    </button>
                </div>
            </div>
        </div>

        <!-- Préstamos Recientes -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-history text-green-600 mr-2"></i>
                    Préstamos Recientes
                </h3>
                
                @if(isset($prestamos_recientes) && count($prestamos_recientes) > 0)
                    <div class="space-y-3">
                        @foreach($prestamos_recientes as $prestamo)
                        <div class="border-l-4 border-blue-500 pl-3 py-2">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $prestamo['USUARIO_NOMBRE'] }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $prestamo['FECHA_PRESTAMO'] }}
                            </div>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                    {{ $prestamo['ESTADO'] == 'ACTIVO' ? 'bg-green-100 text-green-800' : 
                                       ($prestamo['ESTADO'] == 'DEVUELTO' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $prestamo['ESTADO'] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('biblioteca.prestamos.index', ['libro_id' => $libro['ID']]) }}" 
                           class="text-sm text-blue-600 hover:text-blue-800">
                            Ver todos los préstamos →
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-handshake text-gray-400 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">No hay préstamos recientes</p>
                    </div>
                @endif
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
                    ¿Estás seguro de que quieres eliminar el libro "<span id="libroTitulo"></span>"?
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Esta acción no se puede deshacer y eliminará todos los registros relacionados.
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