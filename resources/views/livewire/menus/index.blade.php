<div>
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mi Calendario de Comidas
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
                        <button wire:click="semanaAnterior" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            ← Semana Anterior
                        </button>
                        
                        <div class="flex gap-3">
                            <button wire:click="semanaActual" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Semana Actual
                            </button>
                            <a href="{{ route('exportar.semana', ['fecha' => $semanaInicio]) }}" 
                               class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                               target="_blank">
                                📄 Exportar PDF
                            </a>
                        </div>
                        
                        <button wire:click="semanaSiguiente" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Semana Siguiente →
                        </button>
                    </div>

                    <!-- Calendario Semanal -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">
                                        Comida
                                    </th>
                                    @foreach($dias as $dia)
                                        <th class="border border-gray-300 px-4 py-2 text-center text-sm font-semibold {{ $dia['esHoy'] ? 'bg-blue-100' : '' }}">
                                            <div>{{ $dia['nombre'] }}</div>
                                            <div class="text-lg font-bold">{{ $dia['dia'] }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tiposComida as $tipo)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3 font-semibold bg-gray-50">
                                            {{ ucfirst($tipo) }}
                                        </td>
                                        @foreach($dias as $dia)
                                            @php
                                                $clave = $dia['fecha'] . '-' . $tipo;
                                                $menu = $menus->get($clave);
                                            @endphp
                                            <td class="border border-gray-300 px-2 py-2 text-center align-top {{ $dia['esHoy'] ? 'bg-blue-50' : '' }}">
                                                @if($menu)
                                                    <div class="bg-green-100 rounded p-2 text-xs">
                                                        <div class="font-semibold mb-1">{{ $menu->plato->nombre }}</div>
                                                        @php
                                                            $totales = [
                                                                'calorias' => 0,
                                                                'proteinas' => 0,
                                                                'carbohidratos' => 0,
                                                                'grasas' => 0,
                                                            ];
                                                            foreach($menu->plato->productos as $producto) {
                                                                $factor = $producto->pivot->cantidad_gramos / 100;
                                                                $totales['calorias'] += $producto->calorias * $factor;
                                                                $totales['proteinas'] += $producto->proteinas * $factor;
                                                                $totales['carbohidratos'] += $producto->carbohidratos * $factor;
                                                                $totales['grasas'] += $producto->grasa_total * $factor;
                                                            }
                                                        @endphp
                                                        <div class="text-gray-600">
                                                            {{ number_format($totales['calorias'], 0) }} kcal
                                                        </div>
                                                        <div class="flex gap-2 mt-1 justify-center">
                                                            <a href="{{ route('menus.edit', $menu->id) }}" class="text-blue-600 hover:text-blue-900">
                                                                ✏️
                                                            </a>
                                                            <button 
                                                                wire:click="eliminar({{ $menu->id }})" 
                                                                wire:confirm="¿Eliminar este menú?"
                                                                class="text-red-600 hover:text-red-900">
                                                                🗑️
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <a href="{{ route('menus.create', ['fecha' => $dia['fecha'], 'tipo_comida' => $tipo]) }}" 
                                                       class="block w-full h-full min-h-[60px] hover:bg-gray-100 rounded flex items-center justify-center">
                                                        <span class="text-gray-400 text-2xl">+</span>
                                                    </a>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen del Día -->
                    <div class="mt-6 p-4 bg-indigo-50 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-3">Resumen de la Semana</h3>
                        <div class="grid grid-cols-2 md:grid-cols-7 gap-2 text-xs">
                            @foreach($dias as $dia)
                                @php
                                    $totalDia = [
                                        'calorias' => 0,
                                        'proteinas' => 0,
                                        'carbohidratos' => 0,
                                        'grasas' => 0,
                                    ];
                                    
                                    foreach($tiposComida as $tipo) {
                                        $clave = $dia['fecha'] . '-' . $tipo;
                                        $menu = $menus->get($clave);
                                        if ($menu) {
                                            foreach($menu->plato->productos as $producto) {
                                                $factor = $producto->pivot->cantidad_gramos / 100;
                                                $totalDia['calorias'] += $producto->calorias * $factor;
                                                $totalDia['proteinas'] += $producto->proteinas * $factor;
                                                $totalDia['carbohidratos'] += $producto->carbohidratos * $factor;
                                                $totalDia['grasas'] += $producto->grasa_total * $factor;
                                            }
                                        }
                                    }
                                @endphp
                                <div class="bg-white p-3 rounded {{ $dia['esHoy'] ? 'ring-2 ring-blue-500' : '' }}">
                                    <div class="font-semibold mb-1">{{ substr($dia['nombre'], 0, 3) }}</div>
                                    @if($totalDia['calorias'] > 0)
                                        <div class="space-y-1">
                                            <div><strong>{{ number_format($totalDia['calorias'], 0) }}</strong> kcal</div>
                                            <div class="text-gray-600">P: {{ number_format($totalDia['proteinas'], 0) }}g</div>
                                            <div class="text-gray-600">C: {{ number_format($totalDia['carbohidratos'], 0) }}g</div>
                                            <div class="text-gray-600">G: {{ number_format($totalDia['grasas'], 0) }}g</div>
                                        </div>
                                    @else
                                        <div class="text-gray-400">Sin datos</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
