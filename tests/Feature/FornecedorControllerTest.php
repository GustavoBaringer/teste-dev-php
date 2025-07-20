<?php

namespace Tests\Feature;

use App\Models\Fornecedor;
use App\Services\BrasilApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FornecedorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Testa listagem de fornecedores
     */
    public function test_index_retorna_lista_de_fornecedores(): void
    {
        // Criar fornecedores de teste
        Fornecedor::factory()->count(3)->create();

        $response = $this->getJson('/api/fornecedores');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'tipo_documento',
                        'documento',
                        'nome_razao_social',
                        'nome_fantasia',
                        'email',
                        'telefone',
                        'cep',
                        'endereco',
                        'numero',
                        'complemento',
                        'bairro',
                        'cidade',
                        'estado',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'current_page',
                'per_page',
                'total',
            ]);
    }

    /**
     * Testa criação de fornecedor com CPF
     */
    public function test_store_cria_fornecedor_com_cpf(): void
    {
        $dados = [
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
        ];

        $response = $this->postJson('/api/fornecedores', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'tipo_documento' => 'cpf',
                'documento' => '12345678901',
                'nome_razao_social' => 'João Silva',
                'email' => 'joao@email.com',
            ]);

        $this->assertDatabaseHas('fornecedors', [
            'documento' => '12345678901',
            'nome_razao_social' => 'João Silva',
        ]);
    }

    /**
     * Testa criação de fornecedor com CNPJ (validação automática)
     */
    public function test_store_cria_fornecedor_com_cnpj(): void
    {
        // Mock da BrasilAPI para CNPJ válido
        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/00000000000191' => Http::response([
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

        $dados = [
            'tipo_documento' => 'cnpj',
            'documento' => '00000000000191',
        ];

        $response = $this->postJson('/api/fornecedores', $dados);

        $response->assertStatus(201)
            ->assertJson([
                'tipo_documento' => 'cnpj',
                'documento' => '00000000000191',
                'nome_razao_social' => 'PETROLEO BRASILEIRO S A PETROBRAS',
                'nome_fantasia' => 'PETROBRAS',
                'email' => 'petrobras@petrobras.com.br',
                'cidade' => 'RIO DE JANEIRO',
                'estado' => 'RJ',
            ]);

        $this->assertDatabaseHas('fornecedors', [
            'documento' => '00000000000191',
            'nome_razao_social' => 'PETROLEO BRASILEIRO S A PETROBRAS',
        ]);
    }

    /**
     * Testa criação de fornecedor com CNPJ não encontrado
     */
    public function test_store_cnpj_nao_encontrado(): void
    {
        // Mock para CNPJ não encontrado
        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/12345678901234' => Http::response(['error' => 'CNPJ não encontrado'], 404),
        ]);

        $dados = [
            'tipo_documento' => 'cnpj',
            'documento' => '12345678901234',
        ];

        $response = $this->postJson('/api/fornecedores', $dados);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'CNPJ não encontrado na Receita Federal',
                'documento' => '12345678901234',
            ]);

        $this->assertDatabaseMissing('fornecedors', [
            'documento' => '12345678901234',
        ]);
    }

    /**
     * Testa validação de dados obrigatórios
     */
    public function test_store_valida_dados_obrigatorios(): void
    {
        $response = $this->postJson('/api/fornecedores', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo_documento', 'documento']);
    }

    /**
     * Testa validação de tipo de documento inválido
     */
    public function test_store_valida_tipo_documento(): void
    {
        $dados = [
            'tipo_documento' => 'rg', // Tipo inválido
            'documento' => '12345678901',
        ];

        $response = $this->postJson('/api/fornecedores', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo_documento']);
    }

    /**
     * Testa validação de documento único
     */
    public function test_store_valida_documento_unico(): void
    {
        // Criar fornecedor existente
        Fornecedor::factory()->create([
            'documento' => '12345678901',
        ]);

        $dados = [
            'tipo_documento' => 'cpf',
            'documento' => '12345678901', // Documento já existe
            'nome_razao_social' => 'João Silva',
        ];

        $response = $this->postJson('/api/fornecedores', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['documento']);
    }

    /**
     * Testa validação de email
     */
    public function test_store_valida_email(): void
    {
        $dados = [
            'tipo_documento' => 'cpf',
            'documento' => '12345678901',
            'nome_razao_social' => 'João Silva',
            'email' => 'email-invalido', // Email inválido
        ];

        $response = $this->postJson('/api/fornecedores', $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Testa busca de fornecedor por ID
     */
    public function test_show_retorna_fornecedor(): void
    {
        $fornecedor = Fornecedor::factory()->create();

        $response = $this->getJson("/api/fornecedores/{$fornecedor->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $fornecedor->id,
                'documento' => $fornecedor->documento,
                'nome_razao_social' => $fornecedor->nome_razao_social,
            ]);
    }

    /**
     * Testa busca de fornecedor inexistente
     */
    public function test_show_fornecedor_inexistente(): void
    {
        $response = $this->getJson('/api/fornecedores/999');

        $response->assertStatus(404);
    }

    /**
     * Testa atualização de fornecedor
     */
    public function test_update_atualiza_fornecedor(): void
    {
        $fornecedor = Fornecedor::factory()->create();

        $dados = [
            'nome_razao_social' => 'Nome Atualizado',
            'email' => 'novo@email.com',
        ];

        $response = $this->putJson("/api/fornecedores/{$fornecedor->id}", $dados);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $fornecedor->id,
                'nome_razao_social' => 'Nome Atualizado',
                'email' => 'novo@email.com',
            ]);

        $this->assertDatabaseHas('fornecedors', [
            'id' => $fornecedor->id,
            'nome_razao_social' => 'Nome Atualizado',
            'email' => 'novo@email.com',
        ]);
    }

    /**
     * Testa atualização de fornecedor inexistente
     */
    public function test_update_fornecedor_inexistente(): void
    {
        $dados = [
            'nome_razao_social' => 'Nome Atualizado',
        ];

        $response = $this->putJson('/api/fornecedores/999', $dados);

        $response->assertStatus(404);
    }

    /**
     * Testa validação na atualização
     */
    public function test_update_valida_dados(): void
    {
        $fornecedor = Fornecedor::factory()->create();

        $dados = [
            'email' => 'email-invalido', // Email inválido
        ];

        $response = $this->putJson("/api/fornecedores/{$fornecedor->id}", $dados);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Testa exclusão de fornecedor
     */
    public function test_destroy_remove_fornecedor(): void
    {
        $fornecedor = Fornecedor::factory()->create();

        $response = $this->deleteJson("/api/fornecedores/{$fornecedor->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Fornecedor removido com sucesso',
            ]);

        $this->assertDatabaseMissing('fornecedors', [
            'id' => $fornecedor->id,
        ]);
    }

    /**
     * Testa exclusão de fornecedor inexistente
     */
    public function test_destroy_fornecedor_inexistente(): void
    {
        $response = $this->deleteJson('/api/fornecedores/999');

        $response->assertStatus(404);
    }

    /**
     * Testa paginação na listagem
     */
    public function test_index_paginacao(): void
    {
        // Criar mais fornecedores que o limite por página
        Fornecedor::factory()->count(15)->create();

        $response = $this->getJson('/api/fornecedores');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
                'last_page',
            ]);

        $data = $response->json();
        $this->assertEquals(1, $data['current_page']);
        $this->assertEquals(10, $data['per_page']);
        $this->assertEquals(15, $data['total']);
        $this->assertEquals(2, $data['last_page']);
    }
}
