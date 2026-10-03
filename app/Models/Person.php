<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $fillable = ['facility_id', 'department_id', 'shift_id', 'user_id', 'employee_number', 'full_name', 'person_type', 'job_title', 'phone', 'email', 'national_id', 'birth_date', 'gender', 'join_date', 'status', 'work_location', 'floor', 'room', 'security_company', 'security_permit_number', 'security_permit_expires_at', 'guard_post', 'notes'];

    protected $casts = ['birth_date' => 'date', 'join_date' => 'date', 'security_permit_expires_at' => 'date'];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }

    public function typeLabel(): string
    {
        return match ($this->person_type) {
            'employee' => 'موظف', 'worker' => 'عامل', 'security_guard' => 'حارس أمن', 'manager' => 'مدير', 'supervisor' => 'مشرف', 'accountant' => 'محاسب', 'driver' => 'سائق', 'technician' => 'فني', 'cleaner' => 'عامل نظافة', 'sales' => 'مبيعات', 'hr' => 'موارد بشرية', 'contractor' => 'مقاول', 'tenant' => 'مستأجر', default => 'أخرى'
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'active' => 'نشط', 'on_leave' => 'إجازة', 'suspended' => 'موقوف', 'terminated' => 'منتهي الخدمة', default => $this->status
        };
    }
}
