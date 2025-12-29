<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorSlot extends Model
{
    protected $fillable = [
        'doctor_id', 'start_at', 'end_at', 'day_of_week', 'start_time', 'end_time'
    ];

    protected $dates = ['start_at', 'end_at'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_slot_id');
    }

    // For weekly slots we don't enforce capacity per-slot by default.
    public function bookedCount(): int
    {
        return $this->appointments()->count();
    }

    public function isAvailable(): bool
    {
        // by default consider a slot available; availability for a specific
        // date should be checked with isBookedOnDate()
        return true;
    }

    // check whether this slot is already booked for a given date (Y-m-d or Carbon)
    public function isBookedOnDate($date): bool
    {
        $d = $date instanceof \Carbon\Carbon ? $date->toDateString() : \Carbon\Carbon::parse($date)->toDateString();
        return $this->appointments()->where('date', $d)->exists();
    }
}
