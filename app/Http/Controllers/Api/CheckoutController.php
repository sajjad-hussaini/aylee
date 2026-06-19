<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function applyPromoCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return $this->errorResponse('Cart is empty', 400);
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

        $promo = Coupon::where('code', $request->input('code'))->first();

        if (!$promo) {
            return $this->errorResponse('Invalid promo code', 404);
        }

        [$isValid, $message] = $promo->isValid($subtotal);

        if (!$isValid) {
            return $this->errorResponse($message, 422);
        }

        $discount = $promo->calculateDiscount($subtotal);
        $total = $subtotal - $discount;

        return response()->json([
            'message' => 'Promo code applied',
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'promo_code' => $promo->code,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string',
            'promo_code' => 'nullable|string',
        ]);

        $user = $request->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return $this->errorResponse('Cart is empty', 400);
        }

        // stock check pehle
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                return $this->errorResponse(
                    "Insufficient stock for {$item->product->name}", 422
                );
            }
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);
        $discount = 0;
        $promoCode = null;

        if ($request->filled('promo_code')) {
            $promo = Coupon::where('code', $request->input('promo_code'))->first();

            if (!$promo) {
                return $this->errorResponse('Invalid promo code', 404);
            }

            [$isValid, $message] = $promo->isValid($subtotal);

            if (!$isValid) {
                return $this->errorResponse($message, 422);
            }

            $discount = $promo->calculateDiscount($subtotal);
            $promoCode = $promo->code;
        }

        $total = $subtotal - $discount;

        $order = DB::transaction(function () use ($user, $cartItems, $subtotal, $discount, $total, $promoCode, $request, $promo ?? null) {

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'promo_code' => $promoCode,
                'shipping_address' => $request->input('shipping_address'),
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);

                // stock kam karen
                $item->product->decrement('stock', $item->quantity);
            }

            // promo usage count badhayen
            if (isset($promo)) {
                $promo->increment('used_count');
            }

            // cart clear karen
            $user->cartItems()->delete();

            return $order;
        });

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => new OrderResource($order->load('items.product')),
        ], 201);
    }
}
