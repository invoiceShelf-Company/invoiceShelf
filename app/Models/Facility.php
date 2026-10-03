<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    protected $fillable = ['name', 'code', 'type', 'phone', 'email', 'address', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'company' => 'شركة', 'mall' => 'مول تجاري', 'store' => 'محل تجاري', 'property' => 'عقار / مبنى', default => 'أخرى'
        };
    }
}
