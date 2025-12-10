<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReactivo extends Model
{
    protected $table = "stock_reactivos_cancha";
    protected $fillable = [
        "fecha_hora",
        "usuario_id",
        "circuito",
        "reactivo_id",
        "stock"
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, "usuario_id");
    }
    public function reactivo()
    {
        return $this->belongsTo(Reactivo::class, "reactivo_id");
    }
}
