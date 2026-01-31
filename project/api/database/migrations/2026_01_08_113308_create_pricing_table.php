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
        Schema::create('pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->unique();
            $table->decimal('quoted_price', 15, 2)->nullable(); // Price quoted to customer
            $table->decimal('customer_budget', 15, 2)->nullable(); // Customer's budget
            $table->decimal('agreeable_price', 15, 2)->nullable(); // Agreed price
            $table->string('currency', 3)->default('BDT');
            $table->timestamps();

            // Foreign keys
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');

            // Indexes
            $table->index('lead_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing');
    }
};
