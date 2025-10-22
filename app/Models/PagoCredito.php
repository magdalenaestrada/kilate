<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoCredito extends Model
{
    use HasFactory;
    protected $table = "pagos_creditos";
    protected $fillable = [
        "codigo",
        "modelo_type",
        "modelo_id",
        "monto_total",
        "monto_restante",
        "fecha_inicio",
        "fecha_final",
        "estado"
    ];
    public function detalle_pago()
    {
        return $this->hasMany(DetallePagoCredito::class, "id");
    }
    public function modelo()
    {
        return $this->morphTo();
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
