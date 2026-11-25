<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $reviews = Review::query()
            ->with(['user:id,name', 'product:id,name'])
            ->when(
                isset($validated['product_id']),
                fn ($query) => $query->where(
                    'product_id',
                    $validated['product_id']
                )
            )
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => Review::with(['user:id,name', 'product:id,name'])
                ->findOrFail($id),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $exists = Review::where('user_id', $validated['user_id'])
            ->where('product_id', $validated['product_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Review already exists for this product.',
            ], 422);
        }

        $review = Review::create($validated);
        $review->load(['user:id,name', 'product:id,name']);

        return response()->json([
            'status' => 'created',
            'data' => $review,
        ], 201);
    }
}
