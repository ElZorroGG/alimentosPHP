<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Database\Seeder;
use StaticKidz\BedcaAPI\BedcaClient;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $client = new BedcaClient();
        $categorias = Categoria::all();

        foreach ($categorias as $categoria) {
            $foods = $client->getFoodsInGroup($categoria->codigo);
            
            if (isset($foods->food) && is_array($foods->food)) {
                $foodsLimited = array_slice($foods->food, 0, 10);
                
                foreach ($foodsLimited as $food) {
                    try {
                        $foodDetail = $client->getFood($food->f_id);
                        
                        if (isset($foodDetail->food->foodvalue)) {
                            $foodvalue = $foodDetail->food->foodvalue;
                            
                            Producto::create([
                                'nombre' => $food->f_ori_name ?? $food->f_eng_name,
                                'categoria_id' => $categoria->id,
                                'user_id' => null,
                                'es_personalizado' => false,
                                'calorias' => $this->getComponent($foodvalue, 409),
                                'grasa_total' => $this->getComponent($foodvalue, 410),
                                'grasa_saturada' => $this->getComponent($foodvalue, 299),
                                'grasa_monoinsaturada' => $this->getComponent($foodvalue, 282),
                                'grasa_poliinsaturada' => $this->getComponent($foodvalue, 287),
                                'grasa_trans' => 0,
                                'colesterol' => $this->getComponent($foodvalue, 433),
                                'carbohidratos' => $this->getComponent($foodvalue, 53),
                                'fibra' => $this->getComponent($foodvalue, 307),
                                'proteinas' => $this->getComponent($foodvalue, 416),
                            ]);
                        }
                        
                        usleep(100000);
                    } catch (\Exception $e) {
                        // Silenciar errores
                    }
                }
            }
        }
    }

    private function getComponent($foodvalues, $componentId): float
    {
        foreach ($foodvalues as $value) {
            if (isset($value->c_id) && $value->c_id == $componentId) {
                // Si best_location es un objeto o no es numérico, devolver 0
                if (is_object($value->best_location) || !is_numeric($value->best_location)) {
                    return 0.0;
                }
                return floatval($value->best_location);
            }
        }
        return 0.0;
    }
}
