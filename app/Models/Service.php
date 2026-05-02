<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use HasFactory;

    public const SERVICE_TYPES = [
        'Venue',
        'Catering',
        'Photography',
        'Makeup Artist',
        'Wedding Planner',
        'Bridal Wear',
        'Decor & Styling',
        'Entertainment',
        'Transportation',
        'Other',
    ];

    protected $fillable = [
        'user_id',
        'service_name',
        'type_service',
        'price_estimate',
        'description',
        'image_url',
    ];

    protected $appends = [
        'image_url_resolved',
    ];

    protected function casts(): array
    {
        return [
            'price_estimate' => 'decimal:2',
            'description' => 'string',
            'image_url' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getImageUrlResolvedAttribute(): ?string
    {
        $path = $this->image_url;

        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/'.ltrim($path, '/'));
        }

        if (str_starts_with($path, 'public/')) {
            return asset('storage/'.ltrim(substr($path, 7), '/'));
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
