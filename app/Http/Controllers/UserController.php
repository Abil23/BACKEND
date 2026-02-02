<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 1. GET: List Users (Admin Only)
     * URL: /api/v1/users
     */
    public function index(Request $request)
    {
        // Cek apakah yang request ini Admin?
        if (!($request->user() instanceof Administrator)) {
            return response()->json(['status' => 'forbidden', 'message' => 'You are not an administrator'], 403);
        }

        $users = User::all(); // Ambil semua user
        return response()->json($users, 200);
    }

    /**
     * 2. PUT: Update User
     * URL: /api/v1/users/{id}
     * Akses: Admin boleh edit siapa saja, User cuma boleh edit diri sendiri.
     */
    public function update(Request $request, $id)
    {
        $targetUser = User::find($id);

        if (!$targetUser) {
            return response()->json(['status' => 'not_found', 'message' => 'User not found'], 404);
        }

        // --- CEK OTORITAS ---
        $is_admin = $request->user() instanceof Administrator;
        $is_self = $request->user()->id == $targetUser->id;

        if (!$is_admin && !$is_self) {
            return response()->json(['status' => 'forbidden', 'message' => 'You are not allowed to update this user'], 403);
        }
        // --------------------

        $request->validate([
            'username' => 'min:4|unique:users,username,' . $id,
            'password' => 'min:5'
        ]);

        if ($request->has('username')) {
            $targetUser->username = $request->username;
        }
        if ($request->has('password')) {
            $targetUser->password = Hash::make($request->password);
        }

        $targetUser->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully'
        ], 200);
    }

    /**
     * 3. DELETE: Hapus User (Admin Only)
     * URL: /api/v1/users/{id}
     */
    public function destroy(Request $request, $id)
    {
        // Cek Admin
        if (!($request->user() instanceof Administrator)) {
            return response()->json(['status' => 'forbidden', 'message' => 'You are not an administrator'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'not_found', 'message' => 'User not found'], 404);
        }

        $user->delete(); // Skor & Game milik user ini otomatis hilang (Cascade)

        return response()->json(['status' => 'success', 'message' => 'User deleted'], 204);
    }
}