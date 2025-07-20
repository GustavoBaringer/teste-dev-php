<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrasilApiService
{
    private const CNPJ_ENDPOINT = '/cnpj/v1';

    private string $baseUrl;
    private int $timeout;
    private string $userAgent;

    public function __construct()
    {
        $this->baseUrl = config('services.brasilapi.base_url', 'https://brasilapi.com.br/api');
        $this->timeout = config('services.brasilapi.timeout', 15);
        $this->userAgent = config('services.brasilapi.user_agent', 'Laravel/10.0 BrasilAPI Client');
    }

    /**
     * Busca informações de um CNPJ na BrasilAPI
     */
    public function buscarCnpj(string $cnpj): ?array
    {
        $cnpjLimpo = $this->limparCnpj($cnpj);

        try {
            $response = Http::timeout($this->timeout + 5)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'application/json',
                ])
                ->withOptions([
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_FOLLOWLOCATION => true,
                    ]
                ])
                ->get($this->baseUrl . self::CNPJ_ENDPOINT . '/' . $cnpjLimpo);

            if ($response->successful()) {
                $data = $response->json();

                // Log da resposta para debug
                Log::info('BrasilAPI - CNPJ encontrado', [
                    'cnpj' => $cnpjLimpo,
                    'razao_social' => $data['razao_social'] ?? null,
                ]);

                return $this->formatarDadosCnpj($data);
            }

            if ($response->status() !== 0) {
                Log::warning('BrasilAPI - CNPJ não encontrado', [
                    'cnpj' => $cnpjLimpo,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('BrasilAPI - Erro na consulta', [
                'cnpj' => $cnpj,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }

        return null;
    }

    /**
     * Limpa o CNPJ removendo caracteres especiais
     */
    private function limparCnpj(string $cnpj): string
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    /**
     * Formata os dados retornados pela BrasilAPI
     */
    private function formatarDadosCnpj(array $data): array
    {
        return [
            'tipo_documento' => 'cnpj',
            'documento' => $data['cnpj'] ?? null,
            'nome_razao_social' => $data['razao_social'] ?? null,
            'nome_fantasia' => $data['nome_fantasia'] ?? null,
            'email' => $data['email'] ?? null,
            'telefone' => $data['ddd_telefone_1'] ?? null,
            'cep' => $data['cep'] ?? null,
            'endereco' => $data['logradouro'] ?? null,
            'numero' => $data['numero'] ?? null,
            'complemento' => $data['complemento'] ?? null,
            'bairro' => $data['bairro'] ?? null,
            'cidade' => $data['municipio'] ?? null,
            'estado' => $data['uf'] ?? null,
            'situacao_cadastral' => $data['situacao_cadastral'] ?? null,
            'data_abertura' => $data['data_inicio_atividade'] ?? null,
            'porte_empresa' => $data['porte']['descricao'] ?? null,
            'natureza_juridica' => $data['natureza_juridica']['descricao'] ?? null,
        ];
    }

    /**
     * Valida se o CNPJ está no formato correto
     */
    public function validarFormatoCnpj(string $cnpj): bool
    {
        $cnpjLimpo = $this->limparCnpj($cnpj);
        return strlen($cnpjLimpo) === 14;
    }
}
