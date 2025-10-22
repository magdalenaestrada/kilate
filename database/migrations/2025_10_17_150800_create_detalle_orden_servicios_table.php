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
        Schema::create('detalle_orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_servicio_id');
            $table->foreign("orden_servicio_id")->references("id")->on("orden_servicios")->onDelete("cascade")->onUpdate("cascade");
            $table->string("descripcion");
            $table->unsignedInteger("cantidad")->default(1);
            $table->unsignedInteger("precio_unitario");
            $table->unsignedInteger("subtotal");
            $table->enum("estado", ["A", "I"])->default("A");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_orden_servicios');
    }
};
