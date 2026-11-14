<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Models\FamilyMember;
use App\Models\User;

class FamilyMemberController extends Controller
{
    // Display all family members
    public function index()
    {
        Gate::authorize('viewAny', FamilyMember::class);

        $familyMembers = auth()->user()
            ->familyMembers()
            ->with(['linkedAccount', 'address'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Family members retrieved successfully',
            'data' => $familyMembers
        ], 200);
    }


    // Add a new family member
    public function store(Request $request)
    {
        $user = auth()->user();

        Gate::authorize('create', FamilyMember::class);

        $validated = $request->validate([
            'full_name' => 'required|string|max:120',
            'relation' => 'required|string|max:255',
            'birth_date' => 'nullable|date|before:today',
            'phone' => 'nullable|string|max:20',
            'special_needs' => 'nullable|string',
            'address_id' => [
                'nullable',
                Rule::exists('addresses', 'id')
                    ->where('account_id', $user->id)
            ],
            'notify_caregiver' => 'sometimes|boolean',
        ]);

        $familyMember = $user->familyMembers()->create($validated);

        return response()->json([
            'message' => 'Family member created successfully',
            'data' => $familyMember
        ], 201);
    }


    // Display a specific family member
    public function show(FamilyMember $familyMember)
    {
        Gate::authorize('view', $familyMember);

        $familyMember->load(['linkedAccount', 'address']);

        return response()->json([
            'message' => 'Family member retrieved successfully',
            'data' => $familyMember
        ], 200);
    }


    // Update family member
    public function update(Request $request, FamilyMember $familyMember)
    {
        Gate::authorize('update', $familyMember);

        $user = auth()->user();

        $validated = $request->validate([
            'full_name' => 'sometimes|required|string|max:120',
            'relation' => 'sometimes|required|string|max:255',
            'birth_date' => 'nullable|date|before:today',
            'phone' => 'nullable|string|max:20',
            'special_needs' => 'nullable|string',
            'address_id' => [
                'nullable',
                Rule::exists('addresses', 'id')
                    ->where('account_id', $user->id)
            ],
            'notify_caregiver' => 'sometimes|boolean',
        ]);

        $familyMember->update($validated);

        return response()->json([
            'message' => 'Family member updated successfully',
            'data' => $familyMember->fresh()
        ], 200);
    }


    // Delete family member
    public function destroy(FamilyMember $familyMember)
    {
        Gate::authorize('delete', $familyMember);

        $familyMember->delete();

        return response()->json([
            'message' => 'Family member deleted successfully'
        ], 200);
    }


    // Link family member to an existing account
    public function link(Request $request, FamilyMember $familyMember)
    {
        Gate::authorize('link', $familyMember);

        $validated = $request->validate([
            'phone' => 'required_without:username|nullable|string',
            'username' => 'required_without:phone|nullable|string',
        ]);

        $account = User::query()
            ->when(
                !empty($validated['phone']),
                fn($query) => $query->where('phone', $validated['phone'])
            )
            ->when(
                !empty($validated['username']),
                fn($query) => $query->where('name', $validated['username'])
            )
            ->first();

        if (!$account) {
            return response()->json([
                'message' => 'Account not found'
            ], 404);
        }

        $familyMember->update([
            'linked_account_id' => $account->id
        ]);

        return response()->json([
            'message' => 'Family member linked successfully',
            'data' => $familyMember->fresh()->load('linkedAccount')
        ], 200);
    }


    // Toggle caregiver notifications
    public function notify(FamilyMember $familyMember)
    {
        Gate::authorize('notify', $familyMember);

        $familyMember->update([
            'notify_caregiver' => !$familyMember->notify_caregiver
        ]);

        return response()->json([
            'message' => 'Caregiver notification setting updated successfully',
            'data' => [
                'family_member_id' => $familyMember->id,
                'notify_caregiver' => $familyMember->notify_caregiver
            ]
        ], 200);
    }
}
