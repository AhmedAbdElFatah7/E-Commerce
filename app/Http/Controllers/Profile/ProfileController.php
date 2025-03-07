<?php

namespace App\Http\Controllers\Profile;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'image' => $user->image,
            'location' => $user->Location,
            'address' => $address,
            'orders' => $orders
        ]);
    }
    public function update(Request $request)
    {
        $user = User::find(auth()->id());
        $firstName = $user->first_name;
        $lastName = $user->last_name;  
        $fullName = $firstName . ' ' . $lastName;
        $validate = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|min:10|max:15|unique:users,phone,' . auth()->id(),
            'street_address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);
        
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $location = Location::where('user_id', auth()->id())->first();
        if (!$location) {
            $location = new Location();
            $location->user_id = auth()->id();
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $location->full_name = $fullName; 
        $location->email = $request->email;

        $location->street_address = $request->street_address;
        $location->city = $request->city;
        $location->country = $request->country;

        $location->save();
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
        
        $image = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();

        $user->image = $image;
        $user->save();
        return response()->json([
            'message' => 'Image uploaded successfully',
            'user' => $user,
        ], 201);
    }
}
