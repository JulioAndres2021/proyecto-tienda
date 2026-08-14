<?php

namespace App\Models;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
    use HasFactory;
    
    protected $fillable = ['nombre', 'descripcion'];

    //Relacionamos con productos, una categoría puede tener varios productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}