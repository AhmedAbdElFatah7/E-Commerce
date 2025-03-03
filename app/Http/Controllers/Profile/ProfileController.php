<?php

namespace App\Http\Controllers\Profile;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Order;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::find(auth()->id());
        $firstName = $user->first_name;
        $lastName = $user->last_name;  
        $fullName = $firstName . ' ' . $lastName;
        $address = Location::where('user_id', auth()->id())->first();
        $orders =Order::where('user_id', auth()->id())->get();
        return response()->json([
            'full_name' => $fullName,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email, 
            'phone' => $user->phone,
            'location' => $user->Location,
            'address' => $address,
            'orders' => $orders
        ]);
    }
    public function update(Request $request)
    {
        $user = User::find(auth()->id());
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->Location = $request->Location;
        $user->save();
        return response()->json([
            'message' => 'User details updated successfully',
            'user' => $user,
        ], 201);
    }
    public function uploadImage(Request $request)
    {
        $user = User::find(auth()->id());
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        return response()->json([
            'message' => 'Image uploaded successfully',
            'user' => $user,
        ], 201);
        $image = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();

        $user->image = $image;
        $user->save();
        return response()->json([
            'message' => 'Image uploaded successfully',
            'user' => $user,
        ], 201);
    }
}
