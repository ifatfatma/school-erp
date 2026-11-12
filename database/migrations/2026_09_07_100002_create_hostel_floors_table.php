<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_floors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('hostel_building_id');
            $table->integer('floor_no')->default(0);
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('hostel_floors');
    }
};
