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

    protected $fillable = ['nombre', 'sku' ,'descripcion', 'precio', 'stock', 'categoria_id'];

    //Relacionamos con categorías, un producto pertenece a una categoria
    /*
        producto
    │
    ├── pertenece a categoria
    │
    ├── tiene varios itemscarrito
    */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function itemsCarrito()
    {
        return $this->hasMany(ItemCarrito::class);
    }

}