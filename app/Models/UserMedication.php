<?php

namespace App\Models;
use App\Models\User;
use App\Models\FamilyMember;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\MedicationSchedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class UserMedication extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'family_member_id',
        'medicine_id',
        'prescription_id',
        'dosage',
        'start_date',
        'end_date',
        'qty_on_hand',
        'refill_alert_days',
    ];

    public function account()
    {
        return $this->belongsTo(User::class, 'account_id');
    }

    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function schedules()
    {
        return $this->hasMany(MedicationSchedule::class);
    }
}