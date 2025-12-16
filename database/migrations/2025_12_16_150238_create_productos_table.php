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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // NULL = predefinido
            $table->boolean('es_personalizado')->default(false);
            
            // Macronutrientes por 100g
            $table->decimal('calorias', 8, 2)->default(0);
            $table->decimal('grasa_total', 8, 2)->default(0);
            $table->decimal('grasa_saturada', 8, 2)->default(0);
            $table->decimal('grasa_monoinsaturada', 8, 2)->default(0);
            $table->decimal('grasa_poliinsaturada', 8, 2)->default(0);
            $table->decimal('grasa_trans', 8, 2)->default(0);
            $table->decimal('colesterol', 8, 2)->default(0);
            $table->decimal('carbohidratos', 8, 2)->default(0);
            $table->decimal('fibra', 8, 2)->default(0);
            $table->decimal('proteinas', 8, 2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
