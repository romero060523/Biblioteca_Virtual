<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Libro extends Model
{
    protected $table = 'libros';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'titulo',
        'autor',
        'categoria',
        'stock'
    ];
    
    protected $casts = [
        'stock' => 'integer',
    ];
    
    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'id_libro', 'id');
    }
    
    public function prestamosActivos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'id_libro', 'id')
                    ->where('estado', 'ACTIVO');
    }
    
    public function scopeDisponible($query)
    {
        return $query->where('stock', '>', 0);
    }
    
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }
    
    public function scopePorAutor($query, $autor)
    {
        return $query->where('autor', 'like', "%{$autor}%");
    }
    
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('titulo', 'like', "%{$termino}%")
              ->orWhere('autor', 'like', "%{$termino}%")
              ->orWhere('categoria', 'like', "%{$termino}%");
        });
    }
    
    public function estaDisponible(): bool
    {
        return $this->stock > 0;
    }
    
    public function reducirStock(int $cantidad = 1): bool
    {
        if ($this->stock >= $cantidad) {
            $this->stock -= $cantidad;
            return $this->save();
        }
        return false;
    }
    
    public function aumentarStock(int $cantidad = 1): bool
    {
        $this->stock += $cantidad;
        return $this->save();
    }
} 