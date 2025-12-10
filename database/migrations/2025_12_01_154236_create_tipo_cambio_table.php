<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_cambio', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 10, 4); // Ej: 3.3000
            $table->unsignedBigInteger('usuario_id')->nullable(); // quien registró
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_cambio');
    }
};
