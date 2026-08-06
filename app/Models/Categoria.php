<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
    protected $fillable = ['nombre', 'descripcion'];
    
    //Relacionamos con productos, una categoría puede tener varios productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}