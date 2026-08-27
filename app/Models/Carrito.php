<?php

namespace App\Models;

use App\Models\Compra;
use App\Models\DatoCheckout;
use App\Models\ItemCarrito;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $fillable = ['token', 'estado', 'usuario_id'];

    /*
        Carrito
    │
    ├── tiene muchos ItemCarrito
    ├── tiene un DatoCheckout
    └── puede tener Compras

    Compra
    │
    └── tiene muchos DetalleCompra

    usuario
    │
    └── pertenece a un usuario
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

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

}
