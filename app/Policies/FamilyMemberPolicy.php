<?php

namespace App\Policies;

use App\Models\FamilyMember;
use App\Models\User;

class FamilyMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FamilyMember $familyMember): bool
    {
        return $user->id === $familyMember->caregiver_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, FamilyMember $familyMember): bool
    {
        return $user->id === $familyMember->caregiver_id;
    }

    public function delete(User $user, FamilyMember $familyMember): bool
    {
        return $user->id === $familyMember->caregiver_id;
    }

    public function link(User $user, FamilyMember $familyMember): bool
    {
        return $user->id === $familyMember->caregiver_id;
    }

    public function notify(User $user, FamilyMember $familyMember): bool
    {
        return $user->id === $familyMember->caregiver_id;
    }
}
