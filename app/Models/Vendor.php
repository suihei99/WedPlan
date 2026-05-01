<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    // use HasFactory; // Uncomment if you want to use factories for testing
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'contact_number',
        'status',
        'address',
        'business_documents',
    ];

    protected $appends = [
        'business_document_url',
    ];

    protected function casts()
    {
        return [
            'status' => 'string',
            'business_documents' => 'string',
        ];
    }

    // Define the relationship with the User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the Servies model
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'user_id', 'user_id');
    }

    // Define the relationship with the Bookings model
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id', 'user_id');
    }

    public function getBusinessDocumentUrlAttribute(): ?string
    {
        if (! $this->business_documents) {
            return null;
        }

        return asset('storage/'.ltrim($this->business_documents, '/'));
    }
}
