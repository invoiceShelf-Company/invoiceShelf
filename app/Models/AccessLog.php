<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    protected $fillable = ['facility_id', 'person_id', 'visitor_id', 'gate', 'action', 'logged_at', 'recorded_by', 'notes'];

    protected $casts = ['logged_at' => 'datetime'];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
