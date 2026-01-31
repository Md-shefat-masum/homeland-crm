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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('mobile', 20)->unique();
            $table->string('email', 255)->nullable();
            $table->string('image')->default('avatar.png')->nullable();
            $table->string('alternative_mobile', 20)->nullable();
            $table->unsignedBigInteger('customer_group_id')->nullable();
            $table->unsignedBigInteger('profession_id')->nullable();
            $table->unsignedBigInteger('current_address_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->text('current_address_text')->nullable();
            $table->string('nearest_market', 255)->nullable();
            $table->string('preferred_area', 255)->nullable();
            $table->text('target_real_estate')->nullable(); // JSON or text
            $table->text('notes')->nullable();
            $table->json('info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('set null');
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('set null');
            $table->foreign('current_address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');

            // Indexes
            $table->index('mobile');
            $table->index('email');
            $table->index('customer_group_id');
            $table->index('profession_id');
            $table->index('current_address_id');
            $table->index('project_id');
            $table->index('is_active');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
