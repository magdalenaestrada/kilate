<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proceso extends Model
{
    protected $table = 'procesos';
    protected $fillable = ['id', 'lote_id', 'peso_total', 'circuito_id', 'codigo', 'estado', 'molienda'];


    public function pesos()
    {
        return $this->hasManyThrough(
            Peso::class,
            ProcesoPeso::class,
            'proceso_id',
            'NroSalida',
            'id',
            'peso_id'
        );
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function circuito()
    {
        return $this->belongsTo(Circuito::class, 'circuito_id');
    }
    public function programacion()
    {
        return $this->hasOne(PlProgramacion::class, 'proceso_id');
    }


    public function consumosreactivos()
    {
        return $this->hasMany(ConsumoReactivo::class);
    }

    public function devolucionesReactivos()
    {
        return $this->hasMany(DevolucionReactivo::class);
    }

    public function liquidacion()
    {
        return $this->hasOne(Liquidacion::class, 'proceso_id');
    }

    public function molienda()
    {
        return $this->hasOne(Molienda::class, 'proceso_id');
    }

    public function pesosOtrasBal()
    {
        return $this->hasMany(PesoOtraBal::class, 'proceso_id');
    }

    public function todosLosPesos()
    {
        return collect($this->pesos)->merge($this->pesosOtrasBal);
    }

    public function tiempos()
    {
        return $this->hasMany(FechaMoliendaProceso::class, 'proceso_id');
    }
}
