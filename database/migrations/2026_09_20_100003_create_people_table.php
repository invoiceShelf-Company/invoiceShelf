<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_number')->nullable()->unique();
            $table->string('full_name');
            $table->string('person_type')->default('employee');
            $table->string('job_title')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('national_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->date('join_date')->nullable();
            $table->string('status')->default('active');
            $table->string('work_location')->nullable();
            $table->string('floor')->nullable();
            $table->string('room')->nullable();
            $table->string('security_company')->nullable();
            $table->string('security_permit_number')->nullable();
            $table->date('security_permit_expires_at')->nullable();
            $table->string('guard_post')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['facility_id', 'person_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
