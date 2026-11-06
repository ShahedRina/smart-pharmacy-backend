<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\StoreDeliveryRequest;

class AdminAuthController extends Controller
{
    // Admin Login
    public function adminLogin(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $admin = User::where('email', $data['email'])->first();

        if (!$admin || !Hash::check($data['password'], $admin->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password',
                'data' => null
            ], 401);
        }

        if ($admin->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. Admin only.',
                'data' => null
            ], 403);
        }

        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Admin login successful',
            'data' => [
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'phone' => $admin->phone,
                    'role' => $admin->role,
                ],
                'token' => $token
            ]
        ], 200);
    }

    // Create New Admin
    public function createAdmin(StoreAdminRequest $request)
    {
        $data = $request->validated();

        $admin = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin created successfully',
            'data' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'role' => $admin->role,
            ]
        ], 201);
    }

    // Create Delivery Account
    public function createDelivery(StoreDeliveryRequest $request)
    {
        $data = $request->validated();

        $delivery = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'role' => 'delivery',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Delivery account created successfully',
            'data' => [
                'id' => $delivery->id,
                'name' => $delivery->name,
                'email' => $delivery->email,
                'phone' => $delivery->phone,
                'role' => $delivery->role,
            ]
        ], 201);
    }

    // Admin Logout
    public function adminLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Admin logged out successfully',
            'data' => null
        ], 200);
    }
}
