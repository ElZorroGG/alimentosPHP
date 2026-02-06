<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrar datos existentes: copiar plato_id a la tabla pivot
        $menus = DB::table('menus')->whereNotNull('plato_id')->get();
        foreach ($menus as $menu) {
            DB::table('menu_plato')->insert([
                'menu_id' => $menu->id,
                'plato_id' => $menu->plato_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Eliminar la columna plato_id de menus
        Schema::table('menus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plato_id');
        });
    }

    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('plato_id')->nullable()->constrained('platos')->onDelete('cascade');
        });

        // Restaurar datos: copiar el primer plato de la pivot de vuelta
        $menuPlatos = DB::table('menu_plato')
            ->select('menu_id', DB::raw('MIN(plato_id) as plato_id'))
            ->groupBy('menu_id')
            ->get();

        foreach ($menuPlatos as $mp) {
            DB::table('menus')->where('id', $mp->menu_id)->update(['plato_id' => $mp->plato_id]);
        }
    }
};
