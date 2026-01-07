<?php

namespace App\Livewire\Menus;

use App\Models\Menu;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{
    public $fecha;
    public $semanaInicio;

    public function mount()
    {
        $this->fecha = now()->format('Y-m-d');
        $this->semanaInicio = now()->startOfWeek()->format('Y-m-d');
    }

    public function semanaAnterior()
    {
        $this->semanaInicio = Carbon::parse($this->semanaInicio)->subWeek()->format('Y-m-d');
    }

    public function semanaSiguiente()
    {
        $this->semanaInicio = Carbon::parse($this->semanaInicio)->addWeek()->format('Y-m-d');
    }

    public function semanaActual()
    {
        $this->semanaInicio = now()->startOfWeek()->format('Y-m-d');
    }

    public function eliminar($id)
    {
        $menu = Menu::findOrFail($id);
        
        if ($menu->user_id !== auth()->id()) {
            session()->flash('error', 'No tienes permiso para eliminar este menú.');
            return;
        }

        $menu->delete();
        session()->flash('success', 'Menú eliminado correctamente.');
    }

    public function render()
    {
        $inicio = Carbon::parse($this->semanaInicio);
        $fin = $inicio->copy()->endOfWeek();

        // Obtener todos los menús de la semana
        $menus = Menu::where('user_id', auth()->id())
            ->whereBetween('fecha', [$inicio->format('Y-m-d'), $fin->format('Y-m-d')])
            ->with('plato')
            ->get()
            ->groupBy(function($menu) {
                return $menu->fecha . '-' . $menu->tipo_comida;
            });

        // Generar estructura de la semana
        $dias = [];
        $tiposComida = ['desayuno', 'almuerzo', 'comida', 'merienda', 'cena'];
        
        for ($i = 0; $i < 7; $i++) {
            $fecha = $inicio->copy()->addDays($i);
            $dias[] = [
                'fecha' => $fecha->format('Y-m-d'),
                'nombre' => ucfirst($fecha->translatedFormat('l')),
                'dia' => $fecha->format('j'),
                'esHoy' => $fecha->isToday(),
            ];
        }

        return view('livewire.menus.index', compact('menus', 'dias', 'tiposComida'))
            ->layout('layouts.app');
    }
}
