<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'user_id','doctor_id','doctor_slot_id','date','time','notes','status'
    ];

    protected $dates = ['date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Doctor::class, 'doctor_id');
    }

    public function doctorSlot(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DoctorSlot::class, 'doctor_slot_id');
    }

    // حالة محسوبة تلقائياً للاعرض فقط (computed status)
    public function getComputedStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        $today = Carbon::today();
        $date = Carbon::parse($this->date);

        if ($date->lt($today)) {
            return 'completed';
        }

        if ($date->isSameDay($today)) {
            return $this->status === 'confirmed' ? 'confirmed' : 'pending';
        }

        // مستقبلية
        return $this->status === 'confirmed' ? 'confirmed' : 'pending';
    }
}
