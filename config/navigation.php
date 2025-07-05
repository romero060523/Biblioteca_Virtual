<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Navegación - Biblioteca Virtual
    |--------------------------------------------------------------------------
    |
    | Este archivo contiene la configuración de navegación para la
    | aplicación de Biblioteca Virtual.
    |
    */

    'main_menu' => [
        [
            'title' => 'Dashboard',
            'route' => 'biblioteca.dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'active' => 'biblioteca.dashboard'
        ],
        [
            'title' => 'Libros',
            'route' => 'biblioteca.libros.index',
            'icon' => 'fas fa-books',
            'active' => 'biblioteca.libros.*',
            'children' => [
                [
                    'title' => 'Lista de Libros',
                    'route' => 'biblioteca.libros.index',
                    'icon' => 'fas fa-list'
                ],
                [
                    'title' => 'Nuevo Libro',
                    'route' => 'biblioteca.libros.create',
                    'icon' => 'fas fa-plus'
                ],
                [
                    'title' => 'Libros Disponibles',
                    'route' => 'biblioteca.libros.disponibles',
                    'icon' => 'fas fa-check-circle'
                ]
            ]
        ],
        [
            'title' => 'Préstamos',
            'route' => 'biblioteca.prestamos.index',
            'icon' => 'fas fa-handshake',
            'active' => 'biblioteca.prestamos.*',
            'children' => [
                [
                    'title' => 'Lista de Préstamos',
                    'route' => 'biblioteca.prestamos.index',
                    'icon' => 'fas fa-list'
                ],
                [
                    'title' => 'Nuevo Préstamo',
                    'route' => 'biblioteca.prestamos.create',
                    'icon' => 'fas fa-plus'
                ],
                [
                    'title' => 'Préstamos Vencidos',
                    'route' => 'biblioteca.prestamos.vencidos',
                    'icon' => 'fas fa-exclamation-triangle'
                ],
                [
                    'title' => 'Historial',
                    'route' => 'biblioteca.prestamos.historial',
                    'icon' => 'fas fa-history'
                ]
            ]
        ]
    ],

    'quick_actions' => [
        [
            'title' => 'Nuevo Libro',
            'route' => 'biblioteca.libros.create',
            'icon' => 'fas fa-plus',
            'color' => 'blue'
        ],
        [
            'title' => 'Nuevo Préstamo',
            'route' => 'biblioteca.prestamos.create',
            'icon' => 'fas fa-handshake',
            'color' => 'green'
        ],
        [
            'title' => 'Préstamos Vencidos',
            'route' => 'biblioteca.prestamos.vencidos',
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'yellow'
        ]
    ],

    'breadcrumbs' => [
        'biblioteca.dashboard' => ['Dashboard'],
        'biblioteca.libros.index' => ['Libros', 'Lista'],
        'biblioteca.libros.create' => ['Libros', 'Nuevo Libro'],
        'biblioteca.libros.show' => ['Libros', 'Detalles'],
        'biblioteca.libros.edit' => ['Libros', 'Editar'],
        'biblioteca.prestamos.index' => ['Préstamos', 'Lista'],
        'biblioteca.prestamos.create' => ['Préstamos', 'Nuevo Préstamo'],
        'biblioteca.prestamos.show' => ['Préstamos', 'Detalles'],
        'biblioteca.prestamos.edit' => ['Préstamos', 'Editar'],
        'biblioteca.prestamos.vencidos' => ['Préstamos', 'Vencidos'],
        'biblioteca.prestamos.historial' => ['Préstamos', 'Historial']
    ]
]; 