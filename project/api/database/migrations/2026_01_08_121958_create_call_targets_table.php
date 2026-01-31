<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('call_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Employee
            $table->date('target_date'); // Target date (for daily) or start date (for weekly/monthly)
            $table->enum('target_type', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->integer('target_count'); // How many calls to make
            $table->integer('completed_count')->default(0); // How many completed
            $table->date('period_start_date')->nullable(); // For weekly/monthly targets
            $table->date('period_end_date')->nullable(); // For weekly/monthly targets
            $table->unsignedBigInteger('set_by'); // Admin/Manager who set the target
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('set_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index('user_id');
            $table->index('target_date');
            $table->index('target_type');
            $table->index('is_active');
            $table->index('set_by');
            $table->index(['user_id', 'target_date', 'target_type']); // Composite for employee target view
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_targets');
    }
};
