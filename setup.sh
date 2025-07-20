#!/bin/bash

echo "🚀 Iniciando setup do Sistema de Gestão de Fornecedores..."
echo ""

# Verificar se o Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não está instalado. Por favor, instale o Docker primeiro."
    exit 1
fi

# Verificar se o Docker Compose está instalado
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose não está instalado. Por favor, instale o Docker Compose primeiro."
    exit 1
fi

echo "✅ Docker e Docker Compose encontrados"
echo ""

# Criar arquivo .env se não existir
if [ ! -f .env ]; then
    echo "📝 Criando arquivo .env..."
    cp .env.example .env
    echo "✅ Arquivo .env criado"
else
    echo "✅ Arquivo .env já existe"
fi

echo ""

# Parar containers existentes
echo "🛑 Parando containers existentes..."
docker-compose down

# Construir e iniciar containers
echo "🔨 Construindo e iniciando containers..."
docker-compose up -d --build

# Aguardar MySQL estar pronto
echo "⏳ Aguardando MySQL estar pronto..."
sleep 30

# Executar migrações
echo "🗄️ Executando migrações..."
docker-compose exec app php artisan migrate --force

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
docker-compose exec app php artisan key:generate --force

# Limpar cache
echo "🧹 Limpando cache..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

echo ""
echo "🎉 Setup concluído com sucesso!"
echo ""
echo "📋 Informações de acesso:"
echo "   🌐 Aplicação: http://localhost:8000"
echo "   🗄️  PHPMyAdmin: http://localhost:8080"
echo "   📊 MySQL: localhost:3306"
echo ""
echo "🔧 Comandos úteis:"
echo "   📝 Ver logs: docker-compose logs -f"
echo "   🛑 Parar: docker-compose down"
echo "   ▶️  Iniciar: docker-compose up -d"
echo "   🧪 Testar BrasilAPI: docker-compose exec app php artisan brasilapi:test"
echo ""
echo "📚 Documentação: README.md"
