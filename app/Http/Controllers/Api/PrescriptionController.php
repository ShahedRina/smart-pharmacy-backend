<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'image' => [
            'required',
            'file',
            'mimes:jpg,jpeg,png,pdf',
            'max:5120',
        ],

        'family_member_id' => [
            'nullable',
            Rule::exists('family_members', 'id')
                ->where('caregiver_id', $request->user()->id),
        ],
    ]);

    $path = $request->file('image')
        ->store('prescriptions', 'public');

    $prescription = Prescription::create([
        'customer_id' => $request->user()->id,
        'family_member_id' => $validated['family_member_id'] ?? null,
         'image_url' => Storage::disk('public')->url($path),
        'status' => 'pending',
    ]);

    return response()->json([
        'message' => 'Prescription uploaded successfully',
        'data' => $prescription,
    ], 201);
}
public function index(Request $request)
{
    $prescriptions = Prescription::where(
        'customer_id',
        $request->user()->id
    )
    ->latest()
    ->get();

    return response()->json([
        'data' => $prescriptions,
    ], 200);
}
public function show(Request $request, $id)
{
    $prescription = Prescription::findOrFail($id);

    if ((int) $prescription->customer_id !== (int) $request->user()->id) {
        return response()->json([
            'message' => 'Forbidden',
        ], 403);
    }

    return response()->json([
        'data' => $prescription,
    ], 200);
}
public function pharmacistIndex(Request $request)
{
    $validated = $request->validate([
        'status' => [
            'nullable',
            Rule::in(['pending', 'approved', 'rejected']),
        ],
    ]);

    $query = Prescription::with([
    'customer:id,name',
    'familyMember:id,full_name',
]);

    if (isset($validated['status'])) {
        $query->where('status', $validated['status']);
    }

    $prescriptions = $query->latest()->get();

    return response()->json([
        'data' => $prescriptions,
    ], 200);
}
public function review(Request $request, $id)
{
    $validated = $request->validate([
        'status' => [
            'required',
            Rule::in(['approved', 'rejected']),
        ],
        'review_notes' => [
            'required_if:status,rejected',
            'nullable',
            'string',
        ],
    ]);

    return DB::transaction(function () use ($request, $id, $validated) {

        $prescription = Prescription::where('id', $id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($prescription->status !== 'pending') {
            return response()->json([
                'message' => 'This prescription has already been reviewed.',
            ], 422);
        }

        $prescription->update([
            'status' => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'pharmacist_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Prescription reviewed successfully.',
            'data' => $prescription,
        ], 200);
    });
}
}
