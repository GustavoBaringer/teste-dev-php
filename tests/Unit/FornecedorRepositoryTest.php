<?php

namespace Tests\Unit;

use App\Models\Fornecedor;
use App\Repositories\FornecedorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FornecedorRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private FornecedorRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FornecedorRepository(new Fornecedor());
    }

    /**
     * Testa criação de fornecedor
     */
    public function test_create_fornecedor(): void
    {
        $dados = [
            'tipo_documento' => 'cpf',
            'documento' => '12345678901',
            'nome_razao_social' => 'João Silva',
            'email' => 'joao@email.com',
        ];

        $fornecedor = $this->repository->create($dados);

        $this->assertInstanceOf(Fornecedor::class, $fornecedor);
        $this->assertEquals('12345678901', $fornecedor->documento);
        $this->assertEquals('João Silva', $fornecedor->nome_razao_social);
        $this->assertDatabaseHas('fornecedors', $dados);
    }

    /**
     * Testa busca de fornecedor por ID
     */
    public function test_find_by_id(): void
    {
        $fornecedor = Fornecedor::factory()->create();

        $resultado = $this->repository->findById($fornecedor->id);

        $this->assertInstanceOf(Fornecedor::class, $resultado);
        $this->assertEquals($fornecedor->id, $resultado->id);
    }

    /**
     * Testa busca de fornecedor por ID inexistente
     */
    public function test_find_by_id_inexistente(): void
    {
        $resultado = $this->repository->findById(999);

        $this->assertNull($resultado);
    }

    /**
     * Testa busca de fornecedor por ID ou falhar
     */
    public function test_find_by_id_or_fail(): void
    {
        $fornecedor = Fornecedor::factory()->create();

        $resultado = $this->repository->findByIdOrFail($fornecedor->id);

        $this->assertInstanceOf(Fornecedor::class, $resultado);
        $this->assertEquals($fornecedor->id, $resultado->id);
    }

    /**
     * Testa busca de fornecedor por ID inexistente ou falhar
     */
    public function test_find_by_id_or_fail_inexistente(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->repository->findByIdOrFail(999);
    }

    /**
     * Testa busca de fornecedor por documento
     */
    public function test_find_by_documento(): void
    {
        $fornecedor = Fornecedor::factory()->create([
            'documento' => '12345678901',
        ]);

        $resultado = $this->repository->findByDocumento('12345678901');

        $this->assertInstanceOf(Fornecedor::class, $resultado);
        $this->assertEquals($fornecedor->id, $resultado->id);
    }

    /**
     * Testa busca de fornecedor por documento inexistente
     */
    public function test_find_by_documento_inexistente(): void
    {
        $resultado = $this->repository->findByDocumento('99999999999');

        $this->assertNull($resultado);
    }

    /**
     * Testa verificação de documento existente
     */
    public function test_documento_exists(): void
    {
        Fornecedor::factory()->create([
            'documento' => '12345678901',
        ]);

        $resultado = $this->repository->documentoExists('12345678901');

        $this->assertTrue($resultado);
    }

    /**
     * Testa verificação de documento inexistente
     */
    public function test_documento_not_exists(): void
    {
        $resultado = $this->repository->documentoExists('99999999999');

        $this->assertFalse($resultado);
    }

    /**
     * Testa verificação de documento existente excluindo ID
     */
    public function test_documento_exists_exclude_id(): void
    {
        $fornecedor1 = Fornecedor::factory()->create([
            'documento' => '12345678901',
        ]);

        $fornecedor2 = Fornecedor::factory()->create([
            'documento' => '98765432109',
        ]);

        $resultado = $this->repository->documentoExists('12345678901', $fornecedor1->id);

        $this->assertFalse($resultado);
    }

    /**
     * Testa atualização de fornecedor
     */
    public function test_update_fornecedor(): void
    {
        $fornecedor = Fornecedor::factory()->create();

        $dados = [
            'nome_razao_social' => 'Nome Atualizado',
            'email' => 'novo@email.com',
        ];

        $resultado = $this->repository->update($fornecedor->id, $dados);

        $this->assertTrue($resultado);
        $this->assertDatabaseHas('fornecedors', [
            'id' => $fornecedor->id,
            'nome_razao_social' => 'Nome Atualizado',
            'email' => 'novo@email.com',
        ]);
    }

    /**
     * Testa exclusão de fornecedor
     */
    public function test_delete_fornecedor(): void
    {
        $fornecedor = Fornecedor::factory()->create();

        $resultado = $this->repository->delete($fornecedor->id);

        $this->assertTrue($resultado);
        $this->assertDatabaseMissing('fornecedors', [
            'id' => $fornecedor->id,
        ]);
    }

    /**
     * Testa listagem paginada
     */
    public function test_get_all_paginated(): void
    {
        Fornecedor::factory()->count(15)->create();

        $resultado = $this->repository->getAllPaginated(10);

        $this->assertEquals(15, $resultado->total());
        $this->assertEquals(10, $resultado->perPage());
        $this->assertEquals(1, $resultado->currentPage());
        $this->assertEquals(2, $resultado->lastPage());
    }

    /**
     * Testa busca por tipo de documento
     */
    public function test_find_by_tipo_documento(): void
    {
        Fornecedor::factory()->pessoaFisica()->count(3)->create();
        Fornecedor::factory()->pessoaJuridica()->count(2)->create();

        $resultado = $this->repository->findByTipoDocumento('cpf');

        $this->assertCount(3, $resultado);
        $this->assertEquals('cpf', $resultado->first()->tipo_documento);
    }

    /**
     * Testa busca por cidade
     */
    public function test_find_by_cidade(): void
    {
        Fornecedor::factory()->create(['cidade' => 'São Paulo']);
        Fornecedor::factory()->create(['cidade' => 'Rio de Janeiro']);
        Fornecedor::factory()->create(['cidade' => 'São Paulo']);

        $resultado = $this->repository->findByCidade('São Paulo');

        $this->assertCount(2, $resultado);
        $this->assertEquals('São Paulo', $resultado->first()->cidade);
    }

    /**
     * Testa busca por estado
     */
    public function test_find_by_estado(): void
    {
        Fornecedor::factory()->create(['estado' => 'SP']);
        Fornecedor::factory()->create(['estado' => 'RJ']);
        Fornecedor::factory()->create(['estado' => 'SP']);

        $resultado = $this->repository->findByEstado('SP');

        $this->assertCount(2, $resultado);
        $this->assertEquals('SP', $resultado->first()->estado);
    }

    /**
     * Testa busca com filtros
     */
    public function test_search_with_filters(): void
    {
        Fornecedor::factory()->pessoaJuridica()->create([
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'nome_razao_social' => 'Empresa Teste',
        ]);

        Fornecedor::factory()->pessoaJuridica()->create([
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
            'nome_razao_social' => 'Outra Empresa',
        ]);

        $filtros = [
            'tipo_documento' => 'cnpj',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ];

        $resultado = $this->repository->search($filtros);

        $this->assertCount(1, $resultado);
        $this->assertEquals('São Paulo', $resultado->first()->cidade);
        $this->assertEquals('SP', $resultado->first()->estado);
    }

    /**
     * Testa busca com filtros vazios
     */
    public function test_search_without_filters(): void
    {
        Fornecedor::factory()->count(5)->create();

        $resultado = $this->repository->search([]);

        $this->assertCount(5, $resultado);
    }

    /**
     * Testa busca com filtro de nome
     */
    public function test_search_by_nome(): void
    {
        Fornecedor::factory()->create(['nome_razao_social' => 'Empresa ABC']);
        Fornecedor::factory()->create(['nome_razao_social' => 'Empresa XYZ']);
        Fornecedor::factory()->create(['nome_razao_social' => 'Outra Empresa']);
        Fornecedor::factory()->create(['nome_razao_social' => 'Companhia Teste']);

        $filtros = ['nome_razao_social' => 'Empresa'];

        $resultado = $this->repository->search($filtros);

        $this->assertCount(3, $resultado);
        $this->assertStringContainsString('Empresa', $resultado->first()->nome_razao_social);
        $this->assertStringContainsString('Empresa', $resultado->last()->nome_razao_social);
    }
}
