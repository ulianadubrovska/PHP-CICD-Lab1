<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

final class ApiEndpoints
{
    #[OA\Post(
        path: '/register',
        summary: 'Реєстрація користувача',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Уляна'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lab8@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Password123!'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'Password123!'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Користувача створено, повернуто Bearer-токен'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
            new OA\Response(response: 429, description: 'Забагато запитів'),
        ]
    )]
    public function register(): void {}

    #[OA\Post(
        path: '/login',
        summary: 'Вхід та отримання Bearer-токена',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lab8@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Password123!'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Успішний вхід, повернуто Bearer-токен'),
            new OA\Response(response: 422, description: 'Неправильні дані'),
            new OA\Response(response: 429, description: 'Забагато запитів'),
        ]
    )]
    public function login(): void {}

    #[OA\Get(
        path: '/me',
        summary: 'Дані авторизованого користувача',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Дані користувача'),
            new OA\Response(response: 401, description: 'Потрібна авторизація'),
        ]
    )]
    public function me(): void {}

    #[OA\Post(
        path: '/logout',
        summary: 'Вихід та відкликання токена',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Вихід виконано'),
            new OA\Response(response: 401, description: 'Потрібна авторизація'),
        ]
    )]
    public function logout(): void {}

    #[OA\Get(
        path: '/products',
        summary: 'Отримати список товарів',
        tags: ['Products'],
        responses: [
            new OA\Response(response: 200, description: 'Список товарів із пагінацією'),
        ]
    )]
    public function productsIndex(): void {}

    #[OA\Get(
        path: '/products/{id}',
        summary: 'Переглянути один товар',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Дані товару'),
            new OA\Response(response: 404, description: 'Товар не знайдено'),
        ]
    )]
    public function productsShow(): void {}

    #[OA\Post(
        path: '/products',
        summary: 'Створити новий товар',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: [
                    'name', 'sku', 'category_id',
                    'price', 'stock_quantity', 'is_active',
                ],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Жіноча футболка'),
                    new OA\Property(property: 'sku', type: 'string', example: 'API-LAB8-001'),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'description', type: 'string', example: 'Базова футболка'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 899.00),
                    new OA\Property(property: 'stock_quantity', type: 'integer', example: 15),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Товар створено'),
            new OA\Response(response: 401, description: 'Потрібна авторизація'),
            new OA\Response(response: 403, description: 'Доступ заборонено'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function productsStore(): void {}

    #[OA\Put(
        path: '/products/{id}',
        summary: 'Редагувати власний товар',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: [
                    'name', 'sku', 'category_id',
                    'price', 'stock_quantity', 'is_active',
                ],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Оновлена футболка'),
                    new OA\Property(property: 'sku', type: 'string', example: 'API-LAB8-001'),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'description', type: 'string', example: 'Оновлений опис'),
                    new OA\Property(property: 'price', type: 'number', example: 999.00),
                    new OA\Property(property: 'stock_quantity', type: 'integer', example: 10),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Товар оновлено'),
            new OA\Response(response: 401, description: 'Потрібна авторизація'),
            new OA\Response(response: 403, description: 'Це чужий товар'),
            new OA\Response(response: 404, description: 'Товар не знайдено'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function productsUpdate(): void {}

    #[OA\Delete(
        path: '/products/{id}',
        summary: 'Видалити власний товар',
        tags: ['Products'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Товар видалено'),
            new OA\Response(response: 401, description: 'Потрібна авторизація'),
            new OA\Response(response: 403, description: 'Це чужий товар'),
            new OA\Response(response: 404, description: 'Товар не знайдено'),
            new OA\Response(response: 409, description: 'Товар є в замовленнях'),
        ]
    )]
    public function productsDestroy(): void {}
}
