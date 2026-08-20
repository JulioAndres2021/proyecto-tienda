<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatoCheckout extends Model
{
    protected $table = 'datos_checkout';

    protected $fillable = [
        'carrito_id',
        'nombre_cliente',
        'email',
        'direccion_envio',
        'ciudad',
        'codigo_postal',
        'metodo_pago',
    ];

    /*
        datocheckout
    │
    ├── pertenece a carrito
    */
    public function carrito()
    {
        return $this->belongsTo(Carrito::class);
    }
}
