<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UsuarioController;

Route::apiResource('roles', PerfilController::class);
Route::apiResource('addresses', EnderecoController::class);
Route::apiResource('users', UsuarioController::class);
