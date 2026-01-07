<?php

namespace App\Livewire\Menus;

use App\Models\Menu;
use App\Models\Plato;
use Livewire\Component;
use Illuminate\Support\Facades\Request;

class Form extends Component
{
    public $menuId;
    public $fecha;
    public $tipo_comida;
    public $plato_id;

    protected $rules = [
        'fecha' => 'required|date',
        'tipo_comida' => 'required|in:desayuno,almuerzo,comida,merienda,cena',
        'plato_id' => 'required|exists:platos,id',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $menu = Menu::findOrFail($id);
            
            if ($menu->user_id !== auth()->id()) {
                abort(403);
            }

            $this->menuId = $menu->id;
            $this->fecha = $menu->fecha;
            $this->tipo_comida = $menu->tipo_comida;
            $this->plato_id = $menu->plato_id;
        } else {
            // Pre-cargar desde parámetros de URL si existen
            $this->fecha = request('fecha', now()->format('Y-m-d'));
            $this->tipo_comida = request('tipo_comida', 'comida');
        }
    }

    public function guardar()
    {
        $this->validate();

        // Verificar que el plato pertenezca al usuario
        $plato = Plato::findOrFail($this->plato_id);
        if ($plato->user_id !== auth()->id()) {
            session()->flash('error', 'No puedes usar un plato que no te pertenece.');
            return;
        }

        $data = [
            'fecha' => $this->fecha,
            'tipo_comida' => $this->tipo_comida,
            'plato_id' => $this->plato_id,
            'user_id' => auth()->id(),
        ];

        if ($this->menuId) {
            $menu = Menu::findOrFail($this->menuId);
            
            if ($menu->user_id !== auth()->id()) {
                abort(403);
            }

            $menu->update($data);
            session()->flash('success', 'Menú actualizado correctamente.');
        } else {
            // Verificar si ya existe un menú para esa fecha y tipo de comida
            $existente = Menu::where('user_id', auth()->id())
                ->where('fecha', $this->fecha)
                ->where('tipo_comida', $this->tipo_comida)
                ->first();

            if ($existente) {
                session()->flash('error', 'Ya tienes un plato asignado para esta comida. Edita el existente.');
                return;
            }

            Menu::create($data);
            session()->flash('success', 'Menú creado correctamente.');
        }

        return redirect()->route('menus.index');
    }

    public function render()
    {
        $platos = Plato::where('user_id', auth()->id())
            ->orderBy('nombre')
            ->get();

        return view('livewire.menus.form', compact('platos'))
            ->layout('layouts.app');
    }
}
