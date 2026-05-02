<?php

namespace App\Http\Controllers\Api\v1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Vendor\ServiceRequest;
use App\Models\Service;
use App\Models\User;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApiServiceController extends Controller
{
    public function __construct(private readonly ServiceService $serviceService) {}

    public function index(): JsonResponse
    {
        $services = Service::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json(['data' => $services]);
    }

    public function store(ServiceRequest $request): JsonResponse
    {
        $vendorUser = $this->currentVendorUser();
        $service = $this->serviceService->create($vendorUser, $request->validated());

        return response()->json([
            'message' => 'Service created successfully.',
            'data' => $service,
        ], 201);
    }

    public function show(Service $service): JsonResponse
    {
        $this->authorizeService($service);

        return response()->json(['data' => $service]);
    }

    public function update(ServiceRequest $request, Service $service): JsonResponse
    {
        $this->authorizeService($service);
        $updatedService = $this->serviceService->update($service, $request->validated());

        return response()->json([
            'message' => 'Service updated successfully.',
            'data' => $updatedService,
        ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->authorizeService($service);
        $this->serviceService->delete($service);

        return response()->json(['message' => 'Service deleted successfully.']);
    }

    private function authorizeService(Service $service): void
    {
        if ($service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function currentVendorUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403, 'Unauthorized action.');
        }

        return $user;
    }
}
