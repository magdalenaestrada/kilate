<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumoReactivo extends Model
{
    protected $table = 'consumo_reactivos';
    protected $fillable = ['cantidad', 'proceso_id', 'reactivo_id', 'fecha', 'usuario_id'];

    public function reactivo()
    {
        return $this->belongsTo(Reactivo::class, 'reactivo_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function proceso(){
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
}
