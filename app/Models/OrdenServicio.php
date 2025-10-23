<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    use HasFactory;
    protected $table = "orden_servicios";
    protected $fillable = [
        "codigo",
        "proveedor_id",
        "fecha_creacion",
        "fecha_inicio",
        "fecha_fin",
        "descripcion",
        "costo_estimado",
        "costo_final",
        "observaciones",
        "estado_servicio",
        "estado",
    ];
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, "proveedor_id");
    }
    public function detalles()
    {
        return $this->hasMany(DetalleOrdenServicio::class, "orden_servicio_id");
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function comprobantes()
    {
        return $this->morphMany(Comprobante::class, 'modelo');
    }


    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_creacion' => 'datetime',
    ];
}
