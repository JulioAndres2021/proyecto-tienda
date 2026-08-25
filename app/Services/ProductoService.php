<?php

namespace App\Services;

use App\DTOs\Producto\CreateProductoData;
use App\DTOs\Producto\UpdateProductoData;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;

class ProductoService
{
    public function listar(): Collection
    {
        return Producto::with('categoria')->get();
    }

    public function crear(
        CreateProductoData $data
    ): Producto {
        $producto = Producto::create(
            $data->toArray()
        );

        return $producto->load('categoria');
    }

    public function actualizar(
        Producto $producto,
        UpdateProductoData $data
    ): Producto {
        $producto->update(
            $data->toArray()
        );

        return $producto->load('categoria');
    }

    public function eliminar(
        Producto $producto
    ): void {
        $producto->delete();
    }
}