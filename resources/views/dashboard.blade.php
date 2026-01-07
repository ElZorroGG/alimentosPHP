<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard Nutricional
            </h2>
            <a href="{{ route('objetivos.edit') }}" class="text-sm bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                ⚙️ Configurar Objetivos
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php
                $user = auth()->user();
                $hoy = now()->format('Y-m-d');
                $menusHoy = \App\Models\Menu::where('user_id', auth()->id())
                    ->where('fecha', $hoy)
                    ->with('plato.productos')
                    ->get();
                
                $totalesHoy = [
                    'calorias' => 0,
                    'proteinas' => 0,
                    'carbohidratos' => 0,
                    'grasas' => 0,
                ];
                
                foreach($menusHoy as $menu) {
                    foreach($menu->plato->productos as $producto) {
                        $factor = $producto->pivot->cantidad_gramos / 100;
                        $totalesHoy['calorias'] += $producto->calorias * $factor;
                        $totalesHoy['proteinas'] += $producto->proteinas * $factor;
                        $totalesHoy['carbohidratos'] += $producto->carbohidratos * $factor;
                        $totalesHoy['grasas'] += $producto->grasa_total * $factor;
                    }
                }

                $porcentajes = [
                    'calorias' => $user->objetivo_calorias ? ($totalesHoy['calorias'] / $user->objetivo_calorias) * 100 : 0,
                    'proteinas' => $user->objetivo_proteinas ? ($totalesHoy['proteinas'] / $user->objetivo_proteinas) * 100 : 0,
                    'carbohidratos' => $user->objetivo_carbohidratos ? ($totalesHoy['carbohidratos'] / $user->objetivo_carbohidratos) * 100 : 0,
                    'grasas' => $user->objetivo_grasas ? ($totalesHoy['grasas'] / $user->objetivo_grasas) * 100 : 0,
                ];

                $inicioSemana = now()->startOfWeek()->format('Y-m-d');
                $finSemana = now()->endOfWeek()->format('Y-m-d');
                
                $menusSemana = \App\Models\Menu::where('user_id', auth()->id())
                    ->whereBetween('fecha', [$inicioSemana, $finSemana])
                    ->with('plato.productos')
                    ->get();
                
                $totalesSemana = [
                    'calorias' => 0,
                    'proteinas' => 0,
                    'carbohidratos' => 0,
                    'grasas' => 0,
                ];
                
                foreach($menusSemana as $menu) {
                    foreach($menu->plato->productos as $producto) {
                        $factor = $producto->pivot->cantidad_gramos / 100;
                        $totalesSemana['calorias'] += $producto->calorias * $factor;
                        $totalesSemana['proteinas'] += $producto->proteinas * $factor;
                        $totalesSemana['carbohidratos'] += $producto->carbohidratos * $factor;
                        $totalesSemana['grasas'] += $producto->grasa_total * $factor;
                    }
                }

                $promedioSemana = [
                    'calorias' => $totalesSemana['calorias'] / 7,
                    'proteinas' => $totalesSemana['proteinas'] / 7,
                    'carbohidratos' => $totalesSemana['carbohidratos'] / 7,
                    'grasas' => $totalesSemana['grasas'] / 7,
                ];

                $totalPlatos = \App\Models\Plato::where('user_id', auth()->id())->count();
                $totalProductosPersonales = \App\Models\Producto::where('user_id', auth()->id())->count();
                $totalMenus = \App\Models\Menu::where('user_id', auth()->id())->count();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-600 mb-1">Mis Platos</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $totalPlatos }}</div>
                        <a href="{{ route('platos.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver todos →</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-600 mb-1">Productos Personales</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $totalProductosPersonales }}</div>
                        <a href="{{ route('productos.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver todos →</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-600 mb-1">Comidas Planificadas</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $totalMenus }}</div>
                        <a href="{{ route('menus.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Ver calendario →</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-600 mb-1">Productos BEDCA</div>
                        <div class="text-3xl font-bold text-gray-900">210</div>
                        <a href="{{ route('productos.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Explorar →</a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Resumen Nutricional de Hoy</h3>
                    <div class="text-sm text-gray-600 mb-4">{{ now()->translatedFormat('l, j \de F \de Y') }}</div>
                    
                    @if($menusHoy->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            No tienes comidas planificadas para hoy.
                            <a href="{{ route('menus.create', ['fecha' => $hoy]) }}" class="text-blue-600 hover:text-blue-800 underline ml-2">
                                Agregar comida
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                            @php
                                $nutrientes = [
                                    ['key' => 'calorias', 'label' => 'Calorías', 'unidad' => 'kcal', 'color' => 'blue', 'objetivo' => $user->objetivo_calorias],
                                    ['key' => 'proteinas', 'label' => 'Proteínas', 'unidad' => 'g', 'color' => 'green', 'objetivo' => $user->objetivo_proteinas],
                                    ['key' => 'carbohidratos', 'label' => 'Carbohidratos', 'unidad' => 'g', 'color' => 'yellow', 'objetivo' => $user->objetivo_carbohidratos],
                                    ['key' => 'grasas', 'label' => 'Grasas', 'unidad' => 'g', 'color' => 'red', 'objetivo' => $user->objetivo_grasas],
                                ];
                            @endphp
                            @foreach($nutrientes as $nutriente)
                                @php
                                    $valor = $totalesHoy[$nutriente['key']];
                                    $objetivo = $nutriente['objetivo'];
                                    $porcentaje = $objetivo ? ($valor / $objetivo) * 100 : 0;
                                    $colorPorcentaje = $porcentaje < 80 ? 'text-orange-600' : ($porcentaje > 120 ? 'text-red-600' : 'text-green-600');
                                @endphp
                                <div class="text-center p-4 bg-{{ $nutriente['color'] }}-50 rounded-lg relative">
                                    <div class="text-4xl font-bold text-{{ $nutriente['color'] }}-600">
                                        {{ number_format($valor, $nutriente['unidad'] == 'kcal' ? 0 : 1) }}
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">{{ $nutriente['label'] }} ({{ $nutriente['unidad'] }})</div>
                                    
                                    @if($objetivo)
                                        <div class="mt-2 text-xs {{ $colorPorcentaje }} font-bold">
                                            {{ number_format($porcentaje, 0) }}% del objetivo
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Meta: {{ number_format($objetivo, 0) }} {{ $nutriente['unidad'] }}
                                        </div>
                                        @if($porcentaje < 80)
                                            <div class="text-xs text-orange-600 mt-1">⚠️ Por debajo del objetivo</div>
                                        @elseif($porcentaje > 120)
                                            <div class="text-xs text-red-600 mt-1">⚠️ Objetivo superado</div>
                                        @else
                                            <div class="text-xs text-green-600 mt-1">✓ Dentro del rango</div>
                                        @endif
                                    @else
                                        <div class="text-xs text-gray-400 mt-2">
                                            <a href="{{ route('objetivos.edit') }}" class="underline">Configurar objetivo</a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t pt-4">
                            <h4 class="font-semibold mb-3">Comidas del día:</h4>
                            <div class="space-y-3">
                                @foreach($menusHoy as $menu)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                        <div>
                                            <div class="font-semibold">{{ ucfirst($menu->tipo_comida) }}</div>
                                            <div class="text-sm text-gray-600">{{ $menu->plato->nombre }}</div>
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            @php
                                                $caloriasComida = 0;
                                                foreach($menu->plato->productos as $producto) {
                                                    $caloriasComida += ($producto->calorias / 100) * $producto->pivot->cantidad_gramos;
                                                }
                                            @endphp
                                            {{ number_format($caloriasComida, 0) }} kcal
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Promedio Semanal</h3>
                    <div class="text-sm text-gray-600 mb-4">
                        Del {{ \Carbon\Carbon::parse($inicioSemana)->translatedFormat('j \de F') }} 
                        al {{ \Carbon\Carbon::parse($finSemana)->translatedFormat('j \de F') }}
                    </div>
                    
                    @if($menusSemana->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            No tienes comidas planificadas esta semana.
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-3xl font-bold text-blue-600">{{ number_format($promedioSemana['calorias'], 0) }}</div>
                                <div class="text-sm text-gray-600 mt-1">Calorías/día</div>
                                <div class="text-xs text-gray-500 mt-1">Total: {{ number_format($totalesSemana['calorias'], 0) }}</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-3xl font-bold text-green-600">{{ number_format($promedioSemana['proteinas'], 1) }}</div>
                                <div class="text-sm text-gray-600 mt-1">Proteínas/día (g)</div>
                                <div class="text-xs text-gray-500 mt-1">Total: {{ number_format($totalesSemana['proteinas'], 0) }}g</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-3xl font-bold text-yellow-600">{{ number_format($promedioSemana['carbohidratos'], 1) }}</div>
                                <div class="text-sm text-gray-600 mt-1">Carbohidratos/día (g)</div>
                                <div class="text-xs text-gray-500 mt-1">Total: {{ number_format($totalesSemana['carbohidratos'], 0) }}g</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-3xl font-bold text-red-600">{{ number_format($promedioSemana['grasas'], 1) }}</div>
                                <div class="text-sm text-gray-600 mt-1">Grasas/día (g)</div>
                                <div class="text-xs text-gray-500 mt-1">Total: {{ number_format($totalesSemana['grasas'], 0) }}g</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Accesos Rápidos</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('menus.create') }}" class="p-4 text-center bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                            <div class="text-2xl mb-2">🍽️</div>
                            <div class="text-sm font-semibold">Agregar Comida</div>
                        </a>
                        <a href="{{ route('platos.create') }}" class="p-4 text-center bg-green-50 hover:bg-green-100 rounded-lg transition">
                            <div class="text-2xl mb-2">🥘</div>
                            <div class="text-sm font-semibold">Crear Plato</div>
                        </a>
                        <a href="{{ route('productos.create') }}" class="p-4 text-center bg-yellow-50 hover:bg-yellow-100 rounded-lg transition">
                            <div class="text-2xl mb-2">🥕</div>
                            <div class="text-sm font-semibold">Nuevo Producto</div>
                        </a>
                        <a href="{{ route('menus.index') }}" class="p-4 text-center bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                            <div class="text-2xl mb-2">📅</div>
                            <div class="text-sm font-semibold">Ver Calendario</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
