<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Auth\LoginData;
use App\DTOs\Auth\RegisterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = RegisterData::fromArray(
            $request->validated()
        );

        $resultado = $this->authService
            ->registrar($data);

        return ApiResponse::success(
            new AuthResource($resultado),
            'Usuario registrado correctamente.',
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = LoginData::fromArray(
            $request->validated()
        );

        $resultado = $this->authService
            ->login($data);

        return ApiResponse::success(
            new AuthResource($resultado),
            'Inicio de sesión correcto.'
        );
    }

    public function me(): JsonResponse
    {
        $usuario = $this->authService
            ->usuario();

        return ApiResponse::success(
            new UserResource($usuario),
            'Usuario autenticado obtenido correctamente.'
        );
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return ApiResponse::success(
            null,
            'Sesión cerrada correctamente.'
        );
    }

    public function refresh(): JsonResponse
    {
        $resultado = $this->authService
            ->refresh();

        return ApiResponse::success(
            new AuthResource($resultado),
            'Token renovado correctamente.'
        );
    }
}
