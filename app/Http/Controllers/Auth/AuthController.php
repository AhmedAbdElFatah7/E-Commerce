<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class AuthController extends Controller
{

    public function sign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|min:10|max:15|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'date' => 'required|date', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'date' => $request->date,
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json([
            'message' => 'player successfully registered',
            'user' => $user,
            'token' => $token,
        ], 201);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'The email or password is incorrect'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json([
            'message' => ' successfully ',
            'token' => $token,
        ], 200);
    }
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken()); 

            return response()->json(['message' => 'User successfully logged out']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    public function uploadImage(Request $request)
    {
        $user = User::find(auth()->id());
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();

        $user->image = $uploadedFileUrl;
        $user->save();
        return response()->json([
            'message' => 'Image uploaded successfully',
            'image_url' => $uploadedFileUrl
        ]);
    }

    }

