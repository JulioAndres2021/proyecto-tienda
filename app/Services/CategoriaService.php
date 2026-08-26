<?php

namespace App\Services;

use App\DTOs\Categoria\CreateCategoriaData;
use App\DTOs\Categoria\UpdateCategoriaData;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Collection;

class CategoriaService
{
    public function listar(): Collection
    {
        return Categoria::with([
            'usuario',
            'actualizadoPor',
        ])->get();
    }

    public function crear(CreateCategoriaData $data): Categoria 
    {
        $usuarioId = auth('api')->id();

        $categoria = Categoria::create([
            ...$data->toArray(),
            'usuario_id' => $usuarioId,
            'actualizado_por' => $usuarioId,
        ]);

        return $categoria->load([
            'usuario',
            'actualizadoPor',
        ]);
    }

    public function actualizar(Categoria $categoria, UpdateCategoriaData $data): Categoria 
    {
        $categoria->update([
            ...$data->toArray(),
            'actualizado_por' => auth('api')->id(),
        ]);

        return $categoria->load([
            'usuario',
            'actualizadoPor',
        ]);
    }


    public function eliminar(Categoria $categoria): void 
    {
        $categoria->delete();
    }
}