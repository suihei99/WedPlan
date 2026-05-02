@php
    use App\Models\Service;

    $service = $service ?? null;
    $serviceName = old('service_name', $service->service_name ?? '');
    $typeService = old('type_service', $service->type_service ?? '');
    $priceEstimate = old('price_estimate', $service->price_estimate ?? '');
    $description = old('description', $service->description ?? '');
    $imageUrl = $service->image_url ?? '';
@endphp

<div class="vendor-service-form-grid">
    <div class="vendor-service-field-group">
        <label for="service_name">Service Name</label>
        <input id="service_name" name="service_name" type="text" class="vendor-service-field" value="{{ $serviceName }}" placeholder="Elegant venue setup, bridal makeup, live band..." required>
        @error('service_name')
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="vendor-service-field-group">
        <label for="type_service">Type Of Services</label>
        <select id="type_service" name="type_service" class="vendor-service-select" required>
            <option value="" disabled {{ $typeService === '' ? 'selected' : '' }}>Choose a wedding service</option>
            @foreach (Service::SERVICE_TYPES as $type)
                <option value="{{ $type }}" {{ $typeService === $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
        @error('type_service')
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="vendor-service-field-group">
        <label for="price_estimate">Price Estimate (RM)</label>
        <input id="price_estimate" name="price_estimate" type="number" min="0" step="0.01" class="vendor-service-field" value="{{ $priceEstimate }}" placeholder="1200.00" required>
        @error('price_estimate')
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="vendor-service-field-group">
        <label for="image_url">Service Image Upload</label>
        <input
            id="image_url"
            name="image_url"
            type="file"
            class="vendor-service-field"
            accept=".png,.webp,.jpeg,.jpg,.gif"
            data-service-image-input
            data-service-preview-target="#servicePreviewImage"
            data-service-preview-default="{{ $imageUrl ? ((str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) ? $imageUrl : asset('storage/' . ltrim($imageUrl, '/'))) : asset('assets/icons/WebPlan_logo.webp') }}"
        >
        <p class="vendor-service-note">Upload a PNG, WEBP, JPEG, JPG, or GIF file. The file will be saved and the database will store its path.</p>
        @error('image_url')
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="vendor-service-field-group full">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="vendor-service-textarea" placeholder="Share what couples get, package highlights, style, coverage, or special notes.">{{ $description }}</textarea>
        @error('description')
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>
</div>
