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
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100); // e.g., "job", "business", "student", "housewife"
            $table->string('business_type', 255)->nullable(); // If type is "business"
            $table->string('job_title', 255)->nullable(); // If type is "job"
            $table->string('company_name', 255)->nullable(); // If type is "job"
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professions');
    }
};
