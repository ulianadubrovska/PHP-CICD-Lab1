<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ]);

        $payments = Payment::with('order')
            ->where('order_id', $validated['order_id'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => Payment::with('order')->findOrFail($id),
        ]);
    }
}
