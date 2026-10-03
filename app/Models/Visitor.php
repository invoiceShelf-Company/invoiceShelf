<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    protected $fillable = ['facility_id', 'name', 'phone', 'national_id', 'company', 'host_name', 'purpose', 'visitor_number', 'notes'];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }
}
