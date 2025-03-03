<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductController extends Controller
{
    public function uploadImage(Request $request)
    {
        $valedator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'image_1' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'image_2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_3' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_4' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($valedator->fails()) {
            return response()->json(['errors' => $valedator->errors()], 422);
        }
        $product=Product::find($request->product_id);
        $image_1 = cloudinary()->upload($request->file('image_1')->getRealPath())->getSecurePath();
        $image_2 = cloudinary()->upload($request->file('image_2')->getRealPath())->getSecurePath();
        $image_3 = cloudinary()->upload($request->file('image_3')->getRealPath())->getSecurePath();
        $image_4 = cloudinary()->upload($request->file('image_4')->getRealPath())->getSecurePath();

        $image = new Image ; 
        $image->image_1 = $image_1;
        $image->image_2 = $image_2;
        $image->image_3 = $image_3;
        $image->image_4 = $image_4;
        $image->product_id = $request->product_id;
        $image->save();   
        
        return response()->json([
            'message' => 'Image uploaded successfully',
            'image' => $image,
            'product' => $product
        ],201);
    }
    public function store(Request $request)
    {
        $valedator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'reviews' => 'nullable|integer|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'rate' => 'nullable|numeric|min:0|max:5',
            'sell' => 'nullable|integer|min:0',
            'trend' => 'nullable|integer',
            'category' => 'nullable|string|max:255',
            'sub_category' => 'nullable|string|max:255',
        ]);
        if ($valedator->fails()) {
            return response()->json(['errors' => $valedator->errors()], 422);
        }
        $product = Product::create($valedator->validated());

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }
}
