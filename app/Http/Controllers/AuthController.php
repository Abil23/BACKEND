<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * 1. POST: Sign Up (Register User Baru)
     * URL: /api/v1/auth/signup
     */
    public function signup(Request $request)
    {
        // Validasi
        $request->validate([
            'username' => 'required|min:4|unique:users,username|unique:administrators,username',
            'password' => 'required|min:5'
        ]);

        // Buat User Baru
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password) // Enkripsi password
        ]);

        // Langsung Login (Dapat Token)
        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'role' => 'user',
            'message' => 'User registered successfully'
        ], 201);
    }

    /**
     * 2. POST: Sign In (Login)
     * URL: /api/v1/auth/signin
     */
    public function signin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->username;
        $password = $request->password;

        // Cek Administrator
        $admin = Administrator::where('username', $username)->first();
        if ($admin && Hash::check($password, $admin->password)) {
            $admin->tokens()->delete();
            $token = $admin->createToken('admin_token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'token' => $token,
                'role' => 'admin'
            ], 200);
        }

        // Cek User Biasa
        $user = User::where('username', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            $user->tokens()->delete();
            $token = $user->createToken('user_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'role' => 'user'
            ], 200);
        }

        return response()->json([
            'status' => 'invalid',
            'message' => 'Wrong username or password'
        ], 401);
    }
    
    /**
     * 3. POST: Sign Out (Logout)
     * URL: /api/v1/auth/signout
     */
    public function signout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success'], 200);
    }
}