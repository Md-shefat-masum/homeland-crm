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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_source', 255)->nullable(); // Where lead came from
            $table->unsignedBigInteger('lead_source_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->text('customer_requirement')->nullable();
            $table->string('preferred_area', 255)->nullable();
            $table->date('next_contact_date')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('interested_type_id')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'lost'])->default('new');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('sync_status', ['synced', 'pending', 'failed'])->default('synced');
            $table->timestamp('sync_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('interested_type_id')->references('id')->on('interested_types')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index('customer_id');
            $table->index('project_id');
            $table->index('interested_type_id');
            $table->index('status');
            $table->index('next_contact_date');
            $table->index('sync_status');
            $table->index('created_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
