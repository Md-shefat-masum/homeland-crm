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
        Schema::create('customer_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // User who will call
            $table->unsignedBigInteger('customer_id'); // Customer to call
            $table->date('assigned_date'); // Date when employee should call
            $table->unsignedBigInteger('assigned_by'); // Admin/Manager who assigned
            $table->enum('status', ['pending', 'completed', 'cancelled', 'skipped'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('call_log_id')->nullable(); // Link to actual call if made
            $table->enum('sync_status', ['synced', 'pending', 'failed'])->default('synced');
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('call_log_id')->references('id')->on('call_logs')->onDelete('set null');

            // Indexes
            $table->index('employee_id');
            $table->index('customer_id');
            $table->index('assigned_date');
            $table->index('status');
            $table->index('assigned_by');
            $table->index('sync_status');
            $table->index(['employee_id', 'assigned_date', 'status']); // Composite for employee daily view
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_assignments');
    }
};
