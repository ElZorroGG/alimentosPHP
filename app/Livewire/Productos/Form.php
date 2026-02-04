<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use App\Models\Categoria;
use Livewire\Component;

class Form extends Component
{
    public $productoId;
    public $nombre;
    public $categoria_id;
    public $calorias = 0;
    public $grasa_total = 0;
    public $grasa_saturada = 0;
    public $grasa_monoinsaturada = 0;
    public $grasa_poliinsaturada = 0;
    public $grasa_trans = 0;
    public $colesterol = 0;
    public $carbohidratos = 0;
    public $fibra = 0;
    public $proteinas = 0;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'categoria_id' => 'required|exists:categorias,id',
        'calorias' => 'required|numeric|min:0',
        'grasa_total' => 'required|numeric|min:0',
        'grasa_saturada' => 'required|numeric|min:0',
        'grasa_monoinsaturada' => 'required|numeric|min:0',
        'grasa_poliinsaturada' => 'required|numeric|min:0',
        'grasa_trans' => 'required|numeric|min:0',
        'colesterol' => 'required|numeric|min:0',
        'carbohidratos' => 'required|numeric|min:0',
        'fibra' => 'required|numeric|min:0',
        'proteinas' => 'required|numeric|min:0',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $producto = Producto::findOrFail($id);
            $user = auth()->user();
            
            if (!$user->can('productos.update')) {
                abort(403, 'No tienes permiso para editar productos.');
            }
            
            $esProductoAPI = is_null($producto->user_id);
            $esPropietario = $producto->user_id === $user->id;
            
            if (!$user->hasRole('admin') && !$esProductoAPI && !$esPropietario) {
                abort(403, 'No tienes permiso para editar este producto.');
            }
            
            $this->productoId = $producto->id;
            $this->nombre = $producto->nombre;
            $this->categoria_id = $producto->categoria_id;
            $this->calorias = $producto->calorias;
            $this->grasa_total = $producto->grasa_total;
            $this->grasa_saturada = $producto->grasa_saturada;
            $this->grasa_monoinsaturada = $producto->grasa_monoinsaturada;
            $this->grasa_poliinsaturada = $producto->grasa_poliinsaturada;
            $this->grasa_trans = $producto->grasa_trans;
            $this->colesterol = $producto->colesterol;
            $this->carbohidratos = $producto->carbohidratos;
            $this->fibra = $producto->fibra;
            $this->proteinas = $producto->proteinas;
        }
    }

    public function guardar()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'categoria_id' => $this->categoria_id,
            'user_id' => auth()->id(),
            'es_personalizado' => true,
            'calorias' => $this->calorias,
            'grasa_total' => $this->grasa_total,
            'grasa_saturada' => $this->grasa_saturada,
            'grasa_monoinsaturada' => $this->grasa_monoinsaturada,
            'grasa_poliinsaturada' => $this->grasa_poliinsaturada,
            'grasa_trans' => $this->grasa_trans,
            'colesterol' => $this->colesterol,
            'carbohidratos' => $this->carbohidratos,
            'fibra' => $this->fibra,
            'proteinas' => $this->proteinas,
        ];

        if ($this->productoId) {
            $producto = Producto::findOrFail($this->productoId);
            $producto->update($data);
            session()->flash('success', 'Producto actualizado correctamente.');
        } else {
            Producto::create($data);
            session()->flash('success', 'Producto creado correctamente.');
        }

        return redirect()->route('productos.index');
    }

    public function render()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('livewire.productos.form', compact('categorias'))
            ->layout('layouts.app');
    }
}
