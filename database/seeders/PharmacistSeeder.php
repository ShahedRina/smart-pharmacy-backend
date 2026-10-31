<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PharmacistSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pharmacist@example.com'],
            [
                'name' => 'Pharmacist',
                'phone' => '0999999999',
                'password' => Hash::make('Pharmacist@123'),
                'role' => 'pharmacist',
            ]
        );
    }
}