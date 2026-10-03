<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['person_id', 'attendance_date', 'scheduled_start', 'scheduled_end', 'check_in', 'check_out', 'worked_minutes', 'late_minutes', 'early_leave_minutes', 'overtime_minutes', 'status', 'notes'];

    protected $casts = ['attendance_date' => 'date'];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function formatMinutes(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    public function getWorkedHoursAttribute(): string
    {
        return $this->formatMinutes($this->worked_minutes);
    }

    public function getOvertimeHoursAttribute(): string
    {
        return $this->formatMinutes($this->overtime_minutes);
    }
}
