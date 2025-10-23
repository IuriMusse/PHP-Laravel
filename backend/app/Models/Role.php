<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    //Um perfil pode ter vários usuários
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
