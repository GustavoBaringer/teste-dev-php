<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\FornecedorRepositoryInterface;
use App\Services\BrasilApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FornecedorController extends Controller
{
    private BrasilApiService $brasilApiService;
    private FornecedorRepositoryInterface $fornecedorRepository;

    public function __construct(BrasilApiService $brasilApiService, FornecedorRepositoryInterface $fornecedorRepository)
    {
        $this->brasilApiService = $brasilApiService;
        $this->fornecedorRepository = $fornecedorRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $fornecedores = $this->fornecedorRepository->getAllPaginated(10);
        return response()->json($fornecedores);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'required|in:cpf,cnpj',
            'documento' => 'required|string|unique:fornecedors,documento',
            'nome_razao_social' => 'nullable|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dados = $validator->validated();

        if ($dados['tipo_documento'] === 'cnpj') {
            $dadosCnpj = $this->brasilApiService->buscarCnpj($dados['documento']);

            if ($dadosCnpj) {
                $dados = array_merge($dadosCnpj, $dados);
            } else {
                return response()->json([
                    'message' => 'CNPJ não encontrado na Receita Federal',
                    'documento' => $dados['documento']
                ], 404);
            }
        }

        $fornecedor = $this->fornecedorRepository->create($dados);
        return response()->json($fornecedor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $fornecedor = $this->fornecedorRepository->findByIdOrFail($id);
        return response()->json($fornecedor);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $fornecedor = $this->fornecedorRepository->findByIdOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_documento' => 'sometimes|required|in:cpf,cnpj',
            'documento' => 'sometimes|required|string|unique:fornecedors,documento,' . $id,
            'nome_razao_social' => 'sometimes|required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $this->fornecedorRepository->update($id, $validator->validated());
        $fornecedor = $this->fornecedorRepository->findByIdOrFail($id);
        return response()->json($fornecedor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $this->fornecedorRepository->delete($id);
        return response()->json(['message' => 'Fornecedor removido com sucesso']);
    }
}
