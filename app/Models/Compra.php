<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }
}
