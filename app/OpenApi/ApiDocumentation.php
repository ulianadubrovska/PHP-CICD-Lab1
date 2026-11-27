<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Fashion Store REST API',
    description: 'Документація захищеного API інтернет-магазину одягу та аксесуарів. Лабораторна робота №8.'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000/api/v1',
    description: 'Локальний сервер Fashion Store'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    description: 'Персональний Bearer-токен Laravel Sanctum'
)]
final class ApiDocumentation {}
