<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return new OrderResource($order->load('items.product'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return $this->errorResponse('This order cannot be cancelled', 422);
        }

        DB::transaction(function () use ($order) {
            // stock wapas add karen
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'cancelled']);
        });

        return $this->successResponse('Order cancelled successfully');
    }

    public function reorder(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $user = $request->user();

        foreach ($order->items as $item) {
            if ($item->product && $item->product->stock > 0) {
                $existing = $user->cartItems()->where('product_id', $item->product_id)->first();

                if ($existing) {
                    $existing->increment('quantity', $item->quantity);
                } else {
                    $user->cartItems()->create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ]);
                }
            }
        }

        return $this->successResponse('Items added to cart from previous order');
    }
}
