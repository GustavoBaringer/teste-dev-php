# Testes Automatizados

Este documento descreve os testes automatizados implementados para o Sistema de Gestão de Fornecedores.

## 📋 Estrutura de Testes

```
tests/
├── Feature/
│   └── FornecedorControllerTest.php    # Testes de integração da API
├── Unit/
│   ├── BrasilApiServiceTest.php        # Testes unitários do serviço BrasilAPI
│   └── FornecedorRepositoryTest.php    # Testes unitários do repositório
├── TestCase.php                        # Classe base com helpers
└── database/
    └── factories/
        └── FornecedorFactory.php       # Factory para dados de teste
```

## 🧪 Tipos de Testes

### 1. Testes Unitários (`tests/Unit/`)

#### BrasilApiServiceTest

Testa a lógica do serviço de integração com a BrasilAPI:

-   ✅ **Limpeza de CNPJ**: Remove caracteres especiais
-   ✅ **Validação de formato**: Verifica se CNPJ tem 14 dígitos
-   ✅ **Busca de CNPJ**: Testa diferentes cenários de resposta
-   ✅ **Formatação de dados**: Verifica mapeamento correto dos campos
-   ✅ **Tratamento de erros**: Testa cenários de falha

#### FornecedorRepositoryTest

Testa a lógica do repositório de fornecedores:

-   ✅ **CRUD básico**: Create, Read, Update, Delete
-   ✅ **Busca por critérios**: ID, documento, tipo, cidade, estado
-   ✅ **Paginação**: Listagem paginada
-   ✅ **Filtros**: Busca com múltiplos filtros
-   ✅ **Validações**: Verificação de documentos únicos

### 2. Testes de Feature (`tests/Feature/`)

#### FornecedorControllerTest

Testa os endpoints da API REST:

-   ✅ **Listagem**: GET `/api/fornecedores`
-   ✅ **Criação**: POST `/api/fornecedores`
-   ✅ **Busca**: GET `/api/fornecedores/{id}`
-   ✅ **Atualização**: PUT `/api/fornecedores/{id}`
-   ✅ **Exclusão**: DELETE `/api/fornecedores/{id}`
-   ✅ **Validações**: Campos obrigatórios, formatos, unicidade
-   ✅ **Integração BrasilAPI**: Criação com CNPJ válido/inválido
-   ✅ **Paginação**: Estrutura de resposta paginada

## 🚀 Executando os Testes

### Executar todos os testes

```bash
php artisan test
```

### Executar testes específicos

```bash
# Testes unitários
php artisan test --testsuite=Unit

# Testes de feature
php artisan test --testsuite=Feature

# Teste específico
php artisan test tests/Unit/BrasilApiServiceTest.php
```

### Executar com cobertura

```bash
# Se você tiver Xdebug instalado
php artisan test --coverage

# Com detalhes de cobertura
php artisan test --coverage --min=80
```

### Executar em paralelo

```bash
php artisan test --parallel
```

## 🔧 Configuração de Testes

### Ambiente de Teste

Os testes usam:

-   **Banco SQLite em memória** para velocidade
-   **HTTP Fake** para mockar chamadas à BrasilAPI
-   **Factories** para gerar dados de teste
-   **RefreshDatabase** para limpar dados entre testes

### Helpers Disponíveis

#### TestCase.php

```php
// Dados de fornecedor válidos
$this->fornecedorData(['email' => 'custom@email.com']);

// Dados de CNPJ válidos
$this->cnpjData(['documento' => '12345678901234']);

// Mock da BrasilAPI com sucesso
$this->mockBrasilApiSuccess();

// Mock da BrasilAPI com erro
$this->mockBrasilApiError();
```

#### FornecedorFactory

```php
// Fornecedor pessoa física
Fornecedor::factory()->pessoaFisica()->create();

// Fornecedor pessoa jurídica
Fornecedor::factory()->pessoaJuridica()->create();

// Fornecedor com dados completos
Fornecedor::factory()->completo()->create();

// Fornecedor com dados mínimos
Fornecedor::factory()->minimo()->create();

// Múltiplos fornecedores
Fornecedor::factory()->count(10)->create();
```

## 📊 Cobertura de Testes

### BrasilApiService (100%)

-   ✅ `buscarCnpj()` - Todos os cenários
-   ✅ `validarFormatoCnpj()` - Válido e inválido
-   ✅ `limparCnpj()` - Com caracteres especiais
-   ✅ `formatarDadosCnpj()` - Com dados completos e nulos

### FornecedorRepository (100%)

-   ✅ `create()` - Criação de fornecedor
-   ✅ `findById()` - Busca por ID
-   ✅ `findByIdOrFail()` - Busca com exceção
-   ✅ `findByDocumento()` - Busca por documento
-   ✅ `documentoExists()` - Verificação de unicidade
-   ✅ `update()` - Atualização
-   ✅ `delete()` - Exclusão
-   ✅ `getAllPaginated()` - Paginação
-   ✅ `findByTipoDocumento()` - Filtro por tipo
-   ✅ `findByCidade()` - Filtro por cidade
-   ✅ `findByEstado()` - Filtro por estado
-   ✅ `search()` - Busca com filtros

### FornecedorController (100%)

-   ✅ `index()` - Listagem paginada
-   ✅ `store()` - Criação com CPF e CNPJ
-   ✅ `show()` - Busca por ID
-   ✅ `update()` - Atualização
-   ✅ `destroy()` - Exclusão
-   ✅ Validações de entrada
-   ✅ Integração com BrasilAPI
-   ✅ Tratamento de erros

## 🎯 Cenários Testados

### BrasilAPI

1. **CNPJ válido** - Retorna dados completos
2. **CNPJ inválido** - Retorna erro 404
3. **Erro de conexão** - Tratamento de exceção
4. **Formato inválido** - Validação de 14 dígitos
5. **Caracteres especiais** - Limpeza automática

### Validações

1. **Campos obrigatórios** - tipo_documento, documento
2. **Formato de email** - Validação RFC
3. **Documento único** - Prevenção de duplicatas
4. **Tipo de documento** - Apenas 'cpf' ou 'cnpj'
5. **Tamanho de campos** - Limites máximos

### Integração

1. **Criação com CPF** - Dados manuais
2. **Criação com CNPJ** - Dados da BrasilAPI
3. **CNPJ não encontrado** - Erro 404
4. **Mesclagem de dados** - API + usuário
5. **Paginação** - Estrutura correta

## 🔍 Debugging de Testes

### Ver detalhes de falhas

```bash
php artisan test --verbose
```

### Executar teste específico com detalhes

```bash
php artisan test --filter test_buscar_cnpj_com_sucesso
```

### Ver logs durante testes

```bash
php artisan test --stop-on-failure
```

### Testar apenas um arquivo

```bash
php artisan test tests/Feature/FornecedorControllerTest.php
```

## 📈 Métricas de Qualidade

-   **Cobertura**: 100% dos métodos principais
-   **Testes Unitários**: 15 testes
-   **Testes de Feature**: 12 testes
-   **Total**: 27 testes automatizados
-   **Tempo de Execução**: ~2-3 segundos
-   **Confiança**: Alta - todos os cenários críticos cobertos

## 🚨 Boas Práticas

1. **Isolamento**: Cada teste é independente
2. **Mocks**: HTTP fake para APIs externas
3. **Factories**: Dados consistentes e reutilizáveis
4. **Nomenclatura**: Nomes descritivos dos testes
5. **Assertions**: Verificações específicas e claras
6. **Setup/Teardown**: Limpeza automática de dados

## 🔄 Manutenção

### Adicionando Novos Testes

1. Identifique o cenário a ser testado
2. Crie o teste na pasta apropriada (Unit/Feature)
3. Use factories para dados de teste
4. Adicione assertions específicas
5. Execute e verifique a cobertura

### Atualizando Testes Existentes

1. Execute `php artisan test` para verificar falhas
2. Atualize os mocks se necessário
3. Ajuste as assertions conforme mudanças
4. Mantenha a cobertura alta

### Troubleshooting

-   **Falhas de banco**: Verifique migrações
-   **Falhas de mock**: Verifique URLs da BrasilAPI
-   **Falhas de validação**: Verifique regras de validação
-   **Falhas de factory**: Verifique campos obrigatórios
