<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | BrasilAPI Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com a BrasilAPI
    |
    */

    'brasilapi' => [
        'base_url' => env('BRASILAPI_BASE_URL', 'https://brasilapi.com.br/api'),
        'timeout' => env('BRASILAPI_TIMEOUT', 15),
        'verify_ssl' => env('BRASILAPI_VERIFY_SSL', false),
        'retry_attempts' => env('BRASILAPI_RETRY_ATTEMPTS', 3),
        'user_agent' => env('BRASILAPI_USER_AGENT', 'Laravel/10.0 BrasilAPI Client'),
    ],

];
