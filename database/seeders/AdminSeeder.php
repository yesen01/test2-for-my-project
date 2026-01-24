<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'مدير النظام',
            'email'    => 'admin@kayan.com',
            'password' => Hash::make('12345678'), // تأكد من تغييرها لاحقاً
            'role'     => 'admin', // تأكد أن هذا الحقل موجود في جدول المستخدمين
        ]);
    }
}
