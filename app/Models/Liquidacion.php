<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liquidacion extends Model
{
    protected $table = 'liquidaciones';

    protected $fillable = [
        'suma_proceso',
        'suma_exceso_reactivos',
        'suma_balanza',
        'suma_comedor',
        'suma_laboratorio',
        'suma_prueba_metalurgica',
        'suma_descoche',
        'subtotal',
        'igv',
        'total',
        'fecha',
        'cliente_id',
        'user_id',
        'proceso_id',
        'precio_unitario_proceso',
        'cantidad_procesada_tn',
        'precio_unitario_laboratorio',
        'cantidad_muestras',
        'precio_unitario_balanza',
        'cantidad_pesajes',
        'precio_prueba_metalurgica',
        'cantidad_pruebas_metalurgicas',
        'precio_descoche',
        'cantidad_descoche',
        'precio_unitario_comida',
        'cantidad_comidas',
        'gastos_adicionales',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(LqCliente::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
