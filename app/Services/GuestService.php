<?php

namespace App\Services;

use App\Models\Couple;
use App\Models\Guest;
use Illuminate\Support\Str;

class GuestService
{
    /**
     * addGuest(), updateGuest(), deleteGuest()
     * updateRSVP(), validateCheckIn(),viewGuestList()
     * methods to manage guest records, including generating unique invite codes and QR code strings for each guest.
     */

    // Method to add a new guest to a couple
    public function create(Couple $couple, array $data): Guest
    {
        $inviteCode = strtoupper(Str::random(8)); // Generate a random 8-character invite code
        $qrCodeString = 'INVITE:'.$inviteCode; // Create a string to encode in the QR code

        return $couple->guests()->create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'pax_count' => $data['pax_count'] ?? 1,
            'rsvp_status' => Guest::RSVP_PENDING, // Default RSVP status
            'invite_code' => $inviteCode,
            'qr_code_string' => $qrCodeString,
        ]);
    }

    public function updateRsvp(Guest $guest, string $status): Guest
    {
        if (! in_array($status, Guest::RSVP_STATUS, true)) {
            throw new \InvalidArgumentException('Invalid RSVP status');
        }

        $guest->update(['rsvp_status' => $status]);

        return $guest->fresh();
    }

    public function getGuestCheckinUrl(string $inviteCode): string
    {
        return url('/guest/checkin/'.$inviteCode);
    }

    public function getGuestQrImageUrl(string $inviteCode): string
    {
        $checkinUrl = $this->getGuestCheckinUrl($inviteCode);

        return 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data='.rawurlencode($checkinUrl);
    }

    public function validationCheckIn(Couple $couple, string $inviteCode): ?Guest
    {
        $guest = $couple->guests()->where('invite_code', $inviteCode)->first();

        if ($guest && $guest->rsvp_status === Guest::RSVP_CONFIRMED) {
            return $guest; // Valid check-in
        }

        return null; // Invalid check-in
    }

    public function getSummary(Couple $couple): array
    {
        $guests = $couple->guests()->get();

        return [
            'total_guests' => $guests->count(),
            'confirmed_guests' => $guests->where('rsvp_status', Guest::RSVP_CONFIRMED)->count(),
            'declined_guests' => $guests->where('rsvp_status', Guest::RSVP_DECLINED)->count(),
            'pending_guests' => $guests->where('rsvp_status', Guest::RSVP_PENDING)->count(),
        ];
    }

    public function delete(Guest $guest): void
    {
        $guest->delete();
    }
}
