<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Plato;
use App\Models\Menu;

class AdminStatsController extends Controller
{
    public function index()
    {
        $stats = [
            'productos' => Producto::count(),
            'platos' => Plato::count(),
            'menus' => Menu::count(),
        ];

        return view('admin.stats', compact('stats'));
    }
}
