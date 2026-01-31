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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->string('phone_number', 20);
            $table->enum('call_type', ['incoming', 'outgoing', 'missed'])->default('incoming');
            $table->enum('call_status', ['ringing', 'answered', 'ended', 'missed', 'rejected'])->default('ringing');
            $table->integer('duration_seconds')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('recording_path', 500)->nullable(); // Encrypted path to recording
            $table->boolean('is_recorded')->default(false);
            $table->enum('recording_consent_status', ['unknown', 'granted', 'denied'])->default('unknown');
            $table->timestamp('recording_consent_at')->nullable();
            $table->boolean('auto_opened_app')->default(false);
            $table->enum('sync_status', ['synced', 'pending', 'failed'])->default('synced');
            $table->timestamp('sync_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // Nullable for auto-detected calls
            $table->timestamps();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('customer_id');
            $table->index('lead_id');
            $table->index('phone_number');
            $table->index('call_type');
            $table->index('started_at');
            $table->index('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
