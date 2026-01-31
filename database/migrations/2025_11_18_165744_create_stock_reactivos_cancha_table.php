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
        Schema::create('stock_reactivos_cancha', function (Blueprint $table) {
            $table->id();
            $table->datetime("fecha_hora");
            $table->foreignId("usuario_id")->constrained("users");
            $table->foreignId('circuito_id')->nullable()->constrained('circuitos');
            $table->foreignId("reactivo_id")->constrained("reactivos")->index();
            $table->decimal("stock");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reactivos_cancha');
    }
};
