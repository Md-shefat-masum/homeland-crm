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
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->string('recipient_mobile', 20);
            $table->text('message');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'delivered'])->default('pending');
            $table->text('provider_response')->nullable(); // Response from SMS gateway
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('scheduled_at')->nullable(); // For scheduled SMS
            $table->enum('sync_status', ['synced', 'pending', 'failed'])->default('synced');
            $table->timestamp('sync_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('set null');
            $table->foreign('template_id')->references('id')->on('sms_templates')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index('customer_id');
            $table->index('lead_id');
            $table->index('recipient_mobile');
            $table->index('status');
            $table->index('sent_at');
            $table->index('scheduled_at');
            $table->index('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
