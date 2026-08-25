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
        return Categoria::all();
    }

    public function crear(CreateCategoriaData $data): Categoria 
    {
        return Categoria::create(
            $data->toArray()
        );
    }

    public function actualizar(Categoria $categoria, UpdateCategoriaData $data): Categoria 
    {
        $categoria->update(
            $data->toArray()
        );

        return $categoria->refresh();
    }

    public function eliminar(Categoria $categoria): void 
    {
        $categoria->delete();
    }
}