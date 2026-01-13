<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    function index($request){
        $user = User::where('email',$request->email)->first();
        if($user && password_verify($request->password,$user->password)){
            return response()->json([
                'status'=>true,
                'message'=>'usuario autenticado correctamente',
                'token'=> $user->createToken('api_token')->plainTextToken
            ]);
        }else{
            return response()->json([
                'status'=> false,
                'message'=> 'Creedenciales incorrectas',
                'token'=> null
            ]);
        }
    }
}
