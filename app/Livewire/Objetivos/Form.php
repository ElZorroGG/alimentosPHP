<?php

namespace App\Livewire\Objetivos;

use Livewire\Component;

class Form extends Component
{
    public $objetivo_calorias;
    public $objetivo_proteinas;
    public $objetivo_carbohidratos;
    public $objetivo_grasas;

    protected $rules = [
        'objetivo_calorias' => 'nullable|numeric|min:0',
        'objetivo_proteinas' => 'nullable|numeric|min:0',
        'objetivo_carbohidratos' => 'nullable|numeric|min:0',
        'objetivo_grasas' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $user = auth()->user();
        $this->objetivo_calorias = $user->objetivo_calorias;
        $this->objetivo_proteinas = $user->objetivo_proteinas;
        $this->objetivo_carbohidratos = $user->objetivo_carbohidratos;
        $this->objetivo_grasas = $user->objetivo_grasas;
    }

    public function guardar()
    {
        $this->validate();

        auth()->user()->update([
            'objetivo_calorias' => $this->objetivo_calorias,
            'objetivo_proteinas' => $this->objetivo_proteinas,
            'objetivo_carbohidratos' => $this->objetivo_carbohidratos,
            'objetivo_grasas' => $this->objetivo_grasas,
        ]);

        session()->flash('success', 'Objetivos actualizados correctamente.');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.objetivos.form')
            ->layout('layouts.app');
    }
}
