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
        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->string('vehicle_no');
            $table->string('registration_no')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('model')->nullable();

            $table->unsignedInteger('capacity')->nullable();

            $table->date('purchase_date')->nullable();

            $table->date('insurance_expiry')->nullable();
            $table->date('fitness_expiry')->nullable();
            $table->date('pollution_expiry')->nullable();

            $table->string('status')->default('Active');

            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('registration_no');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_vehicles');
    }
};
