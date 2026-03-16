<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(   
        ['email' => 'brianjuniorlee99@gmail.com'],
        [
            'name' => 'Super Admin',
            'password' => 'Admin@123',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }
}
