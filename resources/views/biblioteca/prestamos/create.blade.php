@extends('biblioteca.layout')

@section('title', 'Nuevo Préstamo - Biblioteca Virtual')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-plus text-green-600 mr-3"></i>
                Nuevo Préstamo
            </h1>
            <p class="mt-2 text-gray-600">Registrar un nuevo préstamo de libro</p>
        </div>
        <a href="{{ route('biblioteca.prestamos.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver a Préstamos
        </a>
    </div>
</div>

<!-- Formulario -->
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form method="POST" action="{{ route('biblioteca.prestamos.store') }}" class="space-y-6">
            @csrf
            
            <!-- Selección de Libro (simple) -->
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-book text-blue-600 mr-2"></i>
                    Selección de Libro
                </h3>
                <div>
                    <label for="id_libro" class="block text-sm font-medium text-gray-700 mb-1">
                        Libro <span class="text-red-500">*</span>
                    </label>
                    <select name="id_libro" id="id_libro" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('id_libro') border-red-500 @enderror" required>
                        <option value="">Seleccione un libro</option>
                        @foreach($libros as $libro)
                            <option value="{{ $libro['ID'] }}" {{ old('id_libro') == $libro['ID'] ? 'selected' : '' }}>
                                {{ $libro['TITULO'] }} ({{ $libro['AUTOR'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_libro')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Selección de Usuario (eliminada por completo) -->

            <!-- Información del Préstamo -->
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-calendar text-purple-600 mr-2"></i>
                    Información del Préstamo
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha de Préstamo -->
                    <div>
                        <label for="fecha_prestamo" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Préstamo <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha_prestamo" id="fecha_prestamo" 
                               value="{{ old('fecha_prestamo', date('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('fecha_prestamo') border-red-500 @enderror"
                               required>
                        @error('fecha_prestamo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Devolución -->
                    <div>
                        <label for="fecha_devolucion" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Devolución <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha_devolucion" id="fecha_devolucion" 
                               value="{{ old('fecha_devolucion', date('Y-m-d', strtotime('+15 days'))) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('fecha_devolucion') border-red-500 @enderror"
                               required>
                        @error('fecha_devolucion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Observaciones -->
                    <div class="md:col-span-2">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                            Observaciones
                        </label>
                        <textarea name="observaciones" id="observaciones" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('observaciones') border-red-500 @enderror"
                                  placeholder="Observaciones adicionales sobre el préstamo...">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Resumen del Préstamo -->
            <div id="resumen_prestamo" class="bg-gray-50 rounded-lg p-4 hidden">
                <h4 class="text-lg font-medium text-gray-900 mb-3">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Resumen del Préstamo
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h5 class="font-medium text-gray-700">Libro:</h5>
                        <p id="resumen_libro" class="text-sm text-gray-600">-</p>
                    </div>
                    <div>
                        <h5 class="font-medium text-gray-700">Usuario:</h5>
                        <p id="resumen_usuario" class="text-sm text-gray-600">-</p>
                    </div>
                    <div>
                        <h5 class="font-medium text-gray-700">Fecha de Préstamo:</h5>
                        <p id="resumen_fecha_prestamo" class="text-sm text-gray-600">-</p>
                    </div>
                    <div>
                        <h5 class="font-medium text-gray-700">Fecha de Devolución:</h5>
                        <p id="resumen_fecha_devolucion" class="text-sm text-gray-600">-</p>
                    </div>
                </div>
            </div>

            <!-- Panel de administración manual -->
            <!-- Eliminado: administración manual de stock y estado -->
            <!-- Fin panel administración manual -->

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('biblioteca.prestamos.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Registrar Préstamo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const libroSearch = document.getElementById('libro_search');
    const libroResults = document.getElementById('libro_results');
    const libroId = document.getElementById('id_libro');
    
    const usuarioSearch = document.getElementById('usuario_search');
    const usuarioResults = document.getElementById('usuario_results');
    const usuarioId = document.getElementById('usuario_id');
    
    const fechaPrestamo = document.getElementById('fecha_prestamo');
    const fechaDevolucion = document.getElementById('fecha_devolucion');
    const resumenPrestamo = document.getElementById('resumen_prestamo');
    
    // Búsqueda de libros
    if (libroSearch) {
        libroSearch.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 2) {
                libroResults.classList.add('hidden');
                return;
            }
            
            fetch(`/biblioteca/libros/buscar/termino?termino=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    mostrarResultadosLibros(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
        
        // Ocultar resultados al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!libroSearch.contains(e.target) && !libroResults.contains(e.target)) {
                libroResults.classList.add('hidden');
            }
        });
    }
    
    // Actualizar resumen cuando cambien las fechas
    [fechaPrestamo, fechaDevolucion].forEach(input => {
        if (input) {
            input.addEventListener('change', actualizarResumen);
        }
    });
    
    function mostrarResultadosLibros(libros) {
        if (libros.length === 0) {
            libroResults.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron libros</div>';
        } else {
            libroResults.innerHTML = libros.map(libro => `
                <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                     onclick="seleccionarLibro(${libro.ID}, '${libro.TITULO}', '${libro.AUTOR}', ${libro.STOCK})">
                    <div class="font-medium text-gray-900">${libro.TITULO}</div>
                    <div class="text-sm text-gray-600">${libro.AUTOR} - ${libro.CATEGORIA}</div>
                    <div class="text-xs text-gray-500">Stock: ${libro.STOCK} disponibles</div>
                </div>
            `).join('');
        }
        libroResults.classList.remove('hidden');
    }
    
    function mostrarResultadosUsuarios(usuarios) {
        if (usuarios.length === 0) {
            usuarioResults.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron usuarios</div>';
        } else {
            usuarioResults.innerHTML = usuarios.map(usuario => `
                <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                     onclick="seleccionarUsuario(${usuario.ID}, '${usuario.NOMBRE}', '${usuario.EMAIL}')">
                    <div class="font-medium text-gray-900">${usuario.NOMBRE}</div>
                    <div class="text-sm text-gray-600">${usuario.EMAIL}</div>
                </div>
            `).join('');
        }
        usuarioResults.classList.remove('hidden');
    }
    
    function actualizarResumen() {
        if (libroId.value && usuarioId.value && fechaPrestamo.value && fechaDevolucion.value) {
            resumenPrestamo.classList.remove('hidden');
            // Aquí podrías hacer una llamada AJAX para obtener los detalles completos
        } else {
            resumenPrestamo.classList.add('hidden');
        }
    }
});

function seleccionarLibro(id, titulo, autor, stock) {
    document.getElementById('id_libro').value = id;
    document.getElementById('libro_search').value = titulo;
    document.getElementById('libro_results').classList.add('hidden');
    actualizarResumen();
}

function seleccionarUsuario(id, nombre, email) {
    document.getElementById('usuario_id').value = id;
    document.getElementById('usuario_search').value = nombre;
    document.getElementById('usuario_results').classList.add('hidden');
    actualizarResumen();
}

function actualizarResumen() {
    const resumenPrestamo = document.getElementById('resumen_prestamo');
    const fechaPrestamo = document.getElementById('fecha_prestamo').value;
    const fechaDevolucion = document.getElementById('fecha_devolucion').value;
    
    if (fechaPrestamo && fechaDevolucion) {
        document.getElementById('resumen_fecha_prestamo').textContent = new Date(fechaPrestamo).toLocaleDateString('es-ES');
        document.getElementById('resumen_fecha_devolucion').textContent = new Date(fechaDevolucion).toLocaleDateString('es-ES');
        resumenPrestamo.classList.remove('hidden');
    }
}
</script>
@endsection 