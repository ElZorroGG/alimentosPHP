<?php

namespace App\Livewire\Platos;

use App\Models\Plato;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function eliminar($id)
    {
        $plato = Plato::findOrFail($id);
        
        if ($plato->user_id !== auth()->id()) {
            session()->flash('error', 'No tienes permiso para eliminar este plato.');
            return;
        }

        $plato->delete();
        session()->flash('success', 'Plato eliminado correctamente.');
    }

    public function toggleFavorito($id)
    {
        $favorito = auth()->user()->favoritos()
            ->where('tipo_favorito', 'plato')
            ->where('favorito_id', $id)
            ->first();

        if ($favorito) {
            $favorito->delete();
        } else {
            auth()->user()->favoritos()->create([
                'tipo_favorito' => 'plato',
                'favorito_id' => $id,
            ]);
        }
    }

    public function render()
    {
        $favoritosIds = auth()->user()->favoritos()
            ->where('tipo_favorito', 'plato')
            ->pluck('favorito_id')
            ->toArray();

        $platos = Plato::where('user_id', auth()->id())
            ->when($this->search, function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->orderByRaw('CASE WHEN id IN (' . (count($favoritosIds) ? implode(',', $favoritosIds) : '0') . ') THEN 0 ELSE 1 END')
            ->latest('updated_at')
            ->paginate(15);

        return view('livewire.platos.index', compact('platos'))
            ->layout('layouts.app');
    }
}
