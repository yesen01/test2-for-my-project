<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'name',
        'available_times'
    ];

    public function doctorSlots()
    {
        return $this->hasMany(DoctorSlot::class);
    }
}
