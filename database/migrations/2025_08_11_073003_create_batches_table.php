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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers'); // Changed from users to customers
            $table->foreignId('product_id')->constrained();
            $table->string('batch_code')->unique();
            $table->float('weight');
            $table->string('variety')->nullable();
            $table->date('planting_date')->nullable();
            $table->date('harvest_date')->nullable();
            $table->string('cultivation_method')->nullable();
            $table->string('location')->nullable();
            $table->string('gps_coordinates')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->string('certification_number')->nullable();
            $table->date('certification_expiry')->nullable();
            $table->string('water_usage')->nullable();
            $table->string('carbon_footprint')->nullable();
            $table->string('pesticide_usage')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamp('qr_expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
