<div>
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $menuId ? 'Editar' : 'Agregar' }} Comida al Calendario
        </h2>
    @endsection

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex justify-end mb-4">
                        <a href="{{ route('menus.index') }}" class="text-gray-600 hover:text-gray-900">
                            ← Volver al Calendario
                        </a>
                    </div>

                    <form wire:submit="guardar">
                        <!-- Fecha -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                            <input wire:model="fecha" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('fecha') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tipo de Comida -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Comida</label>
                            <select wire:model="tipo_comida" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecciona un tipo</option>
                                <option value="desayuno">Desayuno</option>
                                <option value="almuerzo">Almuerzo</option>
                                <option value="comida">Comida</option>
                                <option value="merienda">Merienda</option>
                                <option value="cena">Cena</option>
                            </select>
                            @error('tipo_comida') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Plato -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plato</label>
                            @if($platos->isEmpty())
                                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                                    No tienes platos creados. 
                                    <a href="{{ route('platos.create') }}" class="underline font-semibold">Crea uno primero</a>.
                                </div>
                            @else
                                <select wire:model.live="plato_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Selecciona un plato</option>
                                    @foreach($platos as $plato)
                                        <option value="{{ $plato->id }}">
                                            {{ $plato->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plato_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                <!-- Vista previa del plato seleccionado -->
                                @if($plato_id)
                                    @php
                                        $platoSeleccionado = $platos->find($plato_id);
                                        if ($platoSeleccionado) {
                                            $totales = [
                                                'calorias' => 0,
                                                'proteinas' => 0,
                                                'carbohidratos' => 0,
                                                'grasas' => 0,
                                            ];
                                            foreach($platoSeleccionado->productos as $producto) {
                                                $factor = $producto->pivot->cantidad_gramos / 100;
                                                $totales['calorias'] += $producto->calorias * $factor;
                                                $totales['proteinas'] += $producto->proteinas * $factor;
                                                $totales['carbohidratos'] += $producto->carbohidratos * $factor;
                                                $totales['grasas'] += $producto->grasa_total * $factor;
                                            }
                                        }
                                    @endphp
                                    @if(isset($totales))
                                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                            <h4 class="font-semibold text-gray-700 mb-2">Vista Previa: {{ $platoSeleccionado->nombre }}</h4>
                                            
                                            @if($platoSeleccionado->descripcion)
                                                <p class="text-sm text-gray-600 mb-3">{{ $platoSeleccionado->descripcion }}</p>
                                            @endif

                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mb-3">
                                                <div>
                                                    <span class="text-gray-600">Calorías:</span>
                                                    <strong>{{ number_format($totales['calorias'], 0) }} kcal</strong>
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

                                            <div class="text-xs text-gray-600">
                                                <strong>Productos:</strong>
                                                <ul class="list-disc list-inside mt-1">
                                                    @foreach($platoSeleccionado->productos as $producto)
                                                        <li>{{ $producto->nombre }} ({{ $producto->pivot->cantidad_gramos }}g)</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('menus.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            @if(!$platos->isEmpty())
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ $menuId ? 'Actualizar' : 'Guardar' }} Menú
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
