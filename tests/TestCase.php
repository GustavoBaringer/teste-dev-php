<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{

    /**
     * Configuração padrão para todos os testes
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Configurar HTTP fake para evitar chamadas reais à API
        Http::preventStrayRequests();

        // Configurar timezone para testes
        config(['app.timezone' => 'UTC']);
    }

    /**
     * Helper para criar dados de fornecedor válidos
     */
    protected function fornecedorData(array $overrides = []): array
    {
        return array_merge([
            'tipo_documento' => 'cpf',
            'documento' => '12345678901',
            'nome_razao_social' => 'João Silva',
            'email' => 'joao@email.com',
            'telefone' => '11999999999',
            'cep' => '01234-567',
            'endereco' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ], $overrides);
    }

    /**
     * Helper para criar dados de CNPJ válidos
     */
    protected function cnpjData(array $overrides = []): array
    {
        return array_merge([
            'tipo_documento' => 'cnpj',
            'documento' => '00000000000191',
        ], $overrides);
    }

    /**
     * Helper para mock da BrasilAPI
     */
    protected function mockBrasilApiSuccess(): void
    {
        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/*' => Http::response([
                'cnpj' => '00000000000191',
                'razao_social' => 'PETROLEO BRASILEIRO S A PETROBRAS',
                'nome_fantasia' => 'PETROBRAS',
                'email' => 'petrobras@petrobras.com.br',
                'ddd_telefone_1' => '21',
                'cep' => '20031-912',
                'logradouro' => 'AV REPUBLICA DO CHILE',
                'numero' => '65',
                'complemento' => 'ANDAR 1 A 20',
                'bairro' => 'CENTRO',
                'municipio' => 'RIO DE JANEIRO',
                'uf' => 'RJ',
                'situacao_cadastral' => 'ATIVA',
                'data_inicio_atividade' => '1953-10-03',
                'porte' => ['descricao' => 'GRANDE PORTE'],
                'natureza_juridica' => ['descricao' => 'SOCIEDADE ANONIMA'],
            ], 200),
        ]);
    }

    /**
     * Helper para mock da BrasilAPI com erro
     */
    protected function mockBrasilApiError(): void
    {
        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/*' => Http::response(['error' => 'CNPJ não encontrado'], 404),
        ]);
    }
}
