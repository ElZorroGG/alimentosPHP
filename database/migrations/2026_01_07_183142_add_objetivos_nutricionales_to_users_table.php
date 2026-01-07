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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('objetivo_calorias', 8, 2)->nullable();
            $table->decimal('objetivo_proteinas', 8, 2)->nullable();
            $table->decimal('objetivo_carbohidratos', 8, 2)->nullable();
            $table->decimal('objetivo_grasas', 8, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['objetivo_calorias', 'objetivo_proteinas', 'objetivo_carbohidratos', 'objetivo_grasas']);
        });
    }
};
