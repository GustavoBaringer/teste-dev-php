# Resolução de Problemas SSL com BrasilAPI

## Problema

O erro `cURL error 60: SSL peer certificate or SSH remote key was not OK` ocorre quando há problemas na validação do certificado SSL ao conectar com a BrasilAPI.

## Soluções Implementadas

### 1. Configuração no arquivo `.env`

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
# BrasilAPI Configuration
BRASILAPI_BASE_URL=https://brasilapi.com.br/api
BRASILAPI_TIMEOUT=15
BRASILAPI_VERIFY_SSL=false
BRASILAPI_RETRY_ATTEMPTS=3
BRASILAPI_USER_AGENT=Laravel/10.0 BrasilAPI Client
```

### 2. Configuração Única Otimizada

O serviço agora usa uma configuração única que combina:

-   **Headers personalizados**: User-Agent e Accept
-   **Configurações cURL avançadas**: SSL desabilitado e redirecionamento
-   **Timeout estendido**: Para maior confiabilidade

### 3. Configurações cURL

As seguintes configurações são aplicadas:

```php
CURLOPT_SSL_VERIFYPEER => false
CURLOPT_SSL_VERIFYHOST => false
CURLOPT_FOLLOWLOCATION => true
```

## Soluções Alternativas

### Para Ambiente de Desenvolvimento

Se ainda houver problemas, você pode:

1. **Atualizar certificados CA**:

    ```bash
    composer update
    ```

2. **Verificar se o PHP tem os certificados CA**:

    ```bash
    php -r "echo openssl_get_cert_locations()['default_cert_file'];"
    ```

3. **Baixar certificados CA manualmente**:

    ```bash
    curl -o cacert.pem https://curl.se/ca/cacert.pem
    ```

4. **Configurar o PHP para usar o certificado**:
   No `php.ini`:
    ```ini
    curl.cainfo = /path/to/cacert.pem
    ```

### Para Ambiente de Produção

Em produção, é recomendado:

1. **Manter verificação SSL habilitada**:

    ```env
    BRASILAPI_VERIFY_SSL=true
    ```

2. **Usar certificados válidos**:

    - Certifique-se de que o servidor tem certificados CA atualizados
    - Configure corretamente o `curl.cainfo` no PHP

3. **Monitorar logs**:
    - Verifique os logs do Laravel em `storage/logs/laravel.log`
    - Monitore as tentativas de conexão

## Logs

O serviço agora registra logs detalhados:

-   **Sucesso**: Log de informações sobre CNPJ encontrado
-   **Aviso**: Log quando CNPJ não é encontrado
-   **Erro**: Log detalhado quando todas as tentativas falham

## Teste

Para testar se a correção funcionou:

```bash
php artisan tinker
```

```php
$service = new App\Services\BrasilApiService();
$result = $service->buscarCnpj('00000000000191'); // CNPJ da Petrobras
dd($result);
```

## Notas Importantes

-   A desabilitação da verificação SSL deve ser usada apenas em desenvolvimento
-   Em produção, sempre mantenha a verificação SSL habilitada
-   Monitore os logs para identificar problemas de conectividade
-   Considere implementar um sistema de cache para reduzir chamadas à API
