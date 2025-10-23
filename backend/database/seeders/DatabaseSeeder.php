<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        // Chama o seeder de dados iniciais
        $this->call([
            InitialDataSeeder::class,
        ]);
    }
}
