<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $men = Product::where('category', 'men')
        ->where('trend',1)
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1')
        ->get();
        
        $women = Product::where('category', 'women')
        ->where('trend', 1)
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1')
        ->get();
        
        $top_rated = Product::where('rate', '>', 3.5)
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1')
        ->get();
        
        return response()->json( 
        ['men' =>$men ,
        'women' => $women ,
        'top_rated' => $top_rated,
        ] ,200 );
    }
    public function men(Request $request)
    {
        $men = Product::where('category', 'men')
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1','price' , 'name' , 'discount');
        $max_price = Product::where('category', 'men')->max('price');
        $low_price = 0; 
        if ($request->low_price) {
            $low_price = $request->low_price ; 
        }
        if ($request->max_price) {
            $max_price = $request->max_price ;
        }
        if ($request->category ) {
            $men = $men->where('sub_category', $request->category)
            ->where('price', '>=', $low_price)
            ->where('price', '<=', $max_price)
            ->paginate(12);

        } else {
            $men = $men->where('price', '>=', $low_price)
            ->where('price', '<=', $max_price)
            ->paginate(12);
        }

        return response()->json([
            'max_price' => $max_price , 
            'men' => $men
        ]);
    }
    public function women(Request $request)
    {
        $women = Product::where('category', 'women')    
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1','price' , 'name' , 'discount');
        $max_price = Product::where('category', 'women')->max('price');
        $low_price = 0; 
        if ($request->low_price) {
            $low_price = $request->low_price ; 
        }
        if ($request->max_price) {
            $max_price = $request->max_price ;
        }
        if ($request->category ) {
            $women = $women->where('sub_category', $request->category)
            ->where('price', '>=', $low_price)
            ->where('price', '<=', $max_price)
            ->paginate(12);

        } else {
            $women = $women->where('price', '>=', $low_price)
            ->where('price', '<=', $max_price)
            ->paginate(12);
        }

        return response()->json([
            'max_price' => $max_price , 
            'women' => $women
        ]);
    }
    public function topSelling(Request $request)
    {
        $max_price = Product::max('price');
        $low_price = 0 ; 
        if ($request->low_price) {
            $low_price = $request->low_price ; 
        }
        if ($request->max_price) {
            $max_price = $request->max_price ;
        }

        $products = Product::orderBy('sell', 'desc')
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1','price' , 'name' , 'discount')
        ->limit(10);

        $menProducts = Product::where('category', 'men')
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1','price' , 'name' , 'discount')
        ->orderBy('sell', 'desc')
        ->limit(5); 


        $womenProducts = Product::where('category', 'women')
        ->leftJoin('images', 'images.product_id', '=', 'products.id')
        ->select('products.id', 'images.image_1','price' , 'name' , 'discount')
        ->orderBy('sell', 'desc')
        ->limit(5);
    
        if ($request->category=='men') {
            $products = $menProducts ;
        }else if ($request->category=='women') {
            $products = $womenProducts ;
        }
        $products = $products->where('price', '>=', $low_price)
        ->where('price', '<=', $max_price)
        ->get();

        return response()->json([
            'max_price' => $max_price , 
            'message' => 'Top best-selling products',
            'products' => $products
            ]);
        }
        public function newest(Request $request)
        {
            $max_price = Product::max('price');
            $low_price = 0 ; 
            if ($request->low_price) {
                $low_price = $request->low_price ; 
            }
            if ($request->max_price) {
                $max_price = $request->max_price ;
            }
    
            $products = Product::orderBy('products.created_at', 'desc') 
            ->leftJoin('images', 'images.product_id', '=', 'products.id')
            ->select('products.id', 'images.image_1','price' , 'name' , 'discount')
            ->take(15);
    
            $menProducts = Product::where('category', 'men')
            ->leftJoin('images', 'images.product_id', '=', 'products.id')
            ->select('products.id', 'images.image_1','price' , 'name' , 'discount')
            ->orderBy('products.created_at', 'desc') 
            ->take(15);
    
            $womenProducts = Product::where('category', 'women')
            ->leftJoin('images', 'images.product_id', '=', 'products.id')
            ->select('products.id', 'images.image_1','price' , 'name' , 'discount')
            ->orderBy('products.created_at', 'desc') 
            ->take(15);
    
            if ($request->category=='men') {
                $products = $menProducts ;
            }else if ($request->category=='women') {
                $products = $womenProducts ;
            }
            $products = $products->where('price', '>=', $low_price)
            ->where('price', '<=', $max_price)
            ->get();
    
            return response()->json([
                'max_price' => $max_price , 
                'message' => 'Top best-selling products',
                'products' => $products
                ]);
        }
}

    