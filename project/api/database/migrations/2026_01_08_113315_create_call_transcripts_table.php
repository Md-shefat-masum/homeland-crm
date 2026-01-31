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
        Schema::create('call_transcripts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('call_log_id')->unique();
            $table->text('transcript_text'); // Bangla text from speech-to-text
            $table->string('original_audio_path', 500)->nullable();
            $table->string('language', 10)->default('bn-BD'); // Bangla Bangladesh
            $table->decimal('confidence_score', 5, 2)->nullable(); // 0.00 to 1.00
            $table->boolean('is_edited')->default(false);
            $table->text('edited_text')->nullable(); // If user edited the transcript
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->enum('processing_mode', ['online_async', 'offline_local'])->default('online_async');
            $table->text('processing_error')->nullable();
            $table->enum('sync_status', ['synced', 'pending', 'failed'])->default('synced');
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('call_log_id')->references('id')->on('call_logs')->onDelete('cascade');

            // Indexes
            $table->index('call_log_id');
            $table->index('processing_status');
            $table->index('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_transcripts');
    }
};
