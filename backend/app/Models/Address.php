<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'street',
        'number',
        'city',
        'state',
        'zip',
    ];

    //Um endereço pode pertencer a vários usuários
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}