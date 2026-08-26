<?php

namespace App\Models;

use App\Models\Carrito;
use App\Models\Categoria;
use App\Models\Producto;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Metodos para el JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /*
        User
    │
    ├── tiene muchas a categoria
    │
    ├── tiene muchos carritos
    │
    ├── tiene muchos productos
    */
    
    public function productos()
    {
        return $this->hasMany(Producto::class, 'usuario_id');
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'usuario_id');
    }

    public function carritos()
    {
        return $this->hasMany(Carrito::class, 'usuario_id');
    }


}
