@extends('biblioteca.layout')

@section('title', 'Dashboard - Biblioteca Virtual')

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i>
        Dashboard
    </h1>
    <p class="mt-2 text-gray-600">Panel de control de la Biblioteca Virtual</p>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total de Libros -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-books text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total de Libros
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $stats['total_libros'] ?? 0 }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Libros Disponibles -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-book text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Libros Disponibles
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $stats['libros_disponibles'] ?? 0 }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Préstamos Activos -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-handshake text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Préstamos Activos
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $stats['prestamos_activos'] ?? 0 }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Usuarios Registrados -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Usuarios Registrados
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $stats['total_usuarios'] ?? 0 }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="bg-white shadow rounded-lg mb-8">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            <i class="fas fa-bolt text-blue-600 mr-2"></i>
            Acciones Rápidas
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('biblioteca.libros.create') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Nuevo Libro
            </a>
            <a href="{{ route('biblioteca.prestamos.create') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <i class="fas fa-handshake mr-2"></i>
                Registrar Préstamo
            </a>
            <a href="{{ route('biblioteca.libros.buscar') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Buscar Libros
            </a>
            <a href="{{ route('biblioteca.prestamos.vencidos') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Préstamos Vencidos
            </a>
        </div>
    </div>
</div>

<!-- Contenido Principal -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Libros Recientes -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-book text-blue-600 mr-2"></i>
                    Libros Recientes
                </h3>
                <a href="{{ route('biblioteca.libros.index') }}" 
                   class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Ver Todos
                </a>
            </div>
            
            @if(isset($libros_recientes) && count($libros_recientes) > 0)
                <div class="space-y-3">
                    @foreach($libros_recientes as $libro)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $libro['TITULO'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $libro['AUTOR'] }} - {{ $libro['CATEGORIA'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $libro['STOCK'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $libro['STOCK'] }} disponibles
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-book-open text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No hay libros recientes</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Préstamos Recientes -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>
                    Préstamos Recientes
                </h3>
                <a href="{{ route('biblioteca.prestamos.index') }}" 
                   class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    Ver Todos
                </a>
            </div>
            
            @if(isset($prestamos_recientes) && count($prestamos_recientes) > 0)
                <div class="space-y-3">
                    @foreach($prestamos_recientes as $prestamo)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $prestamo['LIBRO_TITULO'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $prestamo['USUARIO_NOMBRE'] }} - {{ $prestamo['FECHA_PRESTAMO'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $prestamo['ESTADO'] == 'ACTIVO' ? 'bg-green-100 text-green-800' : 
                               ($prestamo['ESTADO'] == 'DEVUELTO' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $prestamo['ESTADO'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-handshake text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No hay préstamos recientes</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reportes Rápidos -->
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
            Reportes Rápidos
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('biblioteca.reportes.libros-mas-prestados') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-star text-yellow-500 mr-2"></i>
                Libros Más Prestados
            </a>
            <a href="{{ route('biblioteca.reportes.historial') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-history text-gray-500 mr-2"></i>
                Historial de Préstamos
            </a>
            <a href="{{ route('biblioteca.reportes.estadisticas') }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-chart-pie text-blue-500 mr-2"></i>
                Estadísticas Generales
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh de estadísticas cada 30 segundos (opcional)
    // setInterval(function() {
    //     // Aquí podrías hacer una llamada AJAX para actualizar las estadísticas
    // }, 30000);
});
</script>
@endsection 