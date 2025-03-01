<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $product = Product::where('products.id', $request->product_id)
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1', 'images.image_2', 'images.image_3', 'images.image_4', 'products.price', 'products.name', 'products.discount',
        'products.rate', 'products.reviews')
        ->first();
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json(['product' => $product,],
            200, );
    }

    public function store(Request $request)
    {
        $user = User::find(auth()->id());
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stars' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        Review::updateOrCreate(
            ['product_id' => $request->product_id, 'user_id' => $user->id], 
            ['stars' => $request->stars, 'comment' => $request->comment]
        );

        $averageRating = Review::where('product_id', $request->product_id)->avg('stars');
        Product::where('id', $request->product_id)->update(['rate' => round($averageRating, 2)]);

        $commentsCount = Review::where('product_id', $request->product_id)->count();
        Product::where('id', $request->product_id)->update(['reviews' => $commentsCount]);

        return response()->json([
            'message' => 'Review submitted successfully!',
            'average_rating' => round($averageRating, 2)
        ], 200);
    }
    public function getReviews(Request $request)
    {
        $reviews = Review::where('product_id', $request->product_id)->with('user')->latest()->get();
        
        return response()->json($reviews);
    }
}
