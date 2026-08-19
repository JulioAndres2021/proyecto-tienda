<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $fillable = ['token', 'estado'];

    public function items()
    {
        return $this->hasMany(ItemCarrito::class);
    }
}