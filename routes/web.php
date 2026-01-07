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

// Rutas de Platos
Route::middleware(['auth'])->group(function () {
    Route::get('/platos', \App\Livewire\Platos\Index::class)->name('platos.index');
    Route::get('/platos/crear', \App\Livewire\Platos\Form::class)->name('platos.create');
    Route::get('/platos/{id}/editar', \App\Livewire\Platos\Form::class)->name('platos.edit');
});

// Rutas de Menús
Route::middleware(['auth'])->group(function () {
    Route::get('/menus', \App\Livewire\Menus\Index::class)->name('menus.index');
    Route::get('/menus/crear', \App\Livewire\Menus\Form::class)->name('menus.create');
    Route::get('/menus/{id}/editar', \App\Livewire\Menus\Form::class)->name('menus.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/objetivos', \App\Livewire\Objetivos\Form::class)->name('objetivos.edit');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/exportar/semana', [\App\Http\Controllers\ExportController::class, 'exportarSemana'])->name('exportar.semana');
});

require __DIR__.'/auth.php';
