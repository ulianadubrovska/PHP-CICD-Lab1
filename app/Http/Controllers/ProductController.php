<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->with(['category', 'reviews'])
            ->where('is_active', true)
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with([
            'category',
            'reviews.user',
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $product = Product::create($validated);
        $product->load('category');

        return response()->json([
            'status' => 'created',
            'data' => $product,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id',
            ],
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'sku' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $product->update($validated);
        $product->load('category');

        return response()->json([
            'status' => 'updated',
            'data' => $product,
        ]);
    }
}
