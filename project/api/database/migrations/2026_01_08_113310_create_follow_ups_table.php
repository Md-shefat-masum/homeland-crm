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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('customer_id');
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->enum('follow_up_type', ['call', 'visit', 'meeting', 'email', 'sms'])->default('call');
            $table->enum('status', ['pending', 'completed', 'cancelled', 'rescheduled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('reminder_sent')->default(false);
            $table->enum('sync_status', ['synced', 'pending', 'failed'])->default('synced');
            $table->timestamp('sync_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index('lead_id');
            $table->index('customer_id');
            $table->index('scheduled_date');
            $table->index('status');
            $table->index('sync_status');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
