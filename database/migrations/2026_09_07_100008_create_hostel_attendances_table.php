<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('hostel_room_id')->nullable();
            $table->date('attendance_date');
            $table->string('status')->default('present'); // present, absent, leave, late
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('student_id');
            $table->index('attendance_date');
            $table->index('status');
            $table->unique(['school_id', 'student_id', 'attendance_date'], 'hostel_student_att_unique');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('marked_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_attendances');
    }
};
