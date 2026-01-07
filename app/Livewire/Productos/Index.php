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

    public function toggleFavorito($id)
    {
        $favorito = auth()->user()->favoritos()
            ->where('tipo_favorito', 'producto')
            ->where('favorito_id', $id)
            ->first();

        if ($favorito) {
            $favorito->delete();
        } else {
            auth()->user()->favoritos()->create([
                'tipo_favorito' => 'producto',
                'favorito_id' => $id,
            ]);
        }
    }

    public function render()
    {
        $favoritosIds = auth()->user()->favoritos()
            ->where('tipo_favorito', 'producto')
            ->pluck('favorito_id')
            ->toArray();

        $query = Producto::with('categoria')
            ->orderByRaw('CASE WHEN id IN (' . (count($favoritosIds) ? implode(',', $favoritosIds) : '0') . ') THEN 0 ELSE 1 END');

        if ($this->soloMios) {
            $query->where('user_id', auth()->id());
        }

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        if ($this->categoriaFiltro) {
            $query->where('categoria_id', $this->categoriaFiltro);
        }

        $productos = $query->latest('updated_at')->paginate(15);
        $categorias = Categoria::orderBy('nombre')->get();

        return view('livewire.productos.index', compact('productos', 'categorias'))
            ->layout('layouts.app');
    }
}
