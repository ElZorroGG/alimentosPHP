<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Menú Semanal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            color: #555;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }
        .header-info {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
        }
        .dia-container {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .dia-titulo {
            background-color: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
        }
        .comida {
            margin: 10px 0;
            padding-left: 15px;
        }
        .comida-tipo {
            font-weight: bold;
            color: #333;
            text-transform: capitalize;
        }
        .plato-nombre {
            color: #555;
            margin-left: 10px;
        }
        .productos-lista {
            margin-left: 30px;
            font-size: 10px;
            color: #666;
        }
        .macros {
            display: inline-block;
            background-color: #e8f4f8;
            padding: 3px 8px;
            border-radius: 3px;
            margin-left: 10px;
            font-size: 10px;
        }
        .totales-dia {
            margin-top: 10px;
            padding: 8px;
            background-color: #fff9e6;
            border-left: 3px solid #ffc107;
        }
        .totales-semana {
            margin-top: 30px;
            padding: 15px;
            background-color: #e8f5e9;
            border: 2px solid #4caf50;
            page-break-inside: avoid;
        }
        .totales-semana h2 {
            color: #2e7d32;
            margin-top: 0;
        }
        .grid {
            display: table;
            width: 100%;
        }
        .grid-item {
            display: table-cell;
            width: 25%;
            padding: 5px;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <h1>📊 Reporte Nutricional Semanal</h1>
    
    <div class="header-info">
        <p><strong>Usuario:</strong> {{ $user->name }}</p>
        <p><strong>Período:</strong> {{ $inicio->translatedFormat('d/m/Y') }} - {{ $fin->translatedFormat('d/m/Y') }}</p>
        <p><strong>Fecha de generación:</strong> {{ now()->translatedFormat('d/m/Y H:i') }}</p>
    </div>

    @foreach($dias as $dia)
        <div class="dia-container">
            <div class="dia-titulo">
                {{ $dia['fecha']->translatedFormat('l, j \de F \de Y') }}
            </div>

            @if($dia['menus']->isEmpty())
                <div class="comida">
                    <em style="color: #999;">Sin comidas registradas</em>
                </div>
            @else
                @foreach($dia['menus'] as $menu)
                    <div class="comida">
                        <span class="comida-tipo">{{ ucfirst($menu->tipo_comida) }}:</span>
                        <span class="plato-nombre">{{ $menu->platos->pluck('nombre')->join(', ') }}</span>
                        
                        @php
                            $calorias = 0;
                            $proteinas = 0;
                            $carbos = 0;
                            $grasas = 0;
                            foreach($menu->platos as $platoItem) {
                                foreach($platoItem->productos as $producto) {
                                    $factor = $producto->pivot->cantidad_gramos / 100;
                                    $calorias += $producto->calorias * $factor;
                                    $proteinas += $producto->proteinas * $factor;
                                    $carbos += $producto->carbohidratos * $factor;
                                    $grasas += $producto->grasa_total * $factor;
                                }
                            }
                        @endphp
                        
                        <span class="macros">
                            {{ number_format($calorias, 0) }} kcal | 
                            P: {{ number_format($proteinas, 0) }}g | 
                            C: {{ number_format($carbos, 0) }}g | 
                            G: {{ number_format($grasas, 0) }}g
                        </span>

                        <div class="productos-lista">
                            Ingredientes: 
                            @foreach($menu->platos as $platoItem)
                                @foreach($platoItem->productos as $producto)
                                    {{ $producto->nombre }} ({{ $producto->pivot->cantidad_gramos }}g){{ !$loop->last || !$loop->parent->last ? ', ' : '' }}
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="totales-dia">
                    <strong>Total del día:</strong>
                    {{ number_format($dia['totales']['calorias'], 0) }} kcal |
                    Proteínas: {{ number_format($dia['totales']['proteinas'], 1) }}g |
                    Carbohidratos: {{ number_format($dia['totales']['carbohidratos'], 1) }}g |
                    Grasas: {{ number_format($dia['totales']['grasas'], 1) }}g
                </div>
            @endif
        </div>
    @endforeach

    <div class="totales-semana">
        <h2>📈 Resumen Semanal</h2>
        
        <div class="grid">
            <div class="grid-item">
                <div class="stat-label">Calorías Totales</div>
                <div class="stat-value">{{ number_format($totalesSemana['calorias'], 0) }} kcal</div>
                <div class="stat-label">Promedio: {{ number_format($totalesSemana['calorias'] / 7, 0) }} kcal/día</div>
            </div>
            <div class="grid-item">
                <div class="stat-label">Proteínas Totales</div>
                <div class="stat-value">{{ number_format($totalesSemana['proteinas'], 0) }}g</div>
                <div class="stat-label">Promedio: {{ number_format($totalesSemana['proteinas'] / 7, 1) }}g/día</div>
            </div>
            <div class="grid-item">
                <div class="stat-label">Carbohidratos Totales</div>
                <div class="stat-value">{{ number_format($totalesSemana['carbohidratos'], 0) }}g</div>
                <div class="stat-label">Promedio: {{ number_format($totalesSemana['carbohidratos'] / 7, 1) }}g/día</div>
            </div>
            <div class="grid-item">
                <div class="stat-label">Grasas Totales</div>
                <div class="stat-value">{{ number_format($totalesSemana['grasas'], 0) }}g</div>
                <div class="stat-label">Promedio: {{ number_format($totalesSemana['grasas'] / 7, 1) }}g/día</div>
            </div>
        </div>

        @if($user->objetivo_calorias)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ccc;">
                <strong>Comparación con Objetivos:</strong><br>
                <small>
                    Objetivo Calorías: {{ number_format($user->objetivo_calorias, 0) }} kcal/día 
                    (Promedio semanal: {{ number_format(($totalesSemana['calorias'] / 7 / $user->objetivo_calorias) * 100, 0) }}%)
                    @if($user->objetivo_proteinas)
                        | Proteínas: {{ number_format($user->objetivo_proteinas, 0) }}g/día 
                        ({{ number_format(($totalesSemana['proteinas'] / 7 / $user->objetivo_proteinas) * 100, 0) }}%)
                    @endif
                </small>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Generado con Sistema de Gestión Nutricional | {{ config('app.name') }}</p>
    </div>
</body>
</html>
