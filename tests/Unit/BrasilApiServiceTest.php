<?php

namespace Tests\Unit;

use App\Services\BrasilApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BrasilApiServiceTest extends TestCase
{
    use RefreshDatabase;

    private BrasilApiService $brasilApiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->brasilApiService = new BrasilApiService();
    }

    /**
     * Testa a limpeza de CNPJ removendo caracteres especiais
     */
    public function test_limpar_cnpj_remove_caracteres_especiais(): void
    {
        $cnpjComCaracteres = '12.345.678/0001-95';
        $cnpjLimpo = $this->invokePrivateMethod($this->brasilApiService, 'limparCnpj', [$cnpjComCaracteres]);

        $this->assertEquals('12345678000195', $cnpjLimpo);
    }

    /**
     * Testa a validação de formato de CNPJ válido
     */
    public function test_validar_formato_cnpj_valido(): void
    {
        $cnpjValido = '12345678000195';
        $resultado = $this->brasilApiService->validarFormatoCnpj($cnpjValido);

        $this->assertTrue($resultado);
    }

    /**
     * Testa a validação de formato de CNPJ inválido
     */
    public function test_validar_formato_cnpj_invalido(): void
    {
        $cnpjInvalido = '123456789'; // Menos de 14 dígitos
        $resultado = $this->brasilApiService->validarFormatoCnpj($cnpjInvalido);

        $this->assertFalse($resultado);
    }

    /**
     * Testa a validação de formato de CNPJ com caracteres especiais
     */
    public function test_validar_formato_cnpj_com_caracteres_especiais(): void
    {
        $cnpjComCaracteres = '12.345.678/0001-95';
        $resultado = $this->brasilApiService->validarFormatoCnpj($cnpjComCaracteres);

        $this->assertTrue($resultado);
    }

    /**
     * Testa busca de CNPJ com resposta bem-sucedida
     */
    public function test_buscar_cnpj_com_sucesso(): void
    {
        $cnpj = '00000000000191';
        $dadosMock = [
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
        ];

        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/*' => Http::response($dadosMock, 200),
        ]);

        $resultado = $this->brasilApiService->buscarCnpj($cnpj);

        $this->assertNotNull($resultado);
        $this->assertEquals('cnpj', $resultado['tipo_documento']);
        $this->assertEquals('00000000000191', $resultado['documento']);
        $this->assertEquals('PETROLEO BRASILEIRO S A PETROBRAS', $resultado['nome_razao_social']);
        $this->assertEquals('PETROBRAS', $resultado['nome_fantasia']);
        $this->assertEquals('petrobras@petrobras.com.br', $resultado['email']);
        $this->assertEquals('21', $resultado['telefone']);
        $this->assertEquals('20031-912', $resultado['cep']);
        $this->assertEquals('AV REPUBLICA DO CHILE', $resultado['endereco']);
        $this->assertEquals('65', $resultado['numero']);
        $this->assertEquals('ANDAR 1 A 20', $resultado['complemento']);
        $this->assertEquals('CENTRO', $resultado['bairro']);
        $this->assertEquals('RIO DE JANEIRO', $resultado['cidade']);
        $this->assertEquals('RJ', $resultado['estado']);
        $this->assertEquals('ATIVA', $resultado['situacao_cadastral']);
        $this->assertEquals('1953-10-03', $resultado['data_abertura']);
        $this->assertEquals('GRANDE PORTE', $resultado['porte_empresa']);
        $this->assertEquals('SOCIEDADE ANONIMA', $resultado['natureza_juridica']);
    }

    /**
     * Testa busca de CNPJ com resposta de erro 404
     */
    public function test_buscar_cnpj_nao_encontrado(): void
    {
        $cnpj = '12345678901234';

        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/*' => Http::response(['error' => 'CNPJ não encontrado'], 404),
        ]);

        $resultado = $this->brasilApiService->buscarCnpj($cnpj);

        $this->assertNull($resultado);
    }

    /**
     * Testa busca de CNPJ com erro de conexão
     */
    public function test_buscar_cnpj_erro_conexao(): void
    {
        $cnpj = '12345678901234';

        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/*' => Http::response([], 500), // Erro de servidor
        ]);

        $resultado = $this->brasilApiService->buscarCnpj($cnpj);

        $this->assertNull($resultado);
    }

    /**
     * Testa busca de CNPJ com exceção
     */
    public function test_buscar_cnpj_com_excecao(): void
    {
        $cnpj = '12345678901234';

        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/*' => function () {
                throw new \Exception('Erro de rede');
            },
        ]);

        $resultado = $this->brasilApiService->buscarCnpj($cnpj);

        $this->assertNull($resultado);
    }

    /**
     * Testa formatação de dados com campos nulos
     */
    public function test_formatar_dados_cnpj_com_campos_nulos(): void
    {
        $dadosMock = [
            'cnpj' => '00000000000191',
            'razao_social' => 'EMPRESA TESTE',
            // Outros campos são nulos
        ];

        $resultado = $this->invokePrivateMethod($this->brasilApiService, 'formatarDadosCnpj', [$dadosMock]);

        $this->assertEquals('cnpj', $resultado['tipo_documento']);
        $this->assertEquals('00000000000191', $resultado['documento']);
        $this->assertEquals('EMPRESA TESTE', $resultado['nome_razao_social']);
        $this->assertNull($resultado['nome_fantasia']);
        $this->assertNull($resultado['email']);
        $this->assertNull($resultado['telefone']);
    }

    /**
     * Testa formatação de dados com arrays aninhados
     */
    public function test_formatar_dados_cnpj_com_arrays_aninhados(): void
    {
        $dadosMock = [
            'cnpj' => '00000000000191',
            'razao_social' => 'EMPRESA TESTE',
            'porte' => ['descricao' => 'MEDIO PORTE'],
            'natureza_juridica' => ['descricao' => 'LTDA'],
        ];

        $resultado = $this->invokePrivateMethod($this->brasilApiService, 'formatarDadosCnpj', [$dadosMock]);

        $this->assertEquals('MEDIO PORTE', $resultado['porte_empresa']);
        $this->assertEquals('LTDA', $resultado['natureza_juridica']);
    }

    /**
     * Helper para invocar métodos privados
     */
    private function invokePrivateMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
