@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Estadísticas de Uso (Administrador)</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-600">Productos totales</div>
            <div class="text-3xl font-bold">{{ $stats['productos'] }}</div>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-600">Platos totales</div>
            <div class="text-3xl font-bold">{{ $stats['platos'] }}</div>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-600">Menús totales</div>
            <div class="text-3xl font-bold">{{ $stats['menus'] }}</div>
        </div>
    </div>

</div>
@endsection
