<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\NovoUsuarioRequest; 
use App\Http\Requests\AtualizarUsuarioRequest; 
use App\Http\Resources\UsuarioResource;

class UsuarioController extends Controller
{
    // Lista todos os usuários com filtros opcionais
    public function index(Request $request)
    {
        $query =  User::with(['role', 'addresses']);

        // 🔹 Filtro por nome
        if ($request->has('name') && $request->name != '') {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // 🔹 Filtro por CPF
        if ($request->has('cpf') && $request->cpf != '') {
            $query->where('cpf', 'like', '%' . $request->cpf . '%');
        }

        // 🔹 Filtro por período de criação
        if ($request->has('date_start') && $request->has('date_end') &&
            $request->date_start != '' && $request->date_end != '') {
            $query->whereDate('created_at', '>=', $request->date_start);
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        // Define o número de itens por página, usando o valor da requisição (ou 5 como padrão)
        $perPage = $request->input('per_page', 5); 

        // Aplica Paginação com o valor dinâmico
        $users = $query->orderBy('id', 'asc')->paginate($perPage); 

        // Retorna a coleção paginada usando o UserResource
        return UsuarioResource::collection($users);
    }

    // Cria um novo usuário
    public function store(NovoUsuarioRequest $request) 
    {
        try {
            $user = User::create($request->only(['name', 'email', 'cpf', 'role_id']));

            if ($request->has('addresses') && count($request->addresses) > 0) {
                 $user->addresses()->createMany($request->addresses);
            }

            return new UsuarioResource($user->load(['role', 'addresses']));

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao cadastrar: ' . $e->getMessage()], 500);
        }
    }

    //Exibe um usuário específico
    public function show(User $user)
    {
        return new UsuarioResource($user->load(['role', 'addresses']));
    }

    //Atualiza os dados de um usuário existente
    public function update(AtualizarUsuarioRequest $request, User $user) 
    {
        try {
            $oldAddressIds = $user->addresses()->pluck('address_id')->toArray();

            $user->update($request->only(['name', 'email', 'cpf', 'role_id']));
            
            if ($request->has('addresses')) {
                $user->addresses()->detach();
                $user->addresses()->createMany($request->addresses);
            }

            // Limpeza de endereços órfãos
            if (!empty($oldAddressIds)) {
                $stillLinkedIds = DB::table('address_user')
                                 ->whereIn('address_id', $oldAddressIds)
                                 ->pluck('address_id')
                                 ->unique()
                                 ->toArray();
                
                $idsToDelete = array_diff($oldAddressIds, $stillLinkedIds);

                if (!empty($idsToDelete)) {
                    \App\Models\Address::whereIn('id', $idsToDelete)->delete();
                }
            }

            return new UsuarioResource($user->load(['role', 'addresses']));

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar: ' . $e->getMessage()], 500);
        }
    }

    //Exclui um usuário
    public function destroy(User $user) 
    {
        $oldAddressIds = $user->addresses()->pluck('address_id')->toArray();

        $user->delete();
        
        // Limpeza de endereços órfãos após exclusão do usuário
        if (!empty($oldAddressIds)) {
            $stillLinkedIds = DB::table('address_user')
                                 ->whereIn('address_id', $oldAddressIds)
                                 ->pluck('address_id')
                                 ->unique()
                                 ->toArray();
            
            $idsToDelete = array_diff($oldAddressIds, $stillLinkedIds);

            if (!empty($idsToDelete)) {
                \App\Models\Address::whereIn('id', $idsToDelete)->delete();
            }
        }
        
        return response()->json(null, 204);
    }
}