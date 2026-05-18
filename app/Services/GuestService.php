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

    public function getInvitationDetails(string $inviteCode): ?array
    {
        $normalizedInviteCode = strtoupper(trim($inviteCode));

        $guest = Guest::query()
            ->with('user.couple')
            ->where('invite_code', $normalizedInviteCode)
            ->first();

        if (! $guest) {
            return null;
        }

        $couple = $guest->user?->couple;

        if (! $couple) {
            return null;
        }

        $weddingDate = $couple->wedding_date?->format('Y-m-d');
        $weddingTime = $couple->wedding_time ? substr((string) $couple->wedding_time, 0, 5) : null;

        return [
            'invite_code' => $guest->invite_code,
            'guest_name' => $guest->name,
            'pax_count' => (int) ($guest->pax_count ?? 1),
            'rsvp_status' => $guest->rsvp_status,
            'couple' => [
                'partner_1_name' => $couple->partner_1_name,
                'partner_2_name' => $couple->partner_2_name,
                'display_name' => trim(($couple->partner_1_name ?? '').' & '.($couple->partner_2_name ?? '')),
            ],
            'wedding' => [
                'venue' => $couple->wedding_venue,
                'date' => $weddingDate,
                'time' => $weddingTime,
            ],
        ];
    }

    public function updateRsvpByInviteCode(string $inviteCode, string $status): ?Guest
    {
        if (! in_array($status, Guest::RSVP_STATUS, true)) {
            throw new \InvalidArgumentException('Invalid RSVP status');
        }

        $normalizedInviteCode = strtoupper(trim($inviteCode));

        $guest = Guest::query()
            ->where('invite_code', $normalizedInviteCode)
            ->first();

        if (! $guest) {
            return null;
        }

        $guest->update(['rsvp_status' => $status]);

        return $guest->fresh();
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

    public function getPrintableReport(Couple $couple): array
    {
        $guests = $couple->guests()->latest()->get();

        return [
            'generated_at' => now()->format('Y-m-d H:i'),
            'total_guests' => $guests->count(),
            'confirmed_guests' => $guests->where('rsvp_status', Guest::RSVP_CONFIRMED)->count(),
            'pending_guests' => $guests->where('rsvp_status', Guest::RSVP_PENDING)->count(),
            'declined_guests' => $guests->where('rsvp_status', Guest::RSVP_DECLINED)->count(),
            'guests' => $guests->map(fn (Guest $guest): array => [
                'name' => $guest->name,
                'phone' => $guest->phone,
                'pax_count' => (int) ($guest->pax_count ?? 1),
                'invite_code' => $guest->invite_code,
                'rsvp_status' => $guest->rsvp_status,
                'qr_ready' => (bool) $guest->qr_code_string,
            ])->values()->all(),
        ];
    }

    public function delete(Guest $guest): void
    {
        $guest->delete();
    }
}
