<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}