<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'quantity',
        'image',
        'expiry_date',
    ];

    public function category()
{
    return $this->belongsTo(Category::class);
}
public function orderDetails()
{
    return $this->hasMany(OrderDetail::class);
}
}

