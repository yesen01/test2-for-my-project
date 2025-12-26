<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;


class DoctorSeeder extends Seeder
{
    public function run()
    {
        Doctor::create([
            'name' => 'د. محمد علي',
            'available_times' => '09:00,10:00,11:00,14:00'
        ]);

        Doctor::create([
            'name' => 'د. فاطمة الهادي',
            'available_times' => '12:00,13:00,15:00,16:00'
        ]);
    }
}
