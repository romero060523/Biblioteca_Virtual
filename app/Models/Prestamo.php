<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestamo extends Model
{
    protected $table = 'prestamos';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_usuario',
        'id_libro',
        'fecha_prestamo',
        'fecha_devolucion',
        'estado'
    ];
    
    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_devolucion' => 'date',
    ];
    
    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class, 'id_libro', 'id');
    }
    
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }
    
    public function scopeActivos($query)
    {
        return $query->where('estado', 'ACTIVO');
    }
    
    public function scopeDevueltos($query)
    {
        return $query->where('estado', 'DEVUELTO');
    }
    
    public function scopeVencidos($query)
    {
        return $query->where('estado', 'ACTIVO')
                    ->where('fecha_devolucion', '<', now()->toDateString());
    }
    
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('id_usuario', $usuarioId);
    }
    
    public function scopePorLibro($query, $libroId)
    {
        return $query->where('id_libro', $libroId);
    }
    
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_prestamo', [$fechaInicio, $fechaFin]);
    }
    
    public function estaVencido(): bool
    {
        return $this->estado === 'ACTIVO' && 
               $this->fecha_devolucion < now()->toDateString();
    }
    
    public function diasVencido(): int
    {
        if (!$this->estaVencido()) {
            return 0;
        }
        
        return now()->diffInDays($this->fecha_devolucion);
    }
    
    public function marcarComoDevuelto(): bool
    {
        $this->estado = 'DEVUELTO';
        return $this->save();
    }
    
    public function marcarComoVencido(): bool
    {
        $this->estado = 'VENCIDO';
        return $this->save();
    }
} 