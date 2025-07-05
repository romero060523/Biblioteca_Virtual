<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nombre',
        'correo',
        'contraseña',
        'rol'
    ];
    
    protected $hidden = [
        'contraseña',
    ];
    
    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'id_usuario', 'id');
    }
    
    public function prestamosActivos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'id_usuario', 'id')
                    ->where('estado', 'ACTIVO');
    }
    
    public function scopeUsuarios($query)
    {
        return $query->where('rol', 'USUARIO');
    }
    
    public function scopeBibliotecarios($query)
    {
        return $query->where('rol', 'BIBLIOTECARIO');
    }
    
    public function scopePorRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }
    
    public function esBibliotecario(): bool
    {
        return $this->rol === 'BIBLIOTECARIO';
    }
    
    public function esUsuario(): bool
    {
        return $this->rol === 'USUARIO';
    }
    
    public function puedePrestar(): bool
    {
        // Un usuario puede prestar máximo 3 libros activos
        return $this->prestamosActivos()->count() < 3;
    }
    
    public function cantidadPrestamosActivos(): int
    {
        return $this->prestamosActivos()->count();
    }
} 