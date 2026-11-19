<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew([
            'email' => 'admin@school.test'
        ]);

        $admin->name = 'School Admin';
        $admin->password = Hash::make('Admin@12345');
        $admin->role = 'admin';

        $admin->save();
    }
}