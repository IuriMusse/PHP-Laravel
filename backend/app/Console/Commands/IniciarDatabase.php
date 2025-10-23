<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PDOException; // Importado para tratar o erro 1049

class IniciarDatabase extends Command
{
    /**
     * O nome e a assinatura do comando Artisan.
     * Exemplo: php artisan bd:resetar
     *
     * @var string
     */
    protected $signature = 'bd:iniciar';

    /**
     * A descrição do console command.
     *
     * @var string
     */
    protected $description = 'Limpa completamente o banco de dados, executa migrações e insere os dados iniciais (seeder).';

    /**
     * Execute o console command.
     */
    public function handle()
    {
        // Pega o nome do banco de dados configurado no .env
        $databaseName = config('database.connections.' . config('database.default') . '.database');
        $driver = config('database.default');

        $this->info('Iniciando o processo de reset completo do banco de dados...');

        // 1. Limpar cache de configuração, rotas e views para garantir um ambiente fresco
        $this->line('');
        $this->info('1. Limpando cache da aplicação...');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        // 2. CRIAÇÃO DO BANCO DE DADOS
        $this->line('');
        $this->info("2. Verificando e criando o banco de dados '{$databaseName}' (se necessário)...");
        
        try {
            // Cria uma conexão temporária de fallback, forçando a conexão ao DB 'mysql'
            // ou qualquer outro DB padrão que sempre existe, para que possamos executar o CREATE.
            $config = config('database.connections.' . $driver);
            
            // Tenta se conectar sem especificar o DB da aplicação (usa o default ou 'mysql')
            $tempConfig = array_merge($config, [
                'database' => $driver === 'mysql' ? 'mysql' : null, // Usa 'mysql' como DB genérico
            ]);

            // Usa a conexão Padrão do PDO para não ser afetado pelo problema de DB não existir
            $pdo = new \PDO(
                "{$driver}:host={$config['host']};port={$config['port']};",
                $config['username'],
                $config['password']
            );

            // Executa o CREATE DATABASE na conexão genérica
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            
            $this->info("   -> Banco de dados '{$databaseName}' garantido e criado (se ausente).");

        } catch (PDOException $e) {
            $this->error("   ❌ Erro de conexão/permissão ao tentar criar o banco de dados.");
            $this->comment("   Detalhe: Verifique as credenciais no .env. (DB_USERNAME, DB_PASSWORD)");
            
            // Se falhar aqui, não pod prosseguir para o migrate:fresh.
            return 1; 
        }

        // 3. Apaga todas as tabelas e executa as migrações (cria as tabelas novamente)
        $this->line('');
        $this->info('3. Recriando tabelas do banco de dados com migrate:fresh...');
        $this->call('migrate:fresh', ['--force' => true]);

        // 4. Executa todos os seeders (populando o banco com dados iniciais)
        $this->line('');
        $this->info('4. Populando o banco de dados com seeders...');
        $this->call('db:seed');

        $this->line('');
        $this->info('✅ Banco de dados resetado e populado com sucesso!');
        
        // 5. Executa e carrega o servidor local
        $this->line('');
        $this->comment('⏳ Banco de dados sendo carregado!!');
        $this->call('serve');
        

        return 0;
    }
}
