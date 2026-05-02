<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
