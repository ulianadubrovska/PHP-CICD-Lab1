<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopCategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.form', [
            'category' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create($validated);

        return redirect()->route('shop.categories.index')
            ->with('success', 'Категорію успішно створено!');
    }

    public function edit(int $id): View
    {
        return view('categories.form', [
            'category' => Category::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')->ignore($id),
            ],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'slug')->ignore($id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return redirect()->route('shop.categories.index')
            ->with('success', 'Категорію успішно оновлено!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            return redirect()->route('shop.categories.index')
                ->with('error', 'Спочатку потрібно перемістити або видалити товари цієї категорії.');
        }

        $category->delete();

        return redirect()->route('shop.categories.index')
            ->with('success', 'Категорію успішно видалено!');
    }
}
