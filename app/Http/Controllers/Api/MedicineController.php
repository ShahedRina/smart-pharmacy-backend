<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicineController extends Controller
{
    // Get all medicines


public function index(Request $request)
{
    $perPage = $request->integer('per_page', 10);
    $perPage = min(max($perPage, 1), 50);

    $medicines = Medicine::with('category')
        ->when($request->filled('search'), function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        })
        ->when($request->filled('category_id'), function ($query) use ($request) {
            $query->where('category_id', $request->category_id);
        })
        ->when($request->has('requires_prescription'), function ($query) use ($request) {
            $query->where(
                'requires_prescription',
                $request->boolean('requires_prescription')
            );
        })
        ->where(function ($query) {
            $query->whereNull('expiry_date')
                  ->orWhereDate('expiry_date', '>=', now()->toDateString());
        })
        ->orderBy('id', 'desc')
        ->paginate($perPage);

    return response()->json([
        'status' => true,
        'message' => 'Medicines retrieved successfully',
        'data' => $medicines->items(),
        'pagination' => [
            'current_page' => $medicines->currentPage(),
            'per_page' => $medicines->perPage(),
            'total' => $medicines->total(),
            'last_page' => $medicines->lastPage(),
            'next_page_url' => $medicines->nextPageUrl(),
            'prev_page_url' => $medicines->previousPageUrl(),
        ]
    ], 200);
}



    // Create new medicine
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'expiry_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('medicines', 'public');
        }

        $medicine = Medicine::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Medicine created successfully',
            'data' => $medicine->load('category')
        ], 201);
    }

    // Get single medicine
    public function show(Medicine $medicine)
    {
        return response()->json([
            'status' => true,
            'message' => 'Medicine retrieved successfully',
            'data' => $medicine->load('category')
        ], 200);
    }

    // Update medicine
    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'quantity' => 'sometimes|integer|min:0',
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'expiry_date' => 'sometimes|nullable|date|after_or_equal:today',
        ]);

        if ($request->hasFile('image')) {

            if ($medicine->image) {
                Storage::disk('public')->delete($medicine->image);
            }

            $validated['image'] = $request->file('image')
                ->store('medicines', 'public');
        }

        $medicine->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Medicine updated successfully',
            'data' => $medicine->load('category')
        ], 200);
    }

    // Delete medicine
    public function destroy(Medicine $medicine)
    {
        if ($medicine->image) {
            Storage::disk('public')->delete($medicine->image);
        }

        $medicine->delete();

        return response()->json([
            'status' => true,
            'message' => 'Medicine deleted successfully'
        ], 200);
    }
}

