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
        Schema::table('pesos_otras_bal', function (Blueprint $table) {
            $table->unsignedBigInteger('proceso_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesos_otras_bals', function (Blueprint $table) {
            $table->unsignedBigInteger('proceso_id')->nullable(false)->change();
        });
    }
};
