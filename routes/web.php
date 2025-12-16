<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Rutas de Productos
Route::middleware(['auth'])->group(function () {
    Route::get('/productos', \App\Livewire\Productos\Index::class)->name('productos.index');
    Route::get('/productos/crear', \App\Livewire\Productos\Form::class)->name('productos.create');
    Route::get('/productos/{id}/editar', \App\Livewire\Productos\Form::class)->name('productos.edit');
});

require __DIR__.'/auth.php';
