<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class UsuarioResource extends JsonResource
{
    /**
     * Transforme o recurso em uma matriz de array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->formatCpf($this->cpf), 
            'created_at' => $this->created_at->toDateTimeString(),
            'role_id' => $this->role_id,
            // Relações aninhadas
            'role' => $this->whenLoaded('role', function () {
                return [
                    'id' => $this->role->id,
                    'name' => $this->role->name,
                ];
            }),
            
            // Mapeia os endereços
            'addresses' => $this->whenLoaded('addresses', function () {
                return $this->addresses->map(function ($address) {
                    return [
                        'id' => $address->id,
                        'street' => $address->street,
                        'number' => $address->number,
                        'city' => $address->city,
                        'state' => $address->state,
                        'zip' => $address->zip, // Retorna o CEP sem máscara (apenas dígitos)
                    ];
                });
            }),
        ];
    }
    
    // Aplica a máscara de CPF
    protected function formatCpf($cpf)
    {
        if (!$cpf) return null;
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        }
        return $cpf;
    }
}