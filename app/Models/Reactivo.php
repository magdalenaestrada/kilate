<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reactivo extends Model
{
    protected $table = 'reactivos';

    protected $fillable = ['producto_id'];

    public function detalles(){
        return $this->hasOne(ReactivoDetalle::class);
        
    }
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id');
    }

}
