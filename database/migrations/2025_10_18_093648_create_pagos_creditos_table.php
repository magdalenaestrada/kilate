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
        Schema::create('pagos_creditos', function (Blueprint $table) {
            $table->id();
            $table->string("codigo");
            $table->string("modelo_type")->nullable();
            $table->unsignedBigInteger("modelo_id")->nullable();
            $table->unsignedInteger("monto_total");
            $table->unsignedInteger("monto_restante");
            $table->date("fecha_inicio");
            $table->date("fecha_final");
            $table->enum("tiempo", ["S", "M", "Q"]);
            $table->enum("estado", ["P", "E", "F", "C","A"])->default("P");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_credito');
    }
};
