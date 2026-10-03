<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider')->nullable();
            $table->string('scope')->default('local'); // local/global
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->text('instructions')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
