<?php

namespace App\Services;

use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    public function paginateForVendor(User $vendorUser, int $perPage = 6): LengthAwarePaginator
    {
        return $vendorUser->services()->latest()->paginate($perPage);
    }

    public function summaryForVendor(User $vendorUser): array
    {
        $services = $vendorUser->services()->get();
        $totalServices = $services->count();
        $averagePrice = $totalServices > 0 ? (float) $services->avg('price_estimate') : 0.0;

        return [
            'total_services' => $totalServices,
            'average_price' => round($averagePrice, 2),
            'top_categories' => $services
                ->groupBy('type_service')
                ->map(fn (Collection $group) => $group->count())
                ->sortDesc()
                ->take(4),
            'featured' => $services->take(3)->map(function (Service $service) {
                return [
                    'id' => $service->id,
                    'service_name' => $service->service_name,
                    'type_service' => $service->type_service,
                    'price_estimate' => $service->price_estimate,
                ];
            }),
        ];
    }

    public function create(User $vendorUser, array $data): Service
    {
        return $vendorUser->services()->create($this->servicePayload($data));
    }

    public function update(Service $service, array $data): Service
    {
        $service->update($this->servicePayload($data, $service->image_url));

        return $service->refresh();
    }

    public function delete(Service $service): void
    {
        $this->deleteStoredImage($service->image_url);
        $service->delete();
    }

    private function servicePayload(array $data, ?string $existingImagePath = null): array
    {
        return [
            'service_name' => $data['service_name'],
            'type_service' => $data['type_service'],
            'price_estimate' => $data['price_estimate'],
            'description' => $data['description'] ?? null,
            'image_url' => $this->storeServiceImage($data['image_url'] ?? null, $existingImagePath),
        ];
    }

    private function storeServiceImage(mixed $image, ?string $existingImagePath = null): ?string
    {
        if ($image instanceof UploadedFile) {
            $this->deleteStoredImage($existingImagePath);

            return $image->store('services', 'public');
        }

        if (is_string($image) && $image !== '') {
            return $image;
        }

        return $existingImagePath;
    }

    private function deleteStoredImage(?string $imagePath): void
    {
        if (! $imagePath || $this->looksLikeExternalUrl($imagePath)) {
            return;
        }

        Storage::disk('public')->delete($imagePath);
    }

    private function looksLikeExternalUrl(string $imagePath): bool
    {
        return str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://');
    }
}
