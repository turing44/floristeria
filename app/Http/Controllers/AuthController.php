<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterUserRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        $user = User::where("email", $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                "messaje" => "Credenciales incorrectas"
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            "token" => $token,
            "rol" => $user->rol,
        ], Response::HTTP_OK);
    }

    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'rol' => $data['rol'], 
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request) {

        $user = $request->user();

        $user->tokens()->delete();

        return response()->json([
                "message" => "Se han eliminiado los tokens del usuario con id: ".$user->id
            ], Response::HTTP_OK);  
    }
}
