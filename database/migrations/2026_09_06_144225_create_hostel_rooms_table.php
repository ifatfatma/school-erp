<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->unsignedBigInteger('hostel_building_id');

            $table->string('room_no');
            $table->unsignedInteger('floor_no')->default(1);

            $table->string('room_type')->nullable();

            $table->unsignedInteger('capacity')->default(1);
            $table->unsignedInteger('occupied_beds')->default(0);

            $table->decimal('monthly_fee', 12, 2)->default(0);

            $table->boolean('status')->default(true);

            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('hostel_building_id');
            $table->index('status');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('hostel_building_id')
                ->references('id')
                ->on('hostel_buildings')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_rooms');
    }
};