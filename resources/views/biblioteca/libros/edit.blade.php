@extends('biblioteca.layout')

@section('title', 'Editar Libro - Biblioteca Virtual')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-edit text-indigo-600 mr-3"></i>
                Editar Libro
            </h1>
            <p class="mt-2 text-gray-600">Modificar los datos del libro seleccionado</p>
        </div>
        <a href="{{ route('biblioteca.libros.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver a Libros
        </a>
    </div>
</div>

<!-- Formulario -->
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form method="POST" action="{{ route('biblioteca.libros.update', $libro['ID']) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <!-- Información Básica -->
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Información Básica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="titulo" id="titulo" 
                               value="{{ old('titulo', $libro['TITULO']) }}"
                               maxlength="200"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('titulo') border-red-500 @enderror"
                               placeholder="Ingrese el título del libro"
                               required>
                        @error('titulo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Autor -->
                    <div>
                        <label for="autor" class="block text-sm font-medium text-gray-700 mb-1">
                            Autor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="autor" id="autor" 
                               value="{{ old('autor', $libro['AUTOR']) }}"
                               maxlength="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('autor') border-red-500 @enderror"
                               placeholder="Ingrese el nombre del autor"
                               required>
                        @error('autor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Categoría -->
                    <div>
                        <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">
                            Categoría <span class="text-red-500">*</span>
                        </label>
                        <select name="categoria" id="categoria" 
                                maxlength="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('categoria') border-red-500 @enderror"
                                required>
                            <option value="">Seleccione una categoría</option>
                            <option value="Ficción" {{ old('categoria', $libro['CATEGORIA']) == 'Ficción' ? 'selected' : '' }}>Ficción</option>
                            <option value="No Ficción" {{ old('categoria', $libro['CATEGORIA']) == 'No Ficción' ? 'selected' : '' }}>No Ficción</option>
                            <option value="Ciencia" {{ old('categoria', $libro['CATEGORIA']) == 'Ciencia' ? 'selected' : '' }}>Ciencia</option>
                            <option value="Tecnología" {{ old('categoria', $libro['CATEGORIA']) == 'Tecnología' ? 'selected' : '' }}>Tecnología</option>
                            <option value="Historia" {{ old('categoria', $libro['CATEGORIA']) == 'Historia' ? 'selected' : '' }}>Historia</option>
                            <option value="Literatura" {{ old('categoria', $libro['CATEGORIA']) == 'Literatura' ? 'selected' : '' }}>Literatura</option>
                            <option value="Filosofía" {{ old('categoria', $libro['CATEGORIA']) == 'Filosofía' ? 'selected' : '' }}>Filosofía</option>
                            <option value="Psicología" {{ old('categoria', $libro['CATEGORIA']) == 'Psicología' ? 'selected' : '' }}>Psicología</option>
                            <option value="Economía" {{ old('categoria', $libro['CATEGORIA']) == 'Economía' ? 'selected' : '' }}>Economía</option>
                            <option value="Arte" {{ old('categoria', $libro['CATEGORIA']) == 'Arte' ? 'selected' : '' }}>Arte</option>
                            <option value="Otros" {{ old('categoria', $libro['CATEGORIA']) == 'Otros' ? 'selected' : '' }}>Otros</option>
                        </select>
                        @error('categoria')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <!-- Información Adicional -->
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                    Información Adicional
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Editorial -->
                    <div>
                        <label for="editorial" class="block text-sm font-medium text-gray-700 mb-1">
                            Editorial
                        </label>
                        <input type="text" name="editorial" id="editorial" 
                               value="{{ old('editorial', $libro['EDITORIAL'] ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('editorial') border-red-500 @enderror"
                               placeholder="Nombre de la editorial">
                        @error('editorial')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Año de Publicación -->
                    <div>
                        <label for="anio_publicacion" class="block text-sm font-medium text-gray-700 mb-1">
                            Año de Publicación
                        </label>
                        <input type="number" name="anio_publicacion" id="anio_publicacion" 
                               value="{{ old('anio_publicacion', $libro['ANIO_PUBLICACION'] ?? '') }}"
                               min="1800" max="{{ date('Y') + 1 }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('anio_publicacion') border-red-500 @enderror"
                               placeholder="Ej: 2020">
                        @error('anio_publicacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">
                            Stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock" id="stock" 
                               value="{{ old('stock', $libro['STOCK']) }}"
                               min="0" max="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('stock') border-red-500 @enderror"
                               placeholder="Cantidad disponible"
                               required>
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Precio -->
                    <div>
                        <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">
                            Precio (USD)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="precio" id="precio" 
                                   value="{{ old('precio', $libro['PRECIO'] ?? '') }}"
                                   min="0" step="0.01"
                                   class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('precio') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('precio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                    Descripción
                </label>
                <textarea name="descripcion" id="descripcion" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('descripcion') border-red-500 @enderror"
                          placeholder="Breve descripción del libro...">{{ old('descripcion', $libro['DESCRIPCION'] ?? '') }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Botones -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('biblioteca.libros.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 