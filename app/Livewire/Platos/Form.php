<?php

namespace App\Livewire\Platos;

use App\Models\Plato;
use App\Models\Producto;
use Livewire\Component;

class Form extends Component
{
    public $platoId;
    public $nombre;
    public $descripcion;
    public $productos = [];
    public $busquedaProducto = '';
    public $productosDisponibles = [];

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad_gramos' => 'required|numeric|min:1',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $plato = Plato::with('productos')->findOrFail($id);
            
            if ($plato->user_id !== auth()->id()) {
                abort(403);
            }

            $this->platoId = $plato->id;
            $this->nombre = $plato->nombre;
            $this->descripcion = $plato->descripcion;
            
            foreach ($plato->productos as $producto) {
                $this->productos[] = [
                    'producto_id' => $producto->id,
                    'cantidad_gramos' => $producto->pivot->cantidad_gramos,
                ];
            }
        }

        if (empty($this->productos)) {
            $this->agregarProducto();
        }
    }

    public function updatedBusquedaProducto()
    {
        if (strlen($this->busquedaProducto) >= 2) {
            $this->productosDisponibles = Producto::where('nombre', 'like', '%' . $this->busquedaProducto . '%')
                ->limit(20)
                ->get();
        } else {
            $this->productosDisponibles = [];
        }
    }

    public function agregarProducto()
    {
        $this->productos[] = [
            'producto_id' => '',
            'cantidad_gramos' => 100,
        ];
    }

    public function quitarProducto($index)
    {
        unset($this->productos[$index]);
        $this->productos = array_values($this->productos);
    }

    public function guardar()
    {
        $this->validate();

        if (empty($this->productos)) {
            session()->flash('error', 'Debes agregar al menos un producto.');
            return;
        }

        $data = [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'user_id' => auth()->id(),
        ];

        if ($this->platoId) {
            $plato = Plato::findOrFail($this->platoId);
            
            if ($plato->user_id !== auth()->id()) {
                abort(403);
            }

            $plato->update($data);
            session()->flash('success', 'Plato actualizado correctamente.');
        } else {
            $plato = Plato::create($data);
            session()->flash('success', 'Plato creado correctamente.');
        }

        // Sincronizar productos con cantidades
        $sync = [];
        foreach ($this->productos as $producto) {
            if (!empty($producto['producto_id'])) {
                $sync[$producto['producto_id']] = ['cantidad_gramos' => $producto['cantidad_gramos']];
            }
        }
        $plato->productos()->sync($sync);

        return redirect()->route('platos.index');
    }

    public function render()
    {
        $todosProductos = Producto::orderBy('nombre')->get();
        
        return view('livewire.platos.form', compact('todosProductos'))
            ->layout('layouts.app');
    }
}
