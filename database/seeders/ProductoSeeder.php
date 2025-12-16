<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Database\Seeder;
use StaticKidz\BedcaAPI\BedcaClient;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = new BedcaClient();
        $categorias = Categoria::all();

        // Por cada categoría, obtenemos algunos alimentos
        foreach ($categorias as $categoria) {
            try {
                $foods = $client->getFoodsInGroup($categoria->codigo);
                
                if (isset($foods->food) && is_array($foods->food)) {
                    // Limitamos a 20 productos por categoría para no saturar
                    $foodsLimited = array_slice($foods->food, 0, 20);
                    
                    foreach ($foodsLimited as $food) {
                        // Obtenemos los detalles completos del alimento
                        $foodDetail = $client->getFood($food->f_id);
                        
                        if (isset($foodDetail->foodvalue)) {
                            Producto::create([
                                'nombre' => $food->f_ori_name ?? $food->f_eng_name,
                                'categoria_id' => $categoria->id,
                                'user_id' => null, // Producto predefinido
                                'es_personalizado' => false,
                                'calorias' => $this->getComponent($foodDetail->foodvalue, 'ENERC_'),
                                'grasa_total' => $this->getComponent($foodDetail->foodvalue, 'FAT'),
                                'grasa_saturada' => $this->getComponent($foodDetail->foodvalue, 'FASAT'),
                                'grasa_monoinsaturada' => $this->getComponent($foodDetail->foodvalue, 'FAMS'),
                                'grasa_poliinsaturada' => $this->getComponent($foodDetail->foodvalue, 'FAPU'),
                                'grasa_trans' => $this->getComponent($foodDetail->foodvalue, 'FATRN'),
                                'colesterol' => $this->getComponent($foodDetail->foodvalue, 'CHOLE'),
                                'carbohidratos' => $this->getComponent($foodDetail->foodvalue, 'CHOAVL'),
                                'fibra' => $this->getComponent($foodDetail->foodvalue, 'FIBTG'),
                                'proteinas' => $this->getComponent($foodDetail->foodvalue, 'PROT'),
                            ]);
                        }
                        
                        // Pequeña pausa para no saturar la API
                        usleep(100000); // 0.1 segundos
                    }
                }
            } catch (\Exception $e) {
                $this->command->warn("Error al cargar productos de categoría {$categoria->nombre}: " . $e->getMessage());
            }
        }
    }

    /**
     * Obtiene el valor de un componente nutricional
     */
    private function getComponent($foodvalues, $componentCode): float
    {
        foreach ($foodvalues as $value) {
            if (isset($value->c_id) && str_starts_with($value->c_id, $componentCode)) {
                return floatval($value->best_location ?? 0);
            }
        }
        return 0.0;
    }
}
