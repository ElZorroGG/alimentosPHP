<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class ApiFoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
                'status'=>true,
                'message'=>'Lista de productos en BBDD',
                'data'=> food::all()
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        return response()->json([
            'status' => false,
            'message' => 'Use POST method to /api/foods with required data',
            'required_fields' => ['name', 'description', 'price']
        ], 405);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);

        $food = Food::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $food
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Food $food){
        return response()->json([
            'status'=>true,
            'message'=>'Detalle del producto',
            'data'=> $food
    ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Food $food)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Food $food)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Food $food)
    {
        //
    }
}
