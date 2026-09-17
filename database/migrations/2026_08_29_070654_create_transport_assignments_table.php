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
        Schema::create('transport_assignments', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('school_id');

    $table->unsignedBigInteger('transport_route_id');
    $table->unsignedBigInteger('transport_route_stop_id')->nullable();

    $table->unsignedBigInteger('transport_vehicle_id');
    $table->unsignedBigInteger('transport_driver_id');

    $table->date('assignment_date')->nullable();
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();

    $table->string('status')->default('Active');

    $table->text('remarks')->nullable();

    $table->timestamps();
    $table->softDeletes();

    $table->index('school_id');
    $table->index('transport_route_id');
    $table->index('transport_route_stop_id');
    $table->index('transport_vehicle_id');
    $table->index('transport_driver_id');
    $table->index('status');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_assignments');
    }
};
