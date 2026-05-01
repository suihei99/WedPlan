<?php

namespace App\Http\Controllers\web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\UserNotificationService;
use Illuminate\Http\RedirectResponse;

class ManageUserAdminController extends Controller
{
    public function __construct(private readonly UserNotificationService $userNotificationService) {}

    public function approveVendor(Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'status' => Vendor::STATUS_APPROVED,
        ]);

        $vendor->user()->update([
            'is_active' => true,
        ]);

        $this->userNotificationService->notifyVendorApproved($vendor->user);

        return back()->with('success', 'Vendor approved successfully.');
    }

    public function rejectVendor(Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'status' => Vendor::STATUS_REJECTED,
        ]);

        $vendor->user()->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Vendor rejected successfully.');
    }
}
