<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'message' => 'Addresses retrieved successfully',
            'data' => $addresses
        ], 200);
    }


public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'nullable|string|max:50',
        'city' => 'required|string|max:255',
        'details' => 'required|string|max:255',
        'lat' => 'nullable|numeric|between:-90,90',
        'lng' => 'nullable|numeric|between:-180,180',
    ]);

    $user = auth()->user();

    $isFirstAddress = !$user->addresses()->exists();

    $address = $user->addresses()->create([
        'title' => $validated['title'] ?? null,
        'city' => $validated['city'],
        'details' => $validated['details'],
        'lat' => $validated['lat'] ?? null,
        'lng' => $validated['lng'] ?? null,
        'is_default' => $isFirstAddress,
    ]);

    return response()->json([
        'message' => 'Address created successfully',
        'data' => $address
    ], 201);
}

public function update(Request $request, Address $address)
{
    Gate::authorize('update', $address);

    $validated = $request->validate([
        'title' => 'nullable|string|max:50',
        'city' => 'required|string|max:255',
        'details' => 'required|string|max:255',
        'lat' => 'nullable|numeric|between:-90,90',
        'lng' => 'nullable|numeric|between:-180,180',
    ]);

    $address->update($validated);

    return response()->json([
        'message' => 'Address updated successfully',
        'data' => $address
    ], 200);
}

public function destroy(Address $address)
{
    Gate::authorize('delete', $address);

    $address->delete();

    return response()->json([
        'message' => 'Address deleted successfully'
    ], 200);
}


public function setDefault(Address $address)
{
    Gate::authorize('setDefault', $address);

    DB::transaction(function () use ($address) {

        auth()->user()
            ->addresses()
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);

    });

    return response()->json([
        'message' => 'Default address updated successfully',
        'data' => $address->fresh()
    ], 200);
}



}
