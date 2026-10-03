<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case HR = 'hr';
    case Employee = 'employee';
    case Worker = 'worker';
    case Security = 'security';
    case WarehouseManager = 'warehouse_manager';
    case Accountant = 'accountant';
    case Viewer = 'viewer';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'مدير عام', self::Admin => 'مدير النظام', self::HR => 'الموارد البشرية', self::Employee => 'موظف', self::Worker => 'عامل', self::Security => 'حارس أمن', self::WarehouseManager => 'مدير مستودع', self::Accountant => 'محاسب', self::Viewer => 'مشاهد', self::User => 'مستخدم'
        };
    }

    public function isManagement(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin, self::HR], true);
    }
}
