<?php

namespace App\Services;

use App\Models\Couple;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class CoupleService
{
    /**
     * Create a new couple profile and associate it with the user.
     * @param User $user
     * @param array $data
     * @return Couple
     */
    public function createCoupleProfile(User $user, array $data): Couple
    {
        return DB::transaction(function () use ($user, $data) {
            // Create the couple profile
            $couple = Couple::create([
                'user_id' => $user->id,
                'partner1_name' => $data['partner1_name'],
                'partner2_name' => $data['partner2_name'],
                'wedding_date' => $data['wedding_date'] ?? null,
                'wedding_time' => $data['wedding_time'] ?? null,
                'wedding_location' => $data['wedding_location'] ?? null,
            ]);

            // Update the user role to 'couple'
            $user->update(['role' => User::ROLE_COUPLE]);

            return $couple;
        });
    }
}