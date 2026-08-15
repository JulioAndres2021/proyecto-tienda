<?php

namespace App\Models;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    //
    use HasFactory;
    
    protected $fillable = ['nombre', 'sku' ,'descripcion', 'precio', 'stock', 'categoria_id'];

    //Relacionamos con categorías, un producto pertenece a una categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}