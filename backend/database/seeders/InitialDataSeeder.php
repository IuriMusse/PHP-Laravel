<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    /**
     * Execute as sementes do banco de dados
     * Insere o perfil, o usuário e o endereço inicial
     */
    public function run(): void
    {
        // Limpar tabelas para garantir um estado inicial limpo (boa prática)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Role::truncate();
        Address::truncate();
        DB::table('address_user')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Inserir Perfis
        $role = Role::create([
            'name' => 'Administrador',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Inserir Usuários (Vinculado ao Perfil Admin)
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario_teste@gmail.com',
            'cpf' => '16125868567',
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Inserir Endereços
        $address = Address::create([
            'street' => 'Rua Teste para o desafio',
            'number' => '01',
            'city' => 'Salvador',
            'state' => 'BA',
            'zip' => '41000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 4. Vincular Endereço ao Usuário (N:N)
        $user->addresses()->attach($address->id); 
        
        $this->command->info('✅ Dados iniciais (Perfil, Usuário, Endereço) foram inseridos via Seeder.');
    }
}