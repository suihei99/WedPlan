<?php

namespace App\Http\Controllers\web\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Vendor\ServiceRequest;
use App\Models\Service;
use App\Models\User;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ServiceVendorController extends Controller
{
    public function __construct(private readonly ServiceService $serviceService) {}

    public function index()
    {
        $vendorUser = $this->currentVendorUser();

        return view('vendor.service.index', [
            'vendor' => $vendorUser->vendor,
            'services' => $this->serviceService->paginateForVendor($vendorUser),
            'summary' => $this->serviceService->summaryForVendor($vendorUser),
            'serviceTypes' => Service::SERVICE_TYPES,
        ]);
    }

    public function create()
    {
        $vendorUser = $this->currentVendorUser();

        return view('vendor.service.create', [
            'vendor' => $vendorUser->vendor,
            'serviceTypes' => Service::SERVICE_TYPES,
            'service' => null,
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $vendorUser = $this->currentVendorUser();
        $this->serviceService->create($vendorUser, $request->validated());

        return redirect()->route('vendor.service.index')->with('success', 'Service added successfully.');
    }

    public function show(Service $service)
    {
        $this->authorizeService($service);

        return view('vendor.service.show', [
            'vendor' => $this->currentVendorUser()->vendor,
            'service' => $service,
        ]);
    }

    public function edit(Service $service)
    {
        $this->authorizeService($service);

        return view('vendor.service.edit', [
            'vendor' => $this->currentVendorUser()->vendor,
            'service' => $service,
            'serviceTypes' => Service::SERVICE_TYPES,
        ]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $this->authorizeService($service);
        $this->serviceService->update($service, $request->validated());

        return redirect()->route('vendor.service.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->authorizeService($service);
        $this->serviceService->delete($service);

        return redirect()->route('vendor.service.index')->with('success', 'Service deleted successfully.');
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
