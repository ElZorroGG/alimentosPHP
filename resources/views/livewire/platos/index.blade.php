<div>
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mis Platos
        </h2>
    @endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Todos mis Platos</h3>
                        <a href="{{ route('platos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Crear Nuevo Plato
                        </a>
                    </div>

                    <div class="mb-4">
                        <input 
                            wire:model.live="search" 
                            type="text" 
                            placeholder="Buscar platos..." 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    @if($platos->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            @if($search)
                                No se encontraron platos con ese nombre.
                            @else
                                No tienes platos creados aún. ¡Crea tu primer plato!
                            @endif
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descripción
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Productos
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Calorías Totales
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($platos as $plato)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ $plato->nombre }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-500">{{ Str::limit($plato->descripcion, 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $plato->productos->count() }} productos</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ number_format($plato->productos->sum(function($producto) {
                                                        return ($producto->calorias / 100) * $producto->pivot->cantidad_gramos;
                                                    }), 2) }} kcal
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button 
                                                    wire:click="toggleFavorito({{ $plato->id }})" 
                                                    class="mr-3 text-xl hover:scale-110 transition-transform">
                                                    @if(auth()->user()->esFavorito('plato', $plato->id))
                                                        ⭐
                                                    @else
                                                        ☆
                                                    @endif
                                                </button>
                                                <a href="{{ route('platos.edit', $plato->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    Editar
                                                </a>
                                                <button 
                                                    wire:click="eliminar({{ $plato->id }})" 
                                                    wire:confirm="¿Estás seguro de que quieres eliminar este plato?"
                                                    class="text-red-600 hover:text-red-900">
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $platos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
