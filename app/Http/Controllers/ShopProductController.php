<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->withCount('reviews')
            ->orderByDesc('id')
            ->paginate(10);

        return view('catalog.index', compact('products'));
    }

    public function show(int $id): View
    {
        $product = Product::with([
            'category',
            'reviews.user',
        ])->findOrFail($id);

        return view('catalog.show', compact('product'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('catalog.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        Product::create($validated);

        return redirect()
            ->route('shop.catalog.index')
            ->with('success', 'Товар успішно додано!');
    }

    public function edit(int $id): View
    {
        $product = Product::findOrFail($id);
        $categories = Category::orderBy('name')->get();

        return view('catalog.edit', compact('product', 'categories'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $product->update($validated);

        return redirect()
            ->route('shop.catalog.index')
            ->with('success', 'Товар успішно оновлено!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        if ($product->orderItems()->exists()) {
            return redirect()
                ->route('shop.catalog.index')
                ->with('error', 'Не можна видалити товар, який є в замовленнях.');
        }

        $product->delete();

        return redirect()
            ->route('shop.catalog.index')
            ->with('success', 'Товар успішно видалено!');
    }
}
