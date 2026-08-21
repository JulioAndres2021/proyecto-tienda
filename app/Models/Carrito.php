<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $fillable = ['token', 'estado'];

    /*
        Carrito
    │
    ├── tiene muchos ItemCarrito
    ├── tiene un DatoCheckout
    └── puede tener Compras

    Compra
    │
    └── tiene muchos DetalleCompra
     */
    public function items()
    {
        return $this->hasMany(ItemCarrito::class);
    }

    public function datosCheckout()
    {
        return $this->hasOne(DatoCheckout::class);
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

}
