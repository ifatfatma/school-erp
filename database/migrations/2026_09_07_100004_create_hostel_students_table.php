<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('hostel_id')->nullable();
            $table->unsignedBigInteger('hostel_room_id')->nullable();
            $table->unsignedBigInteger('hostel_bed_id')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('status')->default('active'); // active, vacated, suspended
            $table->string('food_preference')->default('both'); // veg, non-veg, both
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('student_id');
            $table->index('hostel_id');
            $table->index('hostel_room_id');
            $table->index('hostel_bed_id');
            $table->index('status');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('hostel_id')
                ->references('id')
                ->on('hostels')
                ->onDelete('set null');

            $table->foreign('hostel_room_id')
                ->references('id')
                ->on('hostel_rooms')
                ->onDelete('set null');

            $table->foreign('hostel_bed_id')
                ->references('id')
                ->on('hostel_beds')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_students');
    }
};
