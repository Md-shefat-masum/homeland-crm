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
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 100); // e.g., 'leads', 'customers'
            $table->uuid('record_uuid')->index(); // Client-side stable ID
            $table->unsignedBigInteger('record_id')->nullable(); // Server ID (null until created on server)
            $table->enum('action', ['create', 'update', 'delete']);
            $table->text('data'); // JSON encoded data
            $table->string('device_id', 255)->nullable(); // Mobile device identifier
            $table->string('idempotency_key', 64)->nullable()->index(); // Prevent duplicate creates
            $table->unsignedBigInteger('depends_on_queue_id')->nullable(); // For ordering
            $table->integer('entity_version')->nullable(); // Optimistic locking
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('depends_on_queue_id')->references('id')->on('sync_queue')->onDelete('set null');

            // Indexes
            $table->index('table_name');
            $table->index('record_id');
            $table->index('status');
            $table->index('device_id');
            $table->index('next_retry_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_queue');
    }
};
