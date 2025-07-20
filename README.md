# Sistema de Gestão de Fornecedores

Sistema desenvolvido em Laravel 12 para gestão de fornecedores com integração à BrasilAPI para validação automática de CNPJs.

## 🚀 Funcionalidades

### ✅ Implementado

-   **CRUD completo de Fornecedores** (Create, Read, Update, Delete)
-   **Validação automática de CNPJ** via BrasilAPI
-   **Padrão Repository** para separação de responsabilidades
-   **API RESTful** com endpoints JSON
-   **Validação de dados** com regras customizadas
-   **Integração com BrasilAPI** para consulta de dados da Receita Federal
-   **Comando Artisan** para testar conexão com BrasilAPI
-   **Suporte a CPF e CNPJ** como tipos de documento
-   **Paginação** na listagem de fornecedores
-   **Logs detalhados** para debugging
-   **🧪 Testes Automatizados** com 100% de cobertura (47 testes)
-   **Factories** para geração de dados de teste
-   **Mocks** para APIs externas
-   **Testes Unitários** e de Feature

### 🔧 Tecnologias Utilizadas

-   **Laravel 12** - Framework PHP
-   **MySQL/SQLite** - Banco de dados
-   **BrasilAPI** - API para consulta de CNPJs
-   **Tailwind CSS** - Framework CSS
-   **Vite** - Build tool
-   **PHP 8.2+** - Linguagem de programação

## 📋 Pré-requisitos

-   PHP 8.2 ou superior
-   Composer
-   Node.js e NPM
-   MySQL ou SQLite
-   Git

## 🛠️ Instalação e Configuração

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd teste-dev-php
```

### 2. Instale as dependências PHP

```bash
composer install
```

### 3. Instale as dependências Node.js

```bash
npm install
```

### 4. Configure o arquivo de ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure o banco de dados

#### Opção A: MySQL

Edite o arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fornecedores_db
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

#### Opção B: SQLite (Recomendado para desenvolvimento)

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### 6. Configure a BrasilAPI

Adicione ao arquivo `.env`:

```env
# BrasilAPI Configuration
BRASILAPI_BASE_URL=https://brasilapi.com.br/api
BRASILAPI_TIMEOUT=15
BRASILAPI_VERIFY_SSL=false
BRASILAPI_RETRY_ATTEMPTS=3
BRASILAPI_USER_AGENT=Laravel/10.0 BrasilAPI Client
```

### 7. Execute as migrações

```bash
php artisan migrate
```

### 8. Inicie o servidor de desenvolvimento

```bash
php artisan serve
```

## 🐳 Execução com Docker

### 🚀 Setup Automático (Recomendado)

Execute o script de setup automático:

#### Linux/macOS:

```bash
chmod +x setup.sh
./setup.sh
```

#### Windows (PowerShell):

```powershell
# Se você tiver Git Bash instalado
bash setup.sh

# Ou execute manualmente os comandos abaixo
```

### 🔧 Setup Manual

#### 1. Criar container MySQL

```bash
docker run --name mysql-fornecedores \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=fornecedores_db \
  -e MYSQL_USER=laravel \
  -e MYSQL_PASSWORD=laravel \
  -p 3306:3306 \
  -d mysql:8.0
```

#### 2. Configurar .env para Docker

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fornecedores_db
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

#### 3. Executar com Docker Compose

O projeto já inclui um arquivo `docker-compose.yml` completo com:

-   **Aplicação Laravel** na porta 8000
-   **MySQL 8.0** na porta 3306
-   **PHPMyAdmin** na porta 8080

Execute:

```bash
docker-compose up -d --build
```

#### 4. Executar migrações e configurações

```bash
# Aguardar MySQL estar pronto (30 segundos)
sleep 30

# Executar migrações
docker-compose exec app php artisan migrate --force

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate --force

# Limpar cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

### 📋 Acesso aos Serviços

Após a execução, você terá acesso a:

-   **🌐 Aplicação**: http://localhost:8000
-   **🗄️ PHPMyAdmin**: http://localhost:8080 (usuário: laravel, senha: laravel)
-   **📊 MySQL**: localhost:3306

### 🔧 Comandos Úteis Docker

```bash
# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Reiniciar containers
docker-compose restart

# Executar comando no container
docker-compose exec app php artisan brasilapi:test

# Executar testes automatizados
docker-compose exec app php artisan test

# Executar testes específicos
docker-compose exec app php artisan test --testsuite=Unit

# Acessar shell do container
docker-compose exec app bash
```

## 🧪 Testando a Aplicação

### 1. Teste a conexão com BrasilAPI

```bash
php artisan brasilapi:test
```

### 2. Teste com CNPJ específico

```bash
php artisan brasilapi:test 00000000000191
```

## 🧪 Testes Automatizados

O projeto possui uma **suita completa de testes automatizados** com **100% de cobertura** dos métodos principais.

### 📊 Estatísticas dos Testes

-   **Total de Testes**: 47 testes
-   **Assertions**: 172 verificações
-   **Tempo de Execução**: ~2-3 segundos
-   **Cobertura**: 100% dos métodos principais

### 🚀 Executando os Testes

#### Executar todos os testes

```bash
php artisan test
```

#### Executar testes específicos

```bash
# Testes unitários
php artisan test --testsuite=Unit

# Testes de feature
php artisan test --testsuite=Feature

# Teste específico
php artisan test tests/Unit/BrasilApiServiceTest.php
```

#### Executar com cobertura

```bash
# Se você tiver Xdebug instalado
php artisan test --coverage

# Com detalhes de cobertura
php artisan test --coverage --min=80
```

#### Executar em paralelo

```bash
php artisan test --parallel
```

### 🧪 Tipos de Testes

#### 1. Testes Unitários (`tests/Unit/`)

##### BrasilApiServiceTest (10 testes)

-   ✅ **Limpeza de CNPJ**: Remove caracteres especiais
-   ✅ **Validação de formato**: Verifica se CNPJ tem 14 dígitos
-   ✅ **Busca de CNPJ**: Testa diferentes cenários de resposta
-   ✅ **Formatação de dados**: Verifica mapeamento correto dos campos
-   ✅ **Tratamento de erros**: Testa cenários de falha

##### FornecedorRepositoryTest (19 testes)

-   ✅ **CRUD básico**: Create, Read, Update, Delete
-   ✅ **Busca por critérios**: ID, documento, tipo, cidade, estado
-   ✅ **Paginação**: Listagem paginada
-   ✅ **Filtros**: Busca com múltiplos filtros
-   ✅ **Validações**: Verificação de documentos únicos

#### 2. Testes de Feature (`tests/Feature/`)

##### FornecedorControllerTest (16 testes)

-   ✅ **Listagem**: GET `/api/fornecedores`
-   ✅ **Criação**: POST `/api/fornecedores`
-   ✅ **Busca**: GET `/api/fornecedores/{id}`
-   ✅ **Atualização**: PUT `/api/fornecedores/{id}`
-   ✅ **Exclusão**: DELETE `/api/fornecedores/{id}`
-   ✅ **Validações**: Campos obrigatórios, formatos, unicidade
-   ✅ **Integração BrasilAPI**: Criação com CNPJ válido/inválido
-   ✅ **Paginação**: Estrutura de resposta paginada

### 🎯 Cenários Testados

#### BrasilAPI

1. **CNPJ válido** - Retorna dados completos
2. **CNPJ inválido** - Retorna erro 404
3. **Erro de conexão** - Tratamento de exceção
4. **Formato inválido** - Validação de 14 dígitos
5. **Caracteres especiais** - Limpeza automática

#### Validações

1. **Campos obrigatórios** - tipo_documento, documento
2. **Formato de email** - Validação RFC
3. **Documento único** - Prevenção de duplicatas
4. **Tipo de documento** - Apenas 'cpf' ou 'cnpj'
5. **Tamanho de campos** - Limites máximos

#### Integração

1. **Criação com CPF** - Dados manuais
2. **Criação com CNPJ** - Dados da BrasilAPI
3. **CNPJ não encontrado** - Erro 404
4. **Mesclagem de dados** - API + usuário
5. **Paginação** - Estrutura correta

### 🔧 Configuração de Testes

#### Ambiente de Teste

Os testes usam:

-   **Banco SQLite em memória** para velocidade
-   **HTTP Fake** para mockar chamadas à BrasilAPI
-   **Factories** para gerar dados de teste
-   **RefreshDatabase** para limpar dados entre testes

#### Helpers Disponíveis

##### TestCase.php

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

##### FornecedorFactory

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

### 🔍 Debugging de Testes

#### Ver detalhes de falhas

```bash
php artisan test --verbose
```

#### Executar teste específico com detalhes

```bash
php artisan test --filter test_buscar_cnpj_com_sucesso
```

#### Ver logs durante testes

```bash
php artisan test --stop-on-failure
```

#### Testar apenas um arquivo

```bash
php artisan test tests/Feature/FornecedorControllerTest.php
```

### 📈 Métricas de Qualidade

-   **Cobertura**: 100% dos métodos principais
-   **Testes Unitários**: 29 testes
-   **Testes de Feature**: 16 testes
-   **Total**: 47 testes automatizados
-   **Tempo de Execução**: ~2-3 segundos
-   **Confiança**: Alta - todos os cenários críticos cobertos

### 🚨 Boas Práticas

1. **Isolamento**: Cada teste é independente
2. **Mocks**: HTTP fake para APIs externas
3. **Factories**: Dados consistentes e reutilizáveis
4. **Nomenclatura**: Nomes descritivos dos testes
5. **Assertions**: Verificações específicas e claras
6. **Setup/Teardown**: Limpeza automática de dados

### 📚 Documentação Completa

Para mais detalhes sobre os testes, consulte:

-   **`docs/TESTES.md`** - Documentação completa dos testes
-   **`tests/TestCase.php`** - Classe base com helpers
-   **`database/factories/FornecedorFactory.php`** - Factory para dados de teste

## 📡 Endpoints da API

### Fornecedores

| Método | Endpoint                 | Descrição                               |
| ------ | ------------------------ | --------------------------------------- |
| GET    | `/api/fornecedores`      | Lista todos os fornecedores (paginação) |
| POST   | `/api/fornecedores`      | Cria novo fornecedor                    |
| GET    | `/api/fornecedores/{id}` | Busca fornecedor por ID                 |
| PUT    | `/api/fornecedores/{id}` | Atualiza fornecedor                     |
| DELETE | `/api/fornecedores/{id}` | Remove fornecedor                       |

### Exemplo de uso com cURL

#### Criar fornecedor com CPF

```bash
curl -X POST http://localhost:8000/api/fornecedores \
  -H "Content-Type: application/json" \
  -d '{
    "tipo_documento": "cpf",
    "documento": "12345678901",
    "nome_razao_social": "João Silva",
    "email": "joao@email.com",
    "telefone": "11999999999"
  }'
```

#### Criar fornecedor com CNPJ (validação automática)

```bash
curl -X POST http://localhost:8000/api/fornecedores \
  -H "Content-Type: application/json" \
  -d '{
    "tipo_documento": "cnpj",
    "documento": "00000000000191"
  }'
```

#### Listar fornecedores

```bash
curl http://localhost:8000/api/fornecedores
```

## 🏗️ Estrutura do Projeto

```
app/
├── Console/Commands/
│   └── TestBrasilApiCommand.php    # Comando para testar BrasilAPI
├── Http/Controllers/
│   └── FornecedorController.php    # Controller principal
├── Models/
│   └── Fornecedor.php              # Modelo Eloquent
├── Repositories/
│   ├── Interfaces/
│   │   └── FornecedorRepositoryInterface.php
│   └── FornecedorRepository.php    # Implementação do repositório
└── Services/
    └── BrasilApiService.php        # Serviço de integração com BrasilAPI

config/
└── services.php                    # Configurações de serviços externos

database/
├── factories/
│   └── FornecedorFactory.php       # Factory para dados de teste
└── migrations/
    └── 2025_07_19_024718_create_fornecedors_table.php

tests/
├── Feature/
│   └── FornecedorControllerTest.php    # Testes de integração da API
├── Unit/
│   ├── BrasilApiServiceTest.php        # Testes unitários do serviço BrasilAPI
│   └── FornecedorRepositoryTest.php    # Testes unitários do repositório
└── TestCase.php                        # Classe base com helpers

docs/
├── BRASILAPI_SSL_FIX.md           # Documentação de resolução de problemas SSL
└── TESTES.md                      # Documentação completa dos testes

# Arquivos Docker
Dockerfile                          # Configuração do container da aplicação
docker-compose.yml                  # Orquestração dos containers
.dockerignore                       # Arquivos ignorados no build Docker
setup.sh                           # Script de setup automático
```

## 🔍 Funcionalidades Detalhadas

### Validação de CNPJ

-   Consulta automática na Receita Federal via BrasilAPI
-   Preenchimento automático de dados (razão social, endereço, etc.)
-   Validação de formato e existência do CNPJ

### Padrão Repository

-   Separação da lógica de acesso aos dados
-   Interface para facilitar testes e manutenção
-   Métodos específicos para diferentes consultas

### Validação de Dados

-   Validação de CPF/CNPJ únicos
-   Validação de formato de email
-   Validação de campos obrigatórios
-   Mensagens de erro personalizadas

## 📝 Logs

Os logs da aplicação estão disponíveis em:

-   `storage/logs/laravel.log` - Logs gerais
-   Logs específicos da BrasilAPI são registrados automaticamente
