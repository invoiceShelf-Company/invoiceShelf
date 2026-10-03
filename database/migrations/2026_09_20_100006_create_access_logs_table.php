<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gate')->nullable();
            $table->string('action')->default('entry');
            $table->dateTime('logged_at');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['facility_id', 'logged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
