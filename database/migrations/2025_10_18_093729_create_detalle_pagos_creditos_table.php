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
        Schema::create('detalle_pagos_creditos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credito_id');
            $table->foreign("credito_id")->references("id")->on("pagos_creditos")->onDelete("cascade")->onUpdate("cascade");
            $table->date("fecha_pago");
            $table->date("fecha_cancelado");
            $table->decimal("monto_pagar");
            $table->enum("estado", ["P", "E", "F", "C","A"])->default("P");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pagos_credito');
    }
};
