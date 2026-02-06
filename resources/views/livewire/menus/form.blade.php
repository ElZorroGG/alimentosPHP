<div>
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $menuId ? 'Editar' : 'Agregar' }} Comida al Calendario
        </h2>
    @endsection

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

                        <!-- Platos -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Platos</label>
                            @if($platos->isEmpty())
                                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                                    No tienes platos creados. 
                                    <a href="{{ route('platos.create') }}" class="underline font-semibold">Crea uno primero</a>.
                                </div>
                            @else
                                {{-- Buscador --}}
                                <div class="relative mb-3">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        🔍
                                    </span>
                                    <input 
                                        wire:model.live.debounce.300ms="busquedaPlato" 
                                        type="text" 
                                        placeholder="Buscar platos..." 
                                        class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    >
                                </div>

                                {{-- Contador de seleccionados --}}
                                @if(count($plato_ids) > 0)
                                    <div class="flex items-center justify-between mb-3 px-1">
                                        <span class="text-sm text-indigo-600 font-medium">
                                            {{ count($plato_ids) }} {{ count($plato_ids) === 1 ? 'plato seleccionado' : 'platos seleccionados' }}
                                        </span>
                                        <button type="button" wire:click="$set('plato_ids', [])" class="text-xs text-red-500 hover:text-red-700 hover:underline">
                                            Quitar todos
                                        </button>
                                    </div>
                                @endif

                                @error('plato_ids') <div class="text-red-500 text-sm mb-3">{{ $message }}</div> @enderror

                                {{-- Grid de platos como tarjetas --}}
                                @php
                                    $platosFiltrados = $platos->when($busquedaPlato, function($collection) {
                                        return $collection->filter(function($plato) {
                                            return str_contains(mb_strtolower($plato->nombre), mb_strtolower($this->busquedaPlato));
                                        });
                                    });
                                @endphp

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[420px] overflow-y-auto pr-1">
                                    @forelse($platosFiltrados as $plato)
                                        @php
                                            $seleccionado = in_array($plato->id, $plato_ids);
                                            $cal = 0; $prot = 0; $carb = 0; $gra = 0;
                                            foreach($plato->productos as $producto) {
                                                $factor = $producto->pivot->cantidad_gramos / 100;
                                                $cal += $producto->calorias * $factor;
                                                $prot += $producto->proteinas * $factor;
                                                $carb += $producto->carbohidratos * $factor;
                                                $gra += $producto->grasa_total * $factor;
                                            }
                                        @endphp
                                        <div 
                                            wire:click="togglePlato({{ $plato->id }})"
                                            class="relative cursor-pointer rounded-xl border-2 p-4 transition-all duration-150 
                                                {{ $seleccionado 
                                                    ? 'border-indigo-500 bg-indigo-50 shadow-md ring-1 ring-indigo-300' 
                                                    : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm' }}"
                                        >
                                            {{-- Check indicator --}}
                                            <div class="absolute top-3 right-3">
                                                @if($seleccionado)
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-500 text-white text-xs font-bold">✓</span>
                                                @else
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 border-gray-300"></span>
                                                @endif
                                            </div>

                                            {{-- Nombre --}}
                                            <h4 class="font-semibold text-sm text-gray-800 pr-8 mb-1">{{ $plato->nombre }}</h4>
                                            
                                            @if($plato->descripcion)
                                                <p class="text-xs text-gray-500 mb-2 line-clamp-1">{{ $plato->descripcion }}</p>
                                            @endif

                                            {{-- Macros en mini badges --}}
                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-700">
                                                    🔥 {{ number_format($cal, 0) }} kcal
                                                </span>
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                                                    P {{ number_format($prot, 0) }}g
                                                </span>
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700">
                                                    C {{ number_format($carb, 0) }}g
                                                </span>
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">
                                                    G {{ number_format($gra, 0) }}g
                                                </span>
                                            </div>
                                            
                                            {{-- Ingredientes --}}
                                            <div class="mt-2 text-[11px] text-gray-400 truncate">
                                                {{ $plato->productos->pluck('nombre')->join(', ') }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-span-2 text-center text-gray-400 py-6">
                                            No se encontraron platos con "{{ $busquedaPlato }}"
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Resumen nutricional de los seleccionados --}}
                                @if(!empty($plato_ids) && count($plato_ids) > 0)
                                    @php
                                        $platosSeleccionados = $platos->whereIn('id', $plato_ids);
                                        $totales = ['calorias' => 0, 'proteinas' => 0, 'carbohidratos' => 0, 'grasas' => 0];
                                        foreach($platosSeleccionados as $platoSel) {
                                            foreach($platoSel->productos as $producto) {
                                                $factor = $producto->pivot->cantidad_gramos / 100;
                                                $totales['calorias'] += $producto->calorias * $factor;
                                                $totales['proteinas'] += $producto->proteinas * $factor;
                                                $totales['carbohidratos'] += $producto->carbohidratos * $factor;
                                                $totales['grasas'] += $producto->grasa_total * $factor;
                                            }
                                        }
                                    @endphp
                                    <div class="mt-4 rounded-xl bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 p-4">
                                        <h4 class="text-sm font-semibold text-indigo-800 mb-3">
                                            Resumen Nutricional — {{ $platosSeleccionados->count() }} {{ $platosSeleccionados->count() === 1 ? 'plato' : 'platos' }}
                                        </h4>
                                        <div class="grid grid-cols-4 gap-3 text-center">
                                            <div class="bg-white rounded-lg p-2 shadow-sm">
                                                <div class="text-lg font-bold text-orange-600">{{ number_format($totales['calorias'], 0) }}</div>
                                                <div class="text-[10px] text-gray-500 uppercase tracking-wide">kcal</div>
                                            </div>
                                            <div class="bg-white rounded-lg p-2 shadow-sm">
                                                <div class="text-lg font-bold text-blue-600">{{ number_format($totales['proteinas'], 1) }}</div>
                                                <div class="text-[10px] text-gray-500 uppercase tracking-wide">Proteínas (g)</div>
                                            </div>
                                            <div class="bg-white rounded-lg p-2 shadow-sm">
                                                <div class="text-lg font-bold text-amber-600">{{ number_format($totales['carbohidratos'], 1) }}</div>
                                                <div class="text-[10px] text-gray-500 uppercase tracking-wide">Carbos (g)</div>
                                            </div>
                                            <div class="bg-white rounded-lg p-2 shadow-sm">
                                                <div class="text-lg font-bold text-red-600">{{ number_format($totales['grasas'], 1) }}</div>
                                                <div class="text-[10px] text-gray-500 uppercase tracking-wide">Grasas (g)</div>
                                            </div>
                                        </div>
                                    </div>
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
