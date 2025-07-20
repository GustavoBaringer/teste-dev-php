<?php

namespace Database\Factories;

use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fornecedor>
 */
class FornecedorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Fornecedor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipoDocumento = $this->faker->randomElement(['cpf', 'cnpj']);

        return [
            'tipo_documento' => $tipoDocumento,
            'documento' => $tipoDocumento === 'cpf'
                ? $this->faker->numerify('###########') // 11 dígitos para CPF
                : $this->faker->numerify('##############'), // 14 dígitos para CNPJ
            'nome_razao_social' => $this->faker->company(),
            'nome_fantasia' => $this->faker->optional()->company(),
            'email' => $this->faker->optional()->safeEmail(),
            'telefone' => $this->faker->optional()->numerify('###########'),
            'cep' => $this->faker->optional()->numerify('#####-###'),
            'endereco' => $this->faker->optional()->streetName(),
            'numero' => $this->faker->optional()->buildingNumber(),
            'complemento' => $this->faker->optional()->secondaryAddress(),
            'bairro' => $this->faker->optional()->citySuffix(),
            'cidade' => $this->faker->optional()->city(),
            'estado' => $this->faker->optional()->stateAbbr(),
        ];
    }

    /**
     * Indica que o fornecedor é uma pessoa física (CPF)
     */
    public function pessoaFisica(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo_documento' => 'cpf',
            'documento' => $this->faker->numerify('###########'),
            'nome_razao_social' => $this->faker->name(),
        ]);
    }

    /**
     * Indica que o fornecedor é uma pessoa jurídica (CNPJ)
     */
    public function pessoaJuridica(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo_documento' => 'cnpj',
            'documento' => $this->faker->numerify('##############'),
            'nome_razao_social' => $this->faker->company(),
        ]);
    }

    /**
     * Fornecedor com dados completos
     */
    public function completo(): static
    {
        return $this->state(fn(array $attributes) => [
            'nome_fantasia' => $this->faker->company(),
            'email' => $this->faker->safeEmail(),
            'telefone' => $this->faker->numerify('###########'),
            'cep' => $this->faker->numerify('#####-###'),
            'endereco' => $this->faker->streetName(),
            'numero' => $this->faker->buildingNumber(),
            'complemento' => $this->faker->secondaryAddress(),
            'bairro' => $this->faker->citySuffix(),
            'cidade' => $this->faker->city(),
            'estado' => $this->faker->stateAbbr(),
        ]);
    }

    /**
     * Fornecedor com dados mínimos
     */
    public function minimo(): static
    {
        return $this->state(fn(array $attributes) => [
            'nome_fantasia' => null,
            'email' => null,
            'telefone' => null,
            'cep' => null,
            'endereco' => null,
            'numero' => null,
            'complemento' => null,
            'bairro' => null,
            'cidade' => null,
            'estado' => null,
        ]);
    }
}
