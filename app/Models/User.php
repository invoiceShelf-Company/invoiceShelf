<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
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
            'last_login_at' => 'datetime',
            'role' => UserRole::class,
        ];
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    /**
     * Relationship: products created by this user.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    /**
     * Relationship: stock movements created by this user.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function person(): HasOne
    {
        return $this->hasOne(Person::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }
}
