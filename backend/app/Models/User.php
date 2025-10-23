<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'role_id',
    ];

    //Um usuário pertence a um função (Role)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    //Um usuário pode ter vários endereços
    public function addresses()
    {
        return $this->belongsToMany(Address::class);
    }
}
