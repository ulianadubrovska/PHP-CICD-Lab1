<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShopReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::with(['user', 'product'])
            ->latest()
            ->paginate(10);

        return view('reviews.index', compact('reviews'));
    }

    public function create(): View
    {
        return view('reviews.form', [
            'review' => null,
            'users' => User::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('reviews', 'product_id')
                    ->where('user_id', $request->input('user_id')),
            ],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        Review::create($validated);

        return redirect()->route('shop.reviews.index')
            ->with('success', 'Відгук успішно додано!');
    }

    public function edit(int $id): View
    {
        return view('reviews.form', [
            'review' => Review::findOrFail($id),
            'users' => User::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('reviews', 'product_id')
                    ->where('user_id', $request->input('user_id'))
                    ->ignore($review->id),
            ],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->update($validated);

        return redirect()->route('shop.reviews.index')
            ->with('success', 'Відгук успішно оновлено!');
    }

    public function destroy(int $id): RedirectResponse
    {
        Review::findOrFail($id)->delete();

        return redirect()->route('shop.reviews.index')
            ->with('success', 'Відгук успішно видалено!');
    }
}
