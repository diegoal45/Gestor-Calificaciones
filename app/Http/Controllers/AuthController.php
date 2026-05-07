<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Registro de usuario (profesor o estudiante)
    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:profesor,estudiante',
        ]);
        $data['password'] = Hash::make($data['password']);
        $usuario = Usuario::create($data);
        return response()->json(['usuario' => $usuario], 201);
    }

    // Login de usuario
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        $usuario = Usuario::where('email', $credentials['email'])->first();
        if (!$usuario || !Hash::check($credentials['password'], $usuario->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
        // Aquí podrías generar un token si usas Sanctum/JWT
        return response()->json(['usuario' => $usuario]);
    }
}
