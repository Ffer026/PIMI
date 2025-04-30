<?php

use Laravel\Sanctum\Sanctum;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\EncryptCookies;

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),

    'expiration' => null,

    'middleware' => [
        'verify_csrf_token' => VerifyCsrfToken::class,
        'encrypt_cookies' => EncryptCookies::class,
    ],
];