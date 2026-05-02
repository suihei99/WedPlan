<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    // Define constants for user roles
    const ROLE_ADMIN = 'admin';

    const ROLE_COUPLE = 'couple';

    const ROLE_VENDOR = 'vendor';

    // All valid roles for easy validation
    const ROLES = [self::ROLE_ADMIN, self::ROLE_COUPLE, self::ROLE_VENDOR];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'device_token',
        'profile_photo_path',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'device_token',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Define the relationship with the Couple model
    public function couple(): HasOne
    {
        return $this->hasOne(Couple::class);
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    // Admin has no specific relationship, as they are just users with admin role and can manage both couples and vendors

    // Role check helper methods
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCouple(): bool
    {
        return $this->role === self::ROLE_COUPLE;
    }

    public function isVendor(): bool
    {
        return $this->role === self::ROLE_VENDOR;
    }

    // Dynamic user accesor to get the profile information based on the role
    public function getProfileAttribute()
    {
        if ($this->isCouple()) {
            return $this->couple; // Return the related couple profile
        } elseif ($this->isVendor()) {
            return $this->vendor; // Return the related vendor profile
        }

        return null; // Admins do not have a specific profile
    }

    // Query Scope to filter users by role
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeCouples($query)
    {
        return $query->where('role', self::ROLE_COUPLE);
    }

    public function scopeVendors($query)
    {
        return $query->where('role', self::ROLE_VENDOR);
    }
}
