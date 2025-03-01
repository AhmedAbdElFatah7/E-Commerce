<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;


class CartController extends Controller
{
    public function addToCart(Request $request)
    {

        $valedator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'required',
        ]);
        if ($valedator->fails()) {
            return response()->json(['errors' => $valedator->errors()], 422);
        }

        $cartItem = Cart::where('user_id', auth()->id())
                        ->where('product_id', $request->product_id )
                        ->where('size', $request->size)
                        ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'size' => $request->size
            ]);
        }

        return response()->json(['message' => 'Product added to cart'], 200);
    }
    public function removeFromCart(Request $request)
    {
        $userId = auth()->id();
        $productId = $request->product_id;
        $size = $request->size;
    
        $cartItem = Cart::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->where('size', $size)
                        ->first();

    return response()->json(['size'=>$size,
        'product_id' => $productId, 'user_id' => $userId]);
        if (!$cartItem) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }
    
        $cartItem->delete();
    
        return response()->json(['message' => 'Product removed from cart'], 200);
    }

    public function viewCart()
    {
        $cartItems = Cart::where('user_id', auth()->id())->select( 'product_id', 'quantity', 'size')->with('product')->get();
        $totalItems = $cartItems->sum('quantity');
        return response()->json([
            'cart' => $cartItems,
            'total_items' => $totalItems,
        ]);
    }
}
