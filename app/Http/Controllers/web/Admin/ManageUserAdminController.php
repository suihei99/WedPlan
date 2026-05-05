<?php

namespace App\Http\Controllers\web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Services\UserNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ManageUserAdminController extends Controller
{
    public function __construct(private readonly UserNotificationService $userNotificationService) {}

    public function vendorsIndex(): View
    {
        $vendors = Vendor::query()
            ->with('user')
            ->latest()
            ->paginate(10);

        $summary = [
            'total' => Vendor::query()->count(),
            'pending' => Vendor::query()->where('status', Vendor::STATUS_PENDING)->count(),
            'approved' => Vendor::query()->where('status', Vendor::STATUS_APPROVED)->count(),
            'rejected' => Vendor::query()->where('status', Vendor::STATUS_REJECTED)->count(),
        ];

        return view('admin.manage_vendor.index', compact('vendors', 'summary'));
    }

    public function showVendor(Vendor $vendor): View
    {
        $vendor->load('user', 'services');

        return view('admin.manage_vendor.view', compact('vendor'));
    }

    public function usersIndex(): View
    {
        $users = User::query()
            ->with(['couple', 'vendor'])
            ->latest()
            ->paginate(12);

        $summary = [
            'total' => User::query()->count(),
            'admins' => User::admins()->count(),
            'couples' => User::couples()->count(),
            'vendors' => User::vendors()->count(),
            'active' => User::query()->where('is_active', true)->count(),
            'inactive' => User::query()->where('is_active', false)->count(),
        ];

        return view('admin.manage_user.index', compact('users', 'summary'));
    }

    public function showUser(User $user): View
    {
        $user->load(['couple', 'vendor']);

        return view('admin.manage_user.view', compact('user'));
    }

    public function toggleUserStatus(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Admin accounts cannot be disabled from this screen.');

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        return back()->with('success', $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.');
    }

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
