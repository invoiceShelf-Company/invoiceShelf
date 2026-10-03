<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = ['facility_id', 'name', 'start_time', 'end_time', 'grace_minutes', 'break_minutes', 'is_overnight', 'is_active'];

    protected $casts = ['is_overnight' => 'boolean', 'is_active' => 'boolean'];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
