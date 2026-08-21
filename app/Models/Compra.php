<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = [
        'carrito_id',
        'nombre_cliente',
        'email',
        'direccion_envio',
        'ciudad',
        'codigo_postal',
        'metodo_pago',
        'subtotal',
        'impuestos',
        'costo_envio',
        'total',
        'estado',
    ];

    /*
    Compra
    │
    └── tiene muchos DetalleCompra
    └── pertenece a carrito
    */
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    public function carrito()
    {
        return $this->belongsTo(Carrito::class);
    }
}
