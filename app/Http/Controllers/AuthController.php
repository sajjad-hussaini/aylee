<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\FavoriteProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        
        $guestToken = $request->header('X-Guest-Token');

        if ($guestToken) {
            $this->mergeGuestCart($request, $user, $guestToken);
            $this->mergeGuestFavorites($request, $user, $guestToken);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Token create karo
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    private function mergeGuestCart(Request $request, $user, $guestToken)
    {
        $guestItems = Cart::where('guest_token', $guestToken)->get();

        foreach ($guestItems as $item) {
            $existing = Cart::where('user_id', $user->id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
                $item->delete();
            } else {
                $item->update(['user_id' => $user->id, 'guest_token' => null]);
            }
        }
    }

    private function mergeGuestFavorites(Request $request, $user, $guestToken)
    {
        $guestFavorites = FavoriteProduct::where('guest_token', $guestToken)->get();

        foreach ($guestFavorites as $fav) {
            $exists = FavoriteProduct::where('user_id', $user->id)
                ->where('product_id', $fav->product_id)
                ->exists();

            $exists ? $fav->delete() : $fav->update(['user_id' => $user->id, 'guest_token' => null]);
        }
    }
}
