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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('file_name', 255);
            $table->string('file_path', 500); // Local file path
            $table->string('google_drive_file_id', 255)->nullable()->unique();
            $table->unsignedBigInteger('file_size'); // In bytes
            $table->enum('backup_type', ['full', 'incremental'])->default('full');
            $table->enum('status', ['pending', 'uploading', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // Nullable for automated backups
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('status');
            $table->index('google_drive_file_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
