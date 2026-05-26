<?php

namespace App\Http\Controllers\Api\v1\Couple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Couple\GuestRequest;
use App\Models\Guest;
use App\Services\GuestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ApiGuestController extends Controller
{
    public function __construct(private readonly GuestService $guestService) {}

    public function qr(string $code): JsonResponse
    {
        return $this->invitation($code);
    }

    public function invitation(string $code): JsonResponse
    {
        $invitation = $this->guestService->getInvitationDetails($code);

        if (! $invitation) {
            return response()->json([
                'message' => 'Invitation not found.',
            ], 404);
        }

        return response()->json([
            'data' => array_merge($invitation, [
                'checkin_url' => $this->guestService->getGuestCheckinUrl($code),
                'qr_image_url' => $this->guestService->getGuestQrImageUrl($code),
            ]),
        ]);
    }

    public function publicUpdateRsvp(Request $request, string $code): JsonResponse
    {
        $validated = $request->validate([
            'rsvp_status' => ['required', 'string', Rule::in(Guest::RSVP_STATUS)],
        ]);

        $guest = $this->guestService->updateRsvpByInviteCode($code, $validated['rsvp_status']);

        if (! $guest) {
            return response()->json([
                'message' => 'Invitation not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'RSVP updated successfully.',
            'data' => $guest,
        ]);
    }

    public function index(): JsonResponse
    {
        $couple = Auth::user()?->couple;
        abort_if(! $couple, 403, 'Couple profile not found.');

        $guests = $couple->guests()->latest()->get();

        return response()->json(['data' => $guests]);
    }

    public function store(GuestRequest $request): JsonResponse
    {
        $couple = Auth::user()?->couple;
        abort_if(! $couple, 403, 'Couple profile not found.');

        $guest = $this->guestService->create($couple, $request->validated());

        return response()->json([
            'message' => 'Guest created successfully.',
            'data' => $guest,
        ], 201);
    }

    public function show(Guest $guest): JsonResponse
    {
        $this->authorizeGuest($guest);

        return response()->json(['data' => $guest]);
    }

    public function update(GuestRequest $request, Guest $guest): JsonResponse
    {
        $this->authorizeGuest($guest);
        $guest->update($request->validated());

        return response()->json([
            'message' => 'Guest updated successfully.',
            'data' => $guest->fresh(),
        ]);
    }

    public function updateRsvp(Request $request, Guest $guest): JsonResponse
    {
        $this->authorizeGuest($guest);

        $validated = $request->validate([
            'rsvp_status' => ['required', 'string', Rule::in(Guest::RSVP_STATUS)],
        ]);

        $updated = $this->guestService->updateRsvp($guest, $validated['rsvp_status']);

        return response()->json([
            'message' => 'RSVP status updated successfully.',
            'data' => $updated,
        ]);
    }

    public function checkin(Guest $guest): JsonResponse
    {
        $this->authorizeGuest($guest);
        $updated = $this->guestService->checkIn($guest);

        return response()->json([
            'message' => 'Guest checked in successfully.',
            'data' => $updated,
        ]);
    }

    public function destroy(Guest $guest): JsonResponse
    {
        $this->authorizeGuest($guest);
        $this->guestService->delete($guest);

        return response()->json(['message' => 'Guest deleted successfully.']);
    }

    private function authorizeGuest(Guest $guest): void
    {
        if ($guest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
