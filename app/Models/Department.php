<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['facility_id', 'name', 'code', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
