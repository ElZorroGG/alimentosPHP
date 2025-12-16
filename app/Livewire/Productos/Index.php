<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use App\Models\Categoria;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $categoriaFiltro = '';
    public $soloMios = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function eliminar($id)
    {
        $producto = Producto::findOrFail($id);
        
        if ($producto->user_id !== auth()->id()) {
            session()->flash('error', 'No tienes permiso para eliminar este producto.');
            return;
        }
        
        $producto->delete();
        session()->flash('success', 'Producto eliminado correctamente.');
    }

    public function render()
    {
        $query = Producto::with('categoria');

        if ($this->soloMios) {
            $query->where('user_id', auth()->id());
        }

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        if ($this->categoriaFiltro) {
            $query->where('categoria_id', $this->categoriaFiltro);
        }

        $productos = $query->latest()->paginate(15);
        $categorias = Categoria::orderBy('nombre')->get();

        return view('livewire.productos.index', compact('productos', 'categorias'))
            ->layout('layouts.app');
    }
}
