<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    use ApiResponseTrait;

    public function applyPromoCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $cartItems = $user->activeCartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return $this->errorResponse('Cart is empty', 400);
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->price * $item->quantity);

        $promo = Coupon::where('code', $request->input('code'))->first();

        if (!$promo) {
            return $this->errorResponse('Invalid promo code', 404);
        }

        [$isValid, $message] = $promo->isValid($subtotal);

        if (!$isValid) {
            return $this->errorResponse($message, 422);
        }

        $discount = $promo->calculateDiscount($subtotal);
        $total = max(0, $subtotal - $discount);

        return $this->successResponse([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'promo_code' => $promo->code,
        ], 'Promo code applied', 200);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'nullable|string',
            'shipping_address.country' => 'nullable|string',
            'shipping_address.email' => 'nullable|email',
            'shipping_address.post_code' => 'nullable|string',
            'shipping_address.address2' => 'nullable|string',

            'promo_code' => 'nullable|string',
            'payment_method' => 'nullable|in:cod,paypal',
        ]);

        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token');

        // Get Cart Items
        if ($user) {
            $cartItems = $user->activeCartItems()
                ->with('product')
                ->get();
        } else {

            if (!$guestToken) {
                return $this->errorResponse('Guest token is required.', 401);
            }

            $cartItems = Cart::where('guest_token', $guestToken)
                ->with('product')
                ->get();
        }

        if ($cartItems->isEmpty()) {
            return $this->errorResponse('Cart is empty', 400);
        }

        // Check stock
        foreach ($cartItems as $item) {

            if (!$item->product) {
                return $this->errorResponse("Product not found.", 404);
            }

            if ($item->quantity > $item->product->stock) {
                return $this->errorResponse(
                    "Insufficient stock for {$item->product->title}",
                    422
                );
            }
        }

        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        $discount = 0;
        $promoCode = null;
        $promo = null;

        if ($request->filled('promo_code')) {

            $promo = Coupon::where('code', $request->promo_code)->first();

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

        $total = max(0, $subtotal - $discount);

        $shipping = $request->shipping_address;

        $order = DB::transaction(function () use (
            $user,
            $guestToken,
            $cartItems,
            $subtotal,
            $discount,
            $total,
            $promoCode,
            $promo,
            $shipping,
            $request
        ) {

            $order = Order::create([
                'user_id' => $user?->id,
                'guest_token' => $guestToken,

                'order_number' => 'ORD-' . strtoupper(Str::random(10)),

                'sub_total' => $subtotal,
                'coupon' => $discount,
                'total_amount' => $total,
                'quantity' => $cartItems->sum('quantity'),

                'payment_method' => $request->input('payment_method', 'cod'),
                'payment_status' => 'unpaid',
                'status' => 'new',

                'first_name' => $shipping['name'],
                'last_name' => $shipping['last_name'] ?? '',
                'email' => $shipping['email'] ?? ($user?->email ?? ''),
                'phone' => $shipping['phone'],
                'country' => $shipping['country'] ?? '',
                'city' => $shipping['city'] ?? '',
                'post_code' => $shipping['post_code'] ?? '',
                'address1' => $shipping['address'],
                'address2' => $shipping['address2'] ?? '',
            ]);

            foreach ($cartItems as $item) {

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->title,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            if ($promo) {
                $promo->increment('used_count');
            }

            // Clear Cart
            if ($user) {

                $user->activeCartItems()->delete();

            } else {

                Cart::where('guest_token', $guestToken)
                    ->where('status', 'active')
                    ->delete();
            }

            return $order;
        });

        return $this->successResponse([
            'order' => new OrderResource($order->load('items.product')),
            'promo_code' => $promoCode,
        ], 'Order placed successfully', 201);
    }
}
