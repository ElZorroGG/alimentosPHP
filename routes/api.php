<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiFoodController;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/status',function (){
    return response()->json([
        'status'=> 'ok',
        'laravell'=> app()->version(),
    ]);
});
Route::group(['middleware'=>'auth:sanctum'],function (){
    Route::apiResource('food', ApiFoodController::class);
});
Route::post('/login', [UserController::class, 'index']);