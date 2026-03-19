<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
                'password' => 'Admin@123',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]);
    }
}
