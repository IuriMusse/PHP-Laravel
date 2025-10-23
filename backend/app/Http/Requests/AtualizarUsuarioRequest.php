<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\RegraCPF;

class AtualizarUsuarioRequest extends FormRequest
{
    /**
     * Determine se o usuário está autorizado a fazer esta solicitação
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenha as regras de validação que se aplicam à solicitação.
     * Regras para o método update (edição).
     */
    public function rules(): array
    {
        // Obtém o ID do usuário da rota (o Model Binding garante que 'user' está disponível)
        $userId = $this->route('user')->id; 
        
        return [
            'name' => 'string|max:100',
            // O email/cpf deve ser único, exceto para o usuário atual
            'email' => 'email|unique:users,email,' . $userId,
            'cpf' => ['string', 'unique:users,cpf,' . $userId, new RegraCPF()],
            'role_id' => 'exists:roles,id',
            
            // Validação dos Endereços
            'addresses' => 'nullable|array',
            'addresses.*.street' => 'required_with:addresses|string|max:150',
            'addresses.*.number' => 'required_with:addresses|string|max:20',
            'addresses.*.city' => 'required_with:addresses|string|max:100',
            'addresses.*.state' => 'required_with:addresses|string|max:100',
            'addresses.*.zip' => 'required_with:addresses|string|max:20',
        ];
    }
}