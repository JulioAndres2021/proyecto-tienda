<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCarrito extends Model
{
    protected $table = 'items_carrito';

    protected $fillable = [
        'carrito_id',
        'producto_id',
        'cantidad',
        'precio_unitario'
    ];

    public function carrito()
    {
        return $this->belongsTo(Carrito::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}