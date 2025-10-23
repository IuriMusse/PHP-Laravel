<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    //Lista todos os perfis
    public function index() 
    {
        return response()->json(Role::all(), 200);
    }

    //Cria um novo perfil
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $profile = Role::create($request->all());
        return response()->json($profile, 201);
    }

    //Exibe um perfil específico
    public function show(Role $role) 
    {
        return response()->json($role, 200);
    }

    //Atualiza um perfil existente
    public function update(Request $request, Role $role)
    {
        $role->update($request->all());
        return response()->json($role, 200);
    }

    //Exclui um perfil.
    public function destroy(Role $role) 
    {
        $role->delete();
        return response()->json(null, 204);
    }
}
