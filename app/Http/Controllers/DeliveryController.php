<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ]);

        $deliveries = Delivery::with('order')
            ->where('order_id', $validated['order_id'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $deliveries,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => Delivery::with('order')->findOrFail($id),
        ]);
    }
}
