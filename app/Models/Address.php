<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'account_id',
        'title',
        'city',
        'details',
        'lat',
        'lng',
        'is_default',
    ];

    protected $casts = [
        'lat' => 'decimal:6',
        'lng' => 'decimal:6',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(User::class, 'account_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }
}