<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'couple_id' => $this->couple_id,
            'couple_name' => $this->coupleName(),
            'couple_email' => $this->couple?->email,
            'type_service' => $this->type_service,
            'booking_date' => $this->booking_date ? $this->booking_date->toDateString() : null,
            'status' => (bool) $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
            'couple' => $this->whenLoaded('couple', function () {
                $user = $this->couple;

                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'couple' => $user->couple ? [
                        'id' => $user->couple->id,
                        'partner_1_name' => $user->couple->partner_1_name,
                        'partner_2_name' => $user->couple->partner_2_name,
                        'display_name' => $this->coupleName(),
                    ] : null,
                ];
            }),
        ];
    }

    private function coupleName(): ?string
    {
        $coupleProfile = $this->couple?->couple;

        if (! $coupleProfile) {
            return null;
        }

        $name = trim(($coupleProfile->partner_1_name ?? '').' & '.($coupleProfile->partner_2_name ?? ''));

        return $name !== '' ? $name : null;
    }
}
