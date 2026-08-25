<?php

namespace App\Services;

use App\DTOs\Auth\LoginData;
use App\DTOs\Auth\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function registrar(RegisterData $data): array
    {
        $usuario = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $token = Auth::guard('api')->login($usuario);

        return [
            'usuario' => $usuario,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')
                ->factory()
                ->getTTL() * 60,
        ];
    }

    public function login(LoginData $data): array
    {
        $token = Auth::guard('api')->attempt(
            $data->credentials()
        );

        if (!$token) {
            throw ValidationException::withMessages([
                'credenciales' => [
                    'Email o contraseña incorrectos.',
                ],
            ]);
        }

        return [
            'usuario' => Auth::guard('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')
                ->factory()
                ->getTTL() * 60,
        ];
    }

    public function usuario()
    {
        return Auth::guard('api')->user();
    }

    public function logout(): void
    {
        Auth::guard('api')->logout();
    }

    public function refresh(): array
    {
        $token = Auth::guard('api')->refresh();

        return [
            'usuario' => Auth::guard('api')->user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')
                ->factory()
                ->getTTL() * 60,
        ];
    }
}
