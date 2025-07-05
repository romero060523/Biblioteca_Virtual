@extends('biblioteca.layout')

@section('title', 'Dashboard - Biblioteca Virtual')

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i>
        BIENVENIDO
    </h1>
    <p class="mt-2 text-gray-600">Panel de control de la Biblioteca Virtual</p>
</div>

<!-- Estadísticas -->
{{-- Eliminado: tarjetas de estadísticas que no funcionan --}}

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
            <button disabled class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                <i class="fas fa-star text-gray-400 mr-2"></i>
                Libros Más Prestados
            </button>
            <button disabled class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                <i class="fas fa-history text-gray-400 mr-2"></i>
                Historial de Préstamos
            </button>
            <button disabled class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                <i class="fas fa-chart-pie text-gray-400 mr-2"></i>
                Estadísticas Generales
            </button>
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