<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportarSemana(Request $request)
    {
        $fecha = $request->get('fecha', now()->format('Y-m-d'));
        $inicio = Carbon::parse($fecha)->startOfWeek();
        $fin = $inicio->copy()->endOfWeek();

        $menus = Menu::where('user_id', auth()->id())
            ->whereBetween('fecha', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->with('plato.productos')
            ->orderBy('fecha')
            ->orderByRaw("FIELD(tipo_comida, 'desayuno', 'almuerzo', 'comida', 'merienda', 'cena')")
            ->get();

        $menusAgrupados = $menus->groupBy(function($menu) {
            return Carbon::parse($menu->fecha)->format('Y-m-d');
        });

        $totalesSemana = [
            'calorias' => 0,
            'proteinas' => 0,
            'carbohidratos' => 0,
            'grasas' => 0,
        ];

        $dias = [];
        for ($i = 0; $i < 7; $i++) {
            $diaActual = $inicio->copy()->addDays($i);
            $fechaFormato = $diaActual->format('Y-m-d');
            
            $totalesDia = [
                'calorias' => 0,
                'proteinas' => 0,
                'carbohidratos' => 0,
                'grasas' => 0,
            ];

            if ($menusAgrupados->has($fechaFormato)) {
                foreach ($menusAgrupados[$fechaFormato] as $menu) {
                    foreach ($menu->plato->productos as $producto) {
                        $factor = $producto->pivot->cantidad_gramos / 100;
                        $totalesDia['calorias'] += $producto->calorias * $factor;
                        $totalesDia['proteinas'] += $producto->proteinas * $factor;
                        $totalesDia['carbohidratos'] += $producto->carbohidratos * $factor;
                        $totalesDia['grasas'] += $producto->grasa_total * $factor;
                    }
                }
            }

            $totalesSemana['calorias'] += $totalesDia['calorias'];
            $totalesSemana['proteinas'] += $totalesDia['proteinas'];
            $totalesSemana['carbohidratos'] += $totalesDia['carbohidratos'];
            $totalesSemana['grasas'] += $totalesDia['grasas'];

            $dias[] = [
                'fecha' => $diaActual,
                'menus' => $menusAgrupados->get($fechaFormato, collect()),
                'totales' => $totalesDia,
            ];
        }

        $pdf = Pdf::loadView('exports.semana-pdf', [
            'inicio' => $inicio,
            'fin' => $fin,
            'dias' => $dias,
            'totalesSemana' => $totalesSemana,
            'user' => auth()->user(),
        ]);

        return $pdf->download('menu-semanal-' . $inicio->format('Y-m-d') . '.pdf');
    }
}
