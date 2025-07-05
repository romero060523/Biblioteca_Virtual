@extends('biblioteca.layout')

@section('title', 'Detalle del Préstamo')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-8 mt-8 border-t-4 border-green-500">
    <h1 class="text-2xl font-bold mb-6 text-green-700 flex items-center">
        <i class="fas fa-eye mr-2"></i> Detalle del Préstamo
    </h1>
    <div class="space-y-4">
        <div class="flex items-center bg-gray-50 rounded p-3">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Libro:</span>
                <span class="text-gray-900">{{ $prestamo['LIBRO_TITULO'] }}</span>
                <span class="text-gray-500">({{ $prestamo['LIBRO_AUTOR'] }})</span>
            </div>
        </div>
        <div class="flex items-center bg-gray-50 rounded p-3">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                    <i class="fas fa-user text-green-600 text-xl"></i>
                </div>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Usuario:</span>
                <span class="text-gray-900">{{ $prestamo['USUARIO_NOMBRE'] }}</span>
                <span class="text-gray-500">({{ $prestamo['USUARIO_CORREO'] }})</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded p-3">
                <span class="font-semibold text-gray-700">Fecha Préstamo:</span>
                <span class="text-gray-900">{{ \Carbon\Carbon::parse($prestamo['FECHA_PRESTAMO'])->format('d/m/Y') }}</span>
            </div>
            <div class="bg-gray-50 rounded p-3">
                <span class="font-semibold text-gray-700">Fecha Devolución:</span>
                <span class="text-gray-900">
                    @if($prestamo['FECHA_DEVOLUCION'])
                        {{ \Carbon\Carbon::parse($prestamo['FECHA_DEVOLUCION'])->format('d/m/Y') }}
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </span>
            </div>
        </div>
        <div class="bg-gray-50 rounded p-3 flex items-center">
            <span class="font-semibold text-gray-700 mr-2">Estado:</span>
            @if($prestamo['ESTADO'] == 'ACTIVO')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i> Activo
                </span>
            @elseif($prestamo['ESTADO'] == 'DEVUELTO')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-undo mr-1"></i> Devuelto
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Vencido
                </span>
            @endif
        </div>
    </div>
    <div class="flex justify-center mt-8">
        @if($prestamo['ESTADO'] == 'ACTIVO')
            <form method="POST" action="{{ route('biblioteca.prestamos.devolver', $prestamo['ID']) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold flex items-center">
                    <i class="fas fa-undo mr-2"></i> Marcar como Devuelto
                </button>
            </form>
        @endif
        <a href="{{ route('biblioteca.prestamos.index') }}" class="ml-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 flex items-center">
            <i class="fas fa-arrow-left mr-1"></i> Volver a la lista
        </a>
    </div>
</div>
@endsection 