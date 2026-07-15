<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $cartItems = $request->user()
            ->activeCartItems()
            ->with('product')
            ->latest()
            ->get();

        $total = $cartItems->sum(fn ($item) => $item->price * $item->quantity);

        return response()->json([
            'items' => CartResource::collection($cartItems),
            'total' => $total,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        $quantity = $request->input('quantity', 1);
        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token');

        if (!$user && !$guestToken) {
            return $this->errorResponse('Guest token required', 422);
        }

        $price = $product->discount
            ? round($product->price - ($product->price * $product->discount) / 100, 2)
            : $product->price;

        $query = Cart::where('product_id', $product->id)->whereNull('order_id');

        $user
            ? $query->where('user_id', $user->id)
            : $query->where('guest_token', $guestToken)->whereNull('user_id');

        $cartItem = $query->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;

            if ($newQuantity > $product->stock) {
                return $this->errorResponse('Insufficient stock', 422);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'amount' => $price * $newQuantity,
            ]);
        } else {
            if ($quantity > $product->stock) {
                return $this->errorResponse('Insufficient stock', 422);
            }

            Cart::create([
                'user_id'     => $user?->id,
                'guest_token' => $user ? null : $guestToken,
                'product_id'  => $product->id,
                'quantity'    => $quantity,
                'price'       => $price,
                'amount'      => $price * $quantity,
            ]);
        }

        return $this->successResponse(null, 'Product added to cart', 200);
    }

    public function updateQuantity(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cart->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $cart->update([
            'quantity' => $request->input('quantity'),
            'amount' => $cart->price * $request->input('quantity'),
        ]);

        return $this->successResponse('Cart updated');
    }

    public function removeFromCart(Request $request, Cart $cart)
    {
        if ($cart->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $cart->delete();

        return $this->successResponse('Product removed from cart');
    }

    public function clearCart(Request $request)
    {
        $request->user()->cartItems()->delete();

        return $this->successResponse('Cart cleared');
    }
}