<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', 'categoria_id'];
    
    //Relacionamos con categorías, un producto pertenece a una categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}