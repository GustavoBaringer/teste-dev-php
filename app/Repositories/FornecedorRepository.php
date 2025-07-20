<?php

namespace App\Repositories;

use App\Models\Fornecedor;
use App\Repositories\Interfaces\FornecedorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FornecedorRepository implements FornecedorRepositoryInterface
{
    protected Fornecedor $model;

    public function __construct(Fornecedor $model)
    {
        $this->model = $model;
    }

    /**
     * Buscar todos os fornecedores com paginação
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Buscar fornecedor por ID
     */
    public function findById(string $id): ?Fornecedor
    {
        return $this->model->find($id);
    }

    /**
     * Buscar fornecedor por ID ou falhar
     */
    public function findByIdOrFail(string $id): Fornecedor
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Buscar fornecedor por documento
     */
    public function findByDocumento(string $documento): ?Fornecedor
    {
        return $this->model->where('documento', $documento)->first();
    }

    /**
     * Verificar se documento já existe
     */
    public function documentoExists(string $documento, ?string $excludeId = null): bool
    {
        $query = $this->model->where('documento', $documento);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Criar novo fornecedor
     */
    public function create(array $data): Fornecedor
    {
        return $this->model->create($data);
    }

    /**
     * Atualizar fornecedor
     */
    public function update(string $id, array $data): bool
    {
        $fornecedor = $this->findByIdOrFail($id);
        return $fornecedor->update($data);
    }

    /**
     * Deletar fornecedor
     */
    public function delete(string $id): bool
    {
        $fornecedor = $this->findByIdOrFail($id);
        return $fornecedor->delete();
    }

    /**
     * Buscar fornecedores por tipo de documento
     */
    public function findByTipoDocumento(string $tipoDocumento): Collection
    {
        return $this->model->where('tipo_documento', $tipoDocumento)->get();
    }

    /**
     * Buscar fornecedores por cidade
     */
    public function findByCidade(string $cidade): Collection
    {
        return $this->model->where('cidade', 'like', "%{$cidade}%")->get();
    }

    /**
     * Buscar fornecedores por estado
     */
    public function findByEstado(string $estado): Collection
    {
        return $this->model->where('estado', $estado)->get();
    }

    /**
     * Buscar fornecedores com filtros
     */
    public function search(array $filters): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['tipo_documento'])) {
            $query->where('tipo_documento', $filters['tipo_documento']);
        }

        if (isset($filters['cidade'])) {
            $query->where('cidade', 'like', "%{$filters['cidade']}%");
        }

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (isset($filters['nome_razao_social'])) {
            $query->where('nome_razao_social', 'like', "%{$filters['nome_razao_social']}%");
        }

        if (isset($filters['nome_fantasia'])) {
            $query->where('nome_fantasia', 'like', "%{$filters['nome_fantasia']}%");
        }

        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }
}
