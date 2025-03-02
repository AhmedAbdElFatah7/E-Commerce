<?php

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $userId = auth()->id();
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $totalPrice = $cartItems->sum(function ($cartItem) {
            return $cartItem->product->price_after_discount * $cartItem->quantity;
        });

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => Str::uuid(),
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'size' => $cartItem->size,
                    'price' => $cartItem->product->price_after_discount,
                ]);
            }

            Cart::where('user_id', $userId)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order_number' => $order->order_number,
                'total_price' => $totalPrice
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout failed', 'error' => $e->getMessage()], 500);
        }
    }
}
