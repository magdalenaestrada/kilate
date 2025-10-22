<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrdenServicio extends Model
{
    use HasFactory;

    protected  $table = "detalle_orden_servicios";
    
    protected $fillable = [
        "orden_servicio_id",
        "descripcion",
        "cantidad",
        "precio_unitario",
        "subtotal",
    ];

    public function orden_servicio(){
        return $this->belongsTo(OrdenServicio::class, "orden_servicio_id");
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
