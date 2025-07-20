<?php

namespace App\Repositories\Interfaces;

use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface FornecedorRepositoryInterface
{
    /**
     * Buscar todos os fornecedores com paginação
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator;

    /**
     * Buscar fornecedor por ID
     */
    public function findById(string $id): ?Fornecedor;

    /**
     * Buscar fornecedor por ID ou falhar
     */
    public function findByIdOrFail(string $id): Fornecedor;

    /**
     * Buscar fornecedor por documento
     */
    public function findByDocumento(string $documento): ?Fornecedor;

    /**
     * Verificar se documento já existe
     */
    public function documentoExists(string $documento, ?string $excludeId = null): bool;

    /**
     * Criar novo fornecedor
     */
    public function create(array $data): Fornecedor;

    /**
     * Atualizar fornecedor
     */
    public function update(string $id, array $data): bool;

    /**
     * Deletar fornecedor
     */
    public function delete(string $id): bool;

    /**
     * Buscar fornecedores por tipo de documento
     */
    public function findByTipoDocumento(string $tipoDocumento): Collection;

    /**
     * Buscar fornecedores por cidade
     */
    public function findByCidade(string $cidade): Collection;

    /**
     * Buscar fornecedores por estado
     */
    public function findByEstado(string $estado): Collection;

    /**
     * Buscar fornecedores com filtros
     */
    public function search(array $filters): LengthAwarePaginator;
}
