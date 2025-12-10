<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionReactivo extends Model
{
    protected $table = 'devolucion_reactivos';
    protected $fillable = ['cantidad', 'proceso_id', 'reactivo_id', 'fecha', 'usuario_id'];


    public function reactivo()
    {
        return $this->belongsTo(Reactivo::class, 'reactivo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
