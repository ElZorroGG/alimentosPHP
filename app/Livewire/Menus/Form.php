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
    public $plato_ids = [];
    public $busquedaPlato = '';

    protected $rules = [
        'fecha' => 'required|date',
        'tipo_comida' => 'required|in:desayuno,almuerzo,comida,merienda,cena',
        'plato_ids' => 'required|array|min:1',
        'plato_ids.*' => 'exists:platos,id',
    ];

    protected $messages = [
        'plato_ids.required' => 'Debes seleccionar al menos un plato.',
        'plato_ids.min' => 'Debes seleccionar al menos un plato.',
    ];

    public function togglePlato($id)
    {
        $id = (int) $id;
        if (in_array($id, $this->plato_ids)) {
            $this->plato_ids = array_values(array_filter($this->plato_ids, fn($v) => $v !== $id));
        } else {
            $this->plato_ids[] = $id;
        }
    }

    public function mount($id = null)
    {
        if ($id) {
            $menu = Menu::with('platos')->findOrFail($id);
            
            if ($menu->user_id !== auth()->id()) {
                abort(403);
            }

            $this->menuId = $menu->id;
            $this->fecha = $menu->fecha;
            $this->tipo_comida = $menu->tipo_comida;
            $this->plato_ids = $menu->platos->pluck('id')->toArray();
        } else {
            // Pre-cargar desde parámetros de URL si existen
            $this->fecha = request('fecha', now()->format('Y-m-d'));
            $this->tipo_comida = request('tipo_comida', 'comida');
        }
    }

    public function guardar()
    {
        $this->validate();

        // Verificar que todos los platos pertenezcan al usuario
        $platos = Plato::whereIn('id', $this->plato_ids)->get();

        if ($platos->count() !== count($this->plato_ids)) {
            session()->flash('error', 'Uno o más platos seleccionados no existen.');
            return;
        }

        foreach ($platos as $plato) {
            if ($plato->user_id !== auth()->id()) {
                session()->flash('error', 'No puedes usar un plato que no te pertenece.');
                return;
            }
        }

        $data = [
            'fecha' => $this->fecha,
            'tipo_comida' => $this->tipo_comida,
            'user_id' => auth()->id(),
        ];

        if ($this->menuId) {
            $menu = Menu::findOrFail($this->menuId);
            
            if ($menu->user_id !== auth()->id()) {
                abort(403);
            }

            $menu->update($data);
            $menu->platos()->sync($this->plato_ids);
            session()->flash('success', 'Menú actualizado correctamente.');
        } else {
            // Verificar si ya existe un menú para esa fecha y tipo de comida
            $existente = Menu::where('user_id', auth()->id())
                ->where('fecha', $this->fecha)
                ->where('tipo_comida', $this->tipo_comida)
                ->first();

            if ($existente) {
                session()->flash('error', 'Ya tienes un menú asignado para esta comida. Edita el existente.');
                return;
            }

            $menu = Menu::create($data);
            $menu->platos()->attach($this->plato_ids);
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
