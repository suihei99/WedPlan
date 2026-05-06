<?php

namespace App\Http\Controllers\web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Couple;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\View\View;

class DashboardAdminController extends Controller
{
    public function showDashboard(): View
    {
        $stats = [
            'couples' => User::couples()->count(),
            'vendors' => User::vendors()->count(),
            'active_users' => User::query()->whereIn('role', [User::ROLE_COUPLE, User::ROLE_VENDOR])->where('is_active', true)->count(),
            'inactive_users' => User::query()->whereIn('role', [User::ROLE_COUPLE, User::ROLE_VENDOR])->where('is_active', false)->count(),
            'pending_vendors' => Vendor::query()->where('status', Vendor::STATUS_PENDING)->count(),
            'approved_vendors' => Vendor::query()->where('status', Vendor::STATUS_APPROVED)->count(),
            'rejected_vendors' => Vendor::query()->where('status', Vendor::STATUS_REJECTED)->count(),
            'upcoming_weddings' => Couple::query()->whereNotNull('wedding_date')->count(),
        ];

        $pendingVendors = Vendor::query()
            ->with('user')
            ->where('status', Vendor::STATUS_PENDING)
            ->latest()
            ->limit(5)
            ->get();

        $recentUsers = User::query()
            ->with(['couple', 'vendor'])
            ->whereIn('role', [User::ROLE_COUPLE, User::ROLE_VENDOR])
            ->latest()
            ->limit(8)
            ->get();

        $upcomingWeddings = Couple::query()
            ->with('user')
            ->whereNotNull('wedding_date')
            ->orderBy('wedding_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingVendors', 'recentUsers', 'upcomingWeddings'));
    }
}
