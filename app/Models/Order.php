<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'family_member_id',
        'pharmacist_id',
        'delivery_agent_id',
        'prescription_id',
        'address_id',
        'order_date',
        'total_amount',
        'status',
        'delivery_otp',
        'recipient_name',
        'delivered_at',
    ];
    public function orderDetails()
{
    return $this->hasMany(OrderDetail::class);
}

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }

    public function pharmacist()
    {
        return $this->belongsTo(User::class, 'pharmacist_id');
    }
    public function statusHistory()
{
    return $this->hasMany(OrderStatusHistory::class);
}

    public function deliveryAgent()
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}