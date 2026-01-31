<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circuito extends Model
{
    use HasFactory;
    protected $table = "circuitos";
    protected $fillable = [
        "descripcion"
    ];
    public function procesos()
    {
        return $this->hasMany(Proceso::class, 'circuito_id');
    }
}
