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
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('tipo_pago', 50)->nullable();
            $table->foreignId('usuario_cancelacion');
            $table->morphs('modelo');
            $table->date('fecha_cancelacion')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->string('comprobante_correlativo', 13)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
