<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Molienda extends Model
{
    protected $table = "molienda";
    protected $fillable = [
        'suma_proceso',
        'suma_balanza',
        'suma_comedor',
        'suma_prueba_metalurgica',
        'subtotal',
        'igv',
        'total',
        'fecha_liquidacion',
        'cliente_id',
        'user_id',
        'proceso_id',
        'precio_unitario_proceso',
        'cantidad_procesada_tn',
        'precio_unitario_balanza',
        'cantidad_pesajes',
        'precio_prueba_metalurgica',
        'cantidad_pruebas_metalurgicas',
        'precio_unitario_comida',
        'cantidad_comidas',
        'gastos_adicionales'
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
    public function cliente()
    {
        return $this->belongsTo(LqCliente::class, 'cliente_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
