<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'caregiver_id',
        'linked_account_id',
        'address_id',
        'full_name',
        'relation',
        'birth_date',
        'phone',
        'special_needs',
        'notify_caregiver',
    ];

    public function caregiver()
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }

    public function linkedAccount()
    {
        return $this->belongsTo(User::class, 'linked_account_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}