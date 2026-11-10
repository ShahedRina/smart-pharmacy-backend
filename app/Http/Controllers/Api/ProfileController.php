<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Display authenticated user's profile
    public function show(Request $request)
    {
        return new AccountResource($request->user());
    }

    // Update authenticated user's profile
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'phone' => 'sometimes|required|string|max:20|unique:users,phone,' . $user->id,
        ]);

        $user->update($validated);

        return new AccountResource($user->fresh());
    }

public function updatePassword(Request $request)
{
    $user = $request->user();

    $request->validate([
        'current_password' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Current password is incorrect',
            'data' => null
        ], 422);
    }

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    $user->tokens()
        ->where('id', '!=', $user->currentAccessToken()->id)
        ->delete();

    return response()->json([
        'status' => true,
        'message' => 'Password updated successfully',
        'data' => null
    ]);
}

}
