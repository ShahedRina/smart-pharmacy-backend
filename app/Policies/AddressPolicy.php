<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy
{
    public function update(User $user, Address $address): bool
    {
        return $user->id === $address->account_id;
    }

    public function delete(User $user, Address $address): bool
    {
        return $user->id === $address->account_id;
    }
    public function setDefault(User $user, Address $address): bool
{
    return (int) $user->id === (int) $address->account_id;
}
}
