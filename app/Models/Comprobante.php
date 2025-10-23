<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = "comprobantes";

    protected $fillable = [
        "total",
        "tipo_pago",
        "usuario_cancelacion",
        "modelo_type",
        "modelo_id",
        "fecha_cancelacion",
        "fecha_emision",
        "comprobante_correlativo",
    ];
    public function modelo()
    {
        return $this->morphTo();
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_cancelacion');
    }
}
