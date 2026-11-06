<?php

namespace App\Models;
use App\Models\UserMedication;
use App\Models\MedicationLog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationSchedule extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_medication_id',
        'dose_time',
        'days_of_week',
    ];

    protected $casts = [
        'days_of_week' => 'array',
    ];

   public function userMedication()
{
    return $this->belongsTo(UserMedication::class, 'user_medication_id');
}

    public function logs()
    {
        return $this->hasMany(MedicationLog::class, 'schedule_id');
    }
    
}