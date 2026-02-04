<div>
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Productos e Ingredientes
        </h2>
    @endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Todos los Productos</h3>
                        <a href="{{ route('productos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Agregar Producto Personal
                        </a>
                    </div>

                    @if (session()->has('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-4 flex gap-4">
                        <input wire:model.live="search" type="text" placeholder="Buscar producto..." 
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        
                        <select wire:model.live="categoriaFiltro" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>

                        <label class="flex items-center">
                            <input wire:model.live="soloMios" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Solo mis productos</span>
                        </label>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calorías</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proteínas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carbs</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grasas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($productos as $producto)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $producto->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $producto->categoria->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($producto->calorias, 1) }} kcal</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($producto->proteinas, 1) }}g</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($producto->carbohidratos, 1) }}g</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($producto->grasa_total, 1) }}g</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($producto->es_personalizado)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Personal
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    BEDCA
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button 
                                                wire:click="toggleFavorito({{ $producto->id }})" 
                                                class="mr-3 text-xl hover:scale-110 transition-transform">
                                                @if(auth()->user()->esFavorito('producto', $producto->id))
                                                    ⭐
                                                @else
                                                    ☆
                                                @endif
                                            </button>
                                            @if(auth()->user()->hasRole('admin') || $producto->es_personalizado && $producto->user_id === auth()->id() || is_null($producto->user_id))
                                                <a href="{{ route('productos.edit', $producto) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                                @if(auth()->user()->hasRole('admin') || ($producto->es_personalizado && $producto->user_id === auth()->id()))
                                                    <button wire:click="eliminar({{ $producto->id }})" 
                                                        wire:confirm="¿Estás seguro de eliminar este producto?"
                                                        class="text-red-600 hover:text-red-900">
                                                        Eliminar
                                                    </button>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            No se encontraron productos.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $productos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
