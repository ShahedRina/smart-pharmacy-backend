<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'schedule_id',
        'logged_by',
        'scheduled_at',
        'status',
        'logged_at',
    ];

    public function schedule()
    {
        return $this->belongsTo(MedicationSchedule::class, 'schedule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}