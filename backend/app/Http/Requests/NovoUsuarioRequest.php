<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\RegraCPF;

class NovoUsuarioRequest extends FormRequest
{
    /**
     * Determine se o usuário está autorizado a fazer esta solicitação.
     * Retorna true pois a autorização deve ser feita antes de validar.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Obtenha as regras de validação que se aplicam à solicitação.
     * Regras para o método store (criação).
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'cpf' => ['required', 'string', 'unique:users', new RegraCPF()],
            'role_id' => 'required|exists:roles,id',
            
            // Validação dos Endereços (N:N)
            'addresses' => 'nullable|array',
            'addresses.*.street' => 'required_with:addresses|string|max:150',
            'addresses.*.number' => 'required_with:addresses|string|max:20',
            'addresses.*.city' => 'required_with:addresses|string|max:100',
            'addresses.*.state' => 'required_with:addresses|string|max:100',
            'addresses.*.zip' => 'required_with:addresses|string|max:20',
        ];
    }
}