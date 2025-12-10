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
        Schema::create('molienda', function (Blueprint $table) {
            $table->id();
            $table->date("fecha_liquidacion");
            $table->foreignId("proceso_id")->constrained("procesos")->onDelete('cascade');
            $table->foreignId("cliente_id")->constrained("lq_clientes")->onDelete('cascade');
            $table->foreignId("user_id")->constrained("users")->onDelete('cascade');
            $table->decimal("cantidad_procesada_tn", 10, 2);
            $table->decimal("suma_proceso", 10, 2);
            $table->decimal("suma_balanza", 10, 2);
            $table->decimal("suma_comedor", 10, 2);
            $table->decimal("suma_prueba_metalurgica", 10, 2);
            $table->decimal("subtotal", 10, 2);
            $table->decimal("igv", 10, 2);
            $table->decimal("total", 10, 2);
            $table->decimal("gastos_adicionales", 10, 2);
            $table->decimal("precio_unitario_proceso", 10, 2);
            $table->decimal("precio_prueba_metalurgica", 10, 2);
            $table->integer("cantidad_pruebas_metalurgicas");
            $table->decimal("precio_unitario_comida", 10, 2);
            $table->integer("cantidad_comidas");
            $table->decimal("precio_unitario_balanza", 10, 2);
            $table->integer("cantidad_pesajes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('molienda');
    }
};
