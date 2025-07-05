<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class NavigationHelper
{
    /**
     * Obtener el menú principal
     */
    public static function getMainMenu()
    {
        return config('navigation.main_menu');
    }

    /**
     * Obtener las acciones rápidas
     */
    public static function getQuickActions()
    {
        return config('navigation.quick_actions');
    }

    /**
     * Verificar si una ruta está activa
     */
    public static function isActive($pattern)
    {
        $currentRoute = Route::currentRouteName();
        
        if (str_contains($pattern, '*')) {
            $pattern = str_replace('*', '', $pattern);
            return str_starts_with($currentRoute, $pattern);
        }
        
        return $currentRoute === $pattern;
    }

    /**
     * Obtener los breadcrumbs para la ruta actual
     */
    public static function getBreadcrumbs()
    {
        $currentRoute = Route::currentRouteName();
        $breadcrumbs = config('navigation.breadcrumbs');
        
        return $breadcrumbs[$currentRoute] ?? [];
    }

    /**
     * Generar HTML para breadcrumbs
     */
    public static function renderBreadcrumbs()
    {
        $breadcrumbs = self::getBreadcrumbs();
        
        if (empty($breadcrumbs)) {
            return '';
        }

        $html = '<nav class="flex" aria-label="Breadcrumb">';
        $html .= '<ol class="inline-flex items-center space-x-1 md:space-x-3">';
        
        foreach ($breadcrumbs as $index => $crumb) {
            if ($index === 0) {
                $html .= '<li class="inline-flex items-center">';
                $html .= '<a href="' . route('biblioteca.dashboard') . '" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">';
                $html .= '<i class="fas fa-home mr-2"></i>';
                $html .= $crumb;
                $html .= '</a>';
                $html .= '</li>';
            } else {
                $html .= '<li>';
                $html .= '<div class="flex items-center">';
                $html .= '<i class="fas fa-chevron-right text-gray-400 mx-2"></i>';
                $html .= '<span class="text-sm font-medium text-gray-500">' . $crumb . '</span>';
                $html .= '</div>';
                $html .= '</li>';
            }
        }
        
        $html .= '</ol>';
        $html .= '</nav>';
        
        return $html;
    }

    /**
     * Generar HTML para el menú de navegación
     */
    public static function renderMainMenu()
    {
        $menu = self::getMainMenu();
        $html = '';

        foreach ($menu as $item) {
            $isActive = self::isActive($item['active']);
            $hasChildren = isset($item['children']) && !empty($item['children']);
            
            $html .= '<li>';
            $html .= '<a href="' . route($item['route']) . '" ';
            $html .= 'class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors ';
            $html .= $isActive ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
            $html .= '">';
            $html .= '<i class="' . $item['icon'] . ' mr-3 flex-shrink-0 h-6 w-6"></i>';
            $html .= $item['title'];
            
            if ($hasChildren) {
                $html .= '<i class="fas fa-chevron-down ml-auto"></i>';
            }
            
            $html .= '</a>';
            
            if ($hasChildren) {
                $html .= '<ul class="mt-1 space-y-1 px-4 ' . ($isActive ? 'block' : 'hidden') . '">';
                foreach ($item['children'] as $child) {
                    $childActive = Route::currentRouteName() === $child['route'];
                    $html .= '<li>';
                    $html .= '<a href="' . route($child['route']) . '" ';
                    $html .= 'class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ';
                    $html .= $childActive ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
                    $html .= '">';
                    $html .= '<i class="' . $child['icon'] . ' mr-3 flex-shrink-0 h-5 w-5"></i>';
                    $html .= $child['title'];
                    $html .= '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
            
            $html .= '</li>';
        }

        return $html;
    }

    /**
     * Generar HTML para las acciones rápidas
     */
    public static function renderQuickActions()
    {
        $actions = self::getQuickActions();
        $html = '';

        foreach ($actions as $action) {
            $colorClasses = [
                'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
                'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
                'indigo' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
                'yellow' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
                'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
            ];

            $colorClass = $colorClasses[$action['color']] ?? $colorClasses['blue'];

            $html .= '<a href="' . route($action['route']) . '" ';
            $html .= 'class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white ';
            $html .= $colorClass . ' focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">';
            $html .= '<i class="' . $action['icon'] . ' mr-2"></i>';
            $html .= $action['title'];
            $html .= '</a>';
        }

        return $html;
    }

    /**
     * Obtener el título de la página actual
     */
    public static function getPageTitle()
    {
        $currentRoute = Route::currentRouteName();
        $breadcrumbs = self::getBreadcrumbs();
        
        if (!empty($breadcrumbs)) {
            return end($breadcrumbs);
        }
        
        // Fallback a un título basado en la ruta
        $routeParts = explode('.', $currentRoute);
        $lastPart = end($routeParts);
        
        return ucfirst(str_replace('-', ' ', $lastPart));
    }

    /**
     * Obtener la descripción de la página actual
     */
    public static function getPageDescription()
    {
        $currentRoute = Route::currentRouteName();
        
        $descriptions = [
            'biblioteca.dashboard' => 'Panel de control de la Biblioteca Virtual',
            'biblioteca.libros.index' => 'Administra el catálogo de libros de la biblioteca',
            'biblioteca.libros.create' => 'Agregar un nuevo libro al catálogo de la biblioteca',
            'biblioteca.libros.show' => 'Detalles del libro',
            'biblioteca.libros.edit' => 'Editar información del libro',
            'biblioteca.prestamos.index' => 'Administra los préstamos de libros de la biblioteca',
            'biblioteca.prestamos.create' => 'Registrar un nuevo préstamo de libro',
            'biblioteca.prestamos.show' => 'Detalles del préstamo',
            'biblioteca.prestamos.edit' => 'Editar información del préstamo',
            'biblioteca.prestamos.vencidos' => 'Préstamos que han vencido',
            'biblioteca.prestamos.historial' => 'Historial completo de préstamos',
            'biblioteca.usuarios.index' => 'Administra los usuarios registrados en la biblioteca',
            'biblioteca.usuarios.create' => 'Registrar un nuevo usuario en la biblioteca',
            'biblioteca.usuarios.show' => 'Detalles del usuario',
            'biblioteca.usuarios.edit' => 'Editar información del usuario',
            'biblioteca.reportes.estadisticas' => 'Estadísticas generales de la biblioteca',
            'biblioteca.reportes.libros-mas-prestados' => 'Libros más solicitados por los usuarios',
            'biblioteca.reportes.historial' => 'Historial detallado de préstamos',
            'biblioteca.reportes.prestamos-vencidos' => 'Reporte de préstamos vencidos',
            'biblioteca.reportes.usuarios-activos' => 'Usuarios con mayor actividad',
            'biblioteca.buscar.general' => 'Búsqueda general en toda la biblioteca',
            'biblioteca.buscar.libros' => 'Búsqueda específica de libros',
            'biblioteca.buscar.usuarios' => 'Búsqueda específica de usuarios',
            'biblioteca.buscar.prestamos' => 'Búsqueda específica de préstamos'
        ];
        
        return $descriptions[$currentRoute] ?? 'Página de la Biblioteca Virtual';
    }
} 