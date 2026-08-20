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
    │
    └── tiene un DatoCheckout
    */
    public function items()
    {
        return $this->hasMany(ItemCarrito::class);
    }

    public function datosCheckout()
    {
        return $this->hasOne(DatoCheckout::class);
    }

}
