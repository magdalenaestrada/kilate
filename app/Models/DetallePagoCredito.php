<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePagoCredito extends Model
{
    use HasFactory;
    protected $table = "detalle_pagos_creditos";
    protected $fillable = [
        "credito_id",
        "fecha_pago",
        "fecha_cancelado",
        "monto_pagar",
        "estado",
    ];

    public function credito(){
        return $this->hasMany(OrdenServicio::class, "id");
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
