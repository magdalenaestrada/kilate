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
        Schema::create('orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->string("codigo");
            $table->unsignedBigInteger('proveedor_id');
            $table->foreign("proveedor_id")->references("id")->on("proveedors")->onDelete("cascade")->onUpdate("cascade");
            $table->date("fecha_creacion");
            $table->date("fecha_inicio");
            $table->date("fecha_fin");
            $table->text('descripcion')->nullable();
            $table->decimal('costo_estimado', 10, 2)->default(0.00);
            $table->decimal('costo_final', 10, 2)->default(0.00);
            $table->enum("estado_servicio", ["P", "E", "F", "C","A"])->default("P");
            $table->enum("estado", ["A", "I"])->default("A");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_servicios');
    }
};
