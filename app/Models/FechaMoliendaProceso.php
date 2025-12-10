<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FechaMoliendaProceso extends Model
{
    protected $table = 'fechas_molienda_proceso';

    protected $fillable = [
        'proceso_id',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'tonelaje'
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
}
