<?php

namespace App\Console\Commands;

use App\Services\BrasilApiService;
use Illuminate\Console\Command;

class TestBrasilApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brasilapi:test {cnpj? : CNPJ para testar (padrão: Petrobras)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a conexão com a BrasilAPI usando um CNPJ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cnpj = $this->argument('cnpj') ?? '00000000000191'; // CNPJ da Petrobras

        $this->info("Testando conexão com BrasilAPI...");
        $this->info("CNPJ: {$cnpj}");
        $this->newLine();

        $service = new BrasilApiService();

        $this->info("Iniciando consulta...");
        $startTime = microtime(true);

        try {
            $result = $service->buscarCnpj($cnpj);

            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            if ($result) {
                $this->info("✅ Conexão bem-sucedida! ({$duration}ms)");
                $this->newLine();

                $this->info("📋 Dados encontrados:");
                $this->table(
                    ['Campo', 'Valor'],
                    [
                        ['Razão Social', $result['nome_razao_social'] ?? 'N/A'],
                        ['Nome Fantasia', $result['nome_fantasia'] ?? 'N/A'],
                        ['Cidade', $result['cidade'] ?? 'N/A'],
                        ['Estado', $result['estado'] ?? 'N/A'],
                        ['Situação', $result['situacao_cadastral'] ?? 'N/A'],
                    ]
                );
            } else {
                $this->error("❌ CNPJ não encontrado na Receita Federal");
                $this->warn("Isso pode indicar que o CNPJ não existe ou está inativo.");
            }
        } catch (\Exception $e) {
            $this->error("❌ Erro na conexão:");
            $this->error($e->getMessage());

            $this->newLine();
            $this->warn("💡 Sugestões:");
            $this->warn("1. Verifique sua conexão com a internet");
            $this->warn("2. Verifique as configurações SSL no arquivo .env");
            $this->warn("3. Consulte a documentação em docs/BRASILAPI_SSL_FIX.md");
        }

        $this->newLine();
        $this->info("📝 Logs disponíveis em: storage/logs/laravel.log");
    }
}
