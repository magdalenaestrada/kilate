<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('liquidaciones', function (Blueprint $table) {
            $table->id();
            $table->decimal('suma_proceso', 10, 2);
            $table->decimal('suma_exceso_reactivos', 10, 2);
            $table->decimal('suma_balanza', 10, 2);
            $table->decimal('suma_comedor', 10, 2);
            $table->decimal('suma_laboratorio', 10, 2);
            $table->decimal('suma_prueba_metalurgica', 10, 2);
            $table->decimal('suma_descoche', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('igv', 10, 2);
            $table->decimal('total', 10, 2);
            $table->date('fecha');
            $table->foreignId('cliente_id')->constrained('lq_clientes');
            $table->foreignId('proceso_id')->constrained('procesos');
            $table->decimal('precio_unitario_proceso', 10, 2);
            $table->decimal('cantidad_procesada_tn', 10, 3);
            $table->decimal('precio_unitario_laboratorio', 10, 2);
            $table->integer('cantidad_muestras');
            $table->decimal('precio_unitario_balanza', 10, 2);
            $table->integer('cantidad_pesajes');
            $table->decimal('precio_prueba_metalurgica', 10, 2);
            $table->integer('cantidad_pruebas_metalurgicas');
            $table->decimal('precio_descoche', 10, 2);
            $table->integer('cantidad_descoche');
            $table->decimal('precio_unitario_comida', 10, 2);
            $table->integer('cantidad_comidas');
            $table->decimal('gastos_adicionales', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidaciones');
    }
};
