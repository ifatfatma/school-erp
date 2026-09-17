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
        Schema::create('transport_vehicle_maintenances', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('school_id');
    $table->unsignedBigInteger('transport_vehicle_id');

    $table->string('maintenance_no')->nullable();

    $table->date('maintenance_date');

    $table->string('maintenance_type');

    $table->decimal('odometer', 12, 2)->nullable();

    $table->string('service_provider')->nullable();

    $table->text('description')->nullable();

    $table->decimal('cost', 12, 2)->default(0);

    $table->date('next_service_date')->nullable();

    $table->decimal('next_service_odometer', 12, 2)->nullable();

    $table->string('status')->default('Completed');

    $table->text('remarks')->nullable();

    $table->unsignedBigInteger('created_by')->nullable();

    $table->timestamps();
    $table->softDeletes();

    $table->index('school_id');
    $table->index('transport_vehicle_id');
    $table->index('maintenance_date');
    $table->index('status');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_vehicle_maintenances');
    }
};
