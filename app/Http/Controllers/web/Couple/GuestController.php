<?php

namespace App\Http\Controllers\web\Couple;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Couple\GuestRequest;
use App\Models\Couple;
use App\Models\Guest;
use App\Services\GuestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    public function __construct(private readonly GuestService $guestService) {}

    public function qr(string $code): RedirectResponse
    {
        return redirect()->away($this->guestService->getGuestQrImageUrl($code));
    }

    public function showGuests()
    {
        $couple = $this->currentCouple();
        $guests = $couple->guests()->latest()->get();

        return view('couple.guests.index', compact('guests', 'couple'));
    }

    public function displayAddGuestForm()
    {
        $couple = $this->currentCouple();
        $rsvpStatuses = Guest::RSVP_STATUS;

        return view('couple.guests.create', compact('rsvpStatuses', 'couple'));
    }

    public function store(GuestRequest $request): RedirectResponse
    {
        $couple = $this->currentCouple();
        $this->guestService->create($couple, $request->validated());

        return redirect()->route('couple.guests.index')->with('success', 'Guest added successfully.');
    }

    public function show(Guest $guest)
    {
        $this->authorizeGuest($guest);
        $couple = $this->currentCouple();

        return view('couple.guests.show', compact('guest', 'couple'));
    }

    public function update(GuestRequest $request, Guest $guest): RedirectResponse
    {
        $this->authorizeGuest($guest);
        $guest->update($request->validated());

        return redirect()->route('couple.guests.index')->with('success', 'Guest updated successfully.');
    }

    public function updateRSVP(Request $request, Guest $guest): RedirectResponse
    {
        $this->authorizeGuest($guest);

        $validated = $request->validate([
            'rsvp_status' => ['required', 'string'],
        ]);

        $this->guestService->updateRsvp($guest, $validated['rsvp_status']);

        return redirect()->route('couple.guests.index')->with('success', 'RSVP updated successfully.');
    }

    public function checkin(Guest $guest): RedirectResponse
    {
        $this->authorizeGuest($guest);
        $this->guestService->updateRsvp($guest, Guest::RSVP_CONFIRMED);

        return redirect()->route('couple.guests.index')->with('success', 'Guest checked in successfully.');
    }

    public function destroy(Guest $guest): RedirectResponse
    {
        $this->authorizeGuest($guest);
        $this->guestService->delete($guest);

        return redirect()->route('couple.guests.index')->with('success', 'Guest deleted successfully.');
    }

    private function authorizeGuest(Guest $guest): void
    {
        if ($guest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function currentCouple(): Couple
    {
        $couple = Auth::user()?->couple;

        if (! $couple) {
            abort(403, 'Couple profile not found.');
        }

        return $couple;
    }
}
