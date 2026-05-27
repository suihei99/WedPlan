<?php

namespace App\Http\Controllers\Api\v1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorCoupleResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ApiVendorCoupleController extends Controller
{
    public function index(): JsonResponse
    {
        $couples = User::query()
            ->where('role', User::ROLE_COUPLE)
            ->with('couple')
            ->orderBy('email')
            ->get();

        return VendorCoupleResource::collection($couples)->response();
    }
}
