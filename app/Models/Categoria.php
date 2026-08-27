<?php

namespace App\Models;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion','usuario_id', 'actualizado_por',];


    /*
        categoria
    │
    ├── tiene varios productos
    │
    ├── pertenece a un usuario
    */
    public function productos()
    {
        return $this->hasMany(Producto::class);
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
