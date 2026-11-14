<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

 protected $fillable = [
    'name',
    'email',
    'phone',
    'password',
    'role',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class, 'account_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'paid_by');
    }

    public function orderStatusChanges()
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by');
    }

    public function medicationLogs()
    {
        return $this->hasMany(MedicationLog::class, 'logged_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'account_id');
    }
    public function familyMembers()
{
    return $this->hasMany(FamilyMember::class, 'caregiver_id');
}

public function linkedFamilyMembers()
{
    return $this->hasMany(FamilyMember::class, 'linked_account_id');
}
}