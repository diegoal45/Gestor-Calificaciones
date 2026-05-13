<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Events\UsuarioRegistrado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        
        // Disparar evento de bienvenida
        event(new UsuarioRegistrado($usuario));
        
        $token = $usuario->createToken('auth_token')->plainTextToken;
        return response()->json(['usuario' => $usuario, 'token' => $token], 201);
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
        $token = $usuario->createToken('auth_token')->plainTextToken;
        return response()->json(['usuario' => $usuario, 'token' => $token]);
    }

    // Logout de usuario
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('usuarios', 'email')->ignore($user->id)],
        ]);
        $user->update($data);
        return response()->json(['usuario' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'password_actual' => 'required|string',
            'password_nueva' => 'required|string|min:6',
        ]);
        $user = $request->user();
        if (!Hash::check($data['password_actual'], $user->password)) {
            return response()->json(['message' => 'La contraseña actual no coincide'], 422);
        }
        $user->password = Hash::make($data['password_nueva']);
        $user->save();
        return response()->json(['message' => 'Contraseña actualizada']);
    }
}
