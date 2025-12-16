<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use StaticKidz\BedcaAPI\BedcaClient;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = new BedcaClient();
        $foodGroups = $client->getFoodGroups();

        if (isset($foodGroups->food)) {
            foreach ($foodGroups->food as $group) {
                Categoria::create([
                    'nombre' => $group->fg_ori_name,
                    'codigo' => $group->fg_id,
                ]);
            }
        }
    }
}
