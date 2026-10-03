<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->time('scheduled_start')->nullable();
            $table->time('scheduled_end')->nullable();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_leave_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->string('status')->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['person_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
