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
        return Producto::with([
            'categoria',
            'usuario',
            'actualizadoPor',
        ])->get();
    }

    public function crear(CreateProductoData $data): Producto 
    {
        $usuarioId = auth('api')->id();

        $producto = Producto::create([
            ...$data->toArray(),
            'usuario_id' => $usuarioId,
            'actualizado_por' => $usuarioId,
        ]);

        return $producto->load([
            'categoria',
            'usuario',
            'actualizadoPor',
        ]);
    }

    public function actualizar(Producto $producto, UpdateProductoData $data): Producto 
    {
        $producto->update([
            ...$data->toArray(),
            'actualizado_por' => auth('api')->id(),
        ]);

        return $producto->load([
            'categoria',
            'usuario',
            'actualizadoPor',
        ]);
    }

    public function eliminar(
        Producto $producto
    ): void {
        $producto->delete();
    }
}