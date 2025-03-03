<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Location;

class LocationController extends Controller
{
    public function checkout()
    {
        $userId = auth()->id();

        $cartItems = Cart::where('user_id', $userId)->with('product')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }
        $subtotal = $cartItems->sum(function ($cartItem) {
            return $cartItem->product->price_after_discount * $cartItem->quantity;
        });
        $shipping = 200; 
        $total = $subtotal + $shipping;

        return response()->json([
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);

    }
    public function storeLocation(Request $request)
    {
        $userId = auth()->id();
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string'
        ]);
        $location = Location::updateorCreate([
            'user_id' => $userId,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'street_address' => $request->street_address,
            'city' => $request->city,
            'country' => $request->country,
        ]);

        return response()->json([
            'message' => 'User details stored successfully',
            'location' => $location,
        ], 201);
    }
}
