<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCatalogClient
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $expectedClient = 'clothing-store-demo';

        // Перевіряємо заголовок для POST-запитів.
        if ($request->isMethod('post')) {
            $client = $request->header('X-Store-Client');

            if ($client !== $expectedClient) {
                return response()->json([
                    'status' => 'access_denied',
                    'message' => 'Відсутній або неправильний заголовок X-Store-Client.',
                ], 403);
            }
        }

        // Передаємо запит далі до контролера.
        $response = $next($request);

        // Додаємо заголовок після виконання контролера.
        $response->headers->set(
            'X-Security-Policy',
            'Enforced'
        );

        return $response;
    }
}
