<div>
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $platoId ? 'Editar' : 'Nuevo' }} Plato
        </h2>
    @endsection

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('platos.index') }}" class="text-gray-600 hover:text-gray-900">
                            ← Volver
                        </a>
                    </div>

                    <form wire:submit="guardar">
                        <!-- Nombre -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Plato</label>
                            <input wire:model="nombre" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción (opcional)</label>
                            <textarea wire:model="descripcion" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Productos -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <label class="block text-sm font-medium text-gray-700">Productos del Plato</label>
                                <button type="button" wire:click="agregarProducto" class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-1 px-3 rounded">
                                    + Agregar Producto
                                </button>
                            </div>

                            @if(empty($productos))
                                <p class="text-gray-500 text-sm mb-4">No hay productos agregados</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($productos as $index => $producto)
                                        <div class="flex gap-3 items-start p-4 bg-gray-50 rounded-lg">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-1">Producto</label>
                                                <select wire:model.live="productos.{{ $index }}.producto_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">Selecciona un producto</option>
                                                    @foreach($todosProductos as $prod)
                                                        <option value="{{ $prod->id }}">
                                                            {{ $prod->nombre }} 
                                                            @if($prod->categoria)
                                                                ({{ $prod->categoria->nombre }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('productos.'.$index.'.producto_id') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <div class="w-32">
                                                <label class="block text-xs text-gray-600 mb-1">Gramos</label>
                                                <input 
                                                    wire:model.live.debounce.500ms="productos.{{ $index }}.cantidad_gramos" 
                                                    type="number" 
                                                    step="1" 
                                                    min="1"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                >
                                                @error('productos.'.$index.'.cantidad_gramos') 
                                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                                @enderror
                                            </div>

                                            <div class="pt-6">
                                                <button 
                                                    type="button" 
                                                    wire:click="quitarProducto({{ $index }})"
                                                    class="text-red-600 hover:text-red-900 text-sm font-bold"
                                                >
                                                    ✕
                                                </button>
                                            </div>
                                        </div>

                                        @if(!empty($producto['producto_id']))
                                            @php
                                                $productoSeleccionado = $todosProductos->find($producto['producto_id']);
                                                if ($productoSeleccionado && !empty($producto['cantidad_gramos'])) {
                                                    $factor = $producto['cantidad_gramos'] / 100;
                                                    $calorias = $productoSeleccionado->calorias * $factor;
                                                    $proteinas = $productoSeleccionado->proteinas * $factor;
                                                    $carbohidratos = $productoSeleccionado->carbohidratos * $factor;
                                                    $grasas = $productoSeleccionado->grasa_total * $factor;
                                                }
                                            @endphp
                                            @if(isset($calorias))
                                                <div class="text-xs text-gray-600 bg-blue-50 p-2 rounded">
                                                    <strong>{{ $producto['cantidad_gramos'] }}g:</strong> 
                                                    {{ number_format($calorias, 1) }} kcal, 
                                                    {{ number_format($proteinas, 1) }}g proteína, 
                                                    {{ number_format($carbohidratos, 1) }}g carbohidratos, 
                                                    {{ number_format($grasas, 1) }}g grasas
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @php
                                $totales = [
                                    'calorias' => 0,
                                    'proteinas' => 0,
                                    'carbohidratos' => 0,
                                    'grasas' => 0,
                                ];
                                foreach($productos as $producto) {
                                    if (!empty($producto['producto_id']) && !empty($producto['cantidad_gramos'])) {
                                        $prod = $todosProductos->find($producto['producto_id']);
                                        if ($prod) {
                                            $factor = $producto['cantidad_gramos'] / 100;
                                            $totales['calorias'] += $prod->calorias * $factor;
                                            $totales['proteinas'] += $prod->proteinas * $factor;
                                            $totales['carbohidratos'] += $prod->carbohidratos * $factor;
                                            $totales['grasas'] += $prod->grasa_total * $factor;
                                        }
                                    }
                                }
                            @endphp
                            
                            @if($totales['calorias'] > 0)
                                <div class="mt-4 p-4 bg-indigo-50 rounded-lg">
                                    <h4 class="font-semibold text-gray-700 mb-2">Totales del Plato:</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-600">Calorías:</span>
                                            <strong>{{ number_format($totales['calorias'], 1) }} kcal</strong>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Proteínas:</span>
                                            <strong>{{ number_format($totales['proteinas'], 1) }}g</strong>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Carbohidratos:</span>
                                            <strong>{{ number_format($totales['carbohidratos'], 1) }}g</strong>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Grasas:</span>
                                            <strong>{{ number_format($totales['grasas'], 1) }}g</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('platos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ $platoId ? 'Actualizar' : 'Guardar' }} Plato
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
