<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

#[\Illuminate\Console\Attributes\Signature('app:create-admin')]
#[\Illuminate\Console\Attributes\Description('Create a new admin account')]
class CreateAdmin extends Command
{
    public function handle()
    {
        $name = $this->ask('Enter admin name');
        $email = $this->ask('Enter admin email');
        $phone = $this->ask('Enter admin phone');
do {
    $password = $this->ask('Enter admin password');

    if (empty($password)) {
        $this->error('Password cannot be empty!');
    } elseif (strlen($password) < 8) {
        $this->error('Password must be at least 8 characters!');
    }

} while (empty($password) || strlen($password) < 8);

        if (User::where('email', $email)->exists()) {
            $this->error('This email already exists!');
            return self::FAILURE;
        }

        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
        ]);

        // Assign admin role securely
        $admin->role = 'admin';
        $admin->save();

        $this->info('Admin account created successfully!');

        return self::SUCCESS;
    }
}

