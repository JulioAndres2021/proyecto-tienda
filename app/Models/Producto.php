<?php

namespace App\Models;

use App\Models\Categoria;
use App\Models\ItemCarrito;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'nombre', 'sku' ,'descripcion', 'precio', 'stock', 'categoria_id','usuario_id',
        'actualizado_por',
    ];

    /*
        producto
    │
    ├── pertenece a categoria
    │
    ├── tiene varios itemscarrito
    │
    ├── pertenece a un usuario
    */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function itemsCarrito()
    {
        return $this->hasMany(ItemCarrito::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

}
