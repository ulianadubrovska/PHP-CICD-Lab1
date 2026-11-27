<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductApiController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $products = Product::query()
            ->with('category')
            ->withCount('reviews')
            ->orderByDesc('id')
            ->paginate(10);

        return ProductResource::collection($products);
    }

    public function show(int $id): ProductResource
    {
        $product = Product::with('category')
            ->withCount('reviews')
            ->findOrFail($id);

        return new ProductResource($product);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Product::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $product = $request->user()
            ->products()
            ->create($data);

        return (new ProductResource($product->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, int $id): ProductResource
    {
        $product = Product::findOrFail($id);

        Gate::authorize('update', $product);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $product->update($data);

        return new ProductResource(
            $product->refresh()->load('category')
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        Gate::authorize('delete', $product);

        if ($product->orderItems()->exists()) {
            return response()->json([
                'message' => 'Не можна видалити товар, який є в замовленнях.',
            ], 409);
        }

        $product->delete();

        return response()->json([
            'message' => 'Товар успішно видалено.',
        ]);
    }
}
