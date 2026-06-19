<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with('product')
            ->latest()
            ->get();

        $total = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

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

        $user = $request->user();
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $product = Product::find($productId);

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        $cartItem = $user->cartItems()->where('product_id', $productId)->first();

        if ($cartItem) {
            // already cart me hai to quantity increase kar do
            $cartItem->increment('quantity', $quantity);
        } else {
            $user->cartItems()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => 2,
                'amount' => 2,
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

        $cart->update(['quantity' => $request->input('quantity')]);

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