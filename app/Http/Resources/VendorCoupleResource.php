<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorCoupleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $coupleProfile = $this->couple;

        return [
            'id' => $this->id,
            'email' => $this->email,
            'couple_id' => $coupleProfile?->id,
            'couple_name' => $this->coupleName(),
            'partner_1_name' => $coupleProfile?->partner_1_name,
            'partner_2_name' => $coupleProfile?->partner_2_name,
        ];
    }

    private function coupleName(): ?string
    {
        $coupleProfile = $this->couple;

        if (! $coupleProfile) {
            return null;
        }

        $name = trim(($coupleProfile->partner_1_name ?? '').' & '.($coupleProfile->partner_2_name ?? ''));

        return $name !== '' ? $name : null;
    }
}
