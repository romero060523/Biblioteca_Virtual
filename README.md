<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Biblioteca Virtual

Sistema de gestión de libros y préstamos desarrollado en Laravel + Oracle.

## Requisitos

- PHP >= 8.0
- Composer
- Node.js y Yarn
- Oracle Database (y extensión PHP OCI8)

## Instalación

1. **Clona el repositorio:**
   ```bash
   git clone https://github.com/romero060523/Biblioteca_Virtual
   ```

2. **Instala las dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Instala las dependencias de Node.js:**
   ```bash
   npm install
   ```

4. **Configura el entorno:**
   - Copia el archivo `.env.example` a `.env` y edítalo con tus credenciales de Oracle y configuración local.
   - Genera la clave de la app:
     ```bash
     php artisan key:generate
     ```

5. **Compila los assets (CSS/JS) con Tailwind:**
   ```bash
   npm run dev
   # o para producción
   npm run build
   ```

6. **Configura la base de datos Oracle:**
   - Ejecuta los scripts en `database/sql/` para crear las tablas y procedimientos necesarios.
   - Asegúrate de tener la extensión OCI8 habilitada en PHP.

7. **Inicia el servidor de desarrollo:**
   ```bash
   php artisan serve
   ```

8. **Accede a la aplicación:**
   - Abre tu navegador en [http://localhost:8000/biblioteca](http://localhost:8000/biblioteca)

## Herramientas y tecnologías usadas

- Laravel
- Oracle Database
- Tailwind CSS
- Composer
- PHP OCI8

## Notas
- Los scripts de la base de datos estan en la carpeta /database/sql , seguir los pasos para ejecutarlo.
- Los reportes rápidos y algunas funciones avanzadas estan deshabilitadas o en desarrollo.
- Para producción, asegúrate de compilar los assets con `npm run build`.

---
