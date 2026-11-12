<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_room_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('hostel_room_id');
            $table->unsignedBigInteger('hostel_bed_id')->nullable();
            $table->date('allocated_date');
            $table->date('vacated_date')->nullable();
            $table->decimal('monthly_fee', 12, 2)->default(0);
            $table->string('status')->default('active'); // active, vacated
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('student_id');
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

            $table->foreign('hostel_room_id')
                ->references('id')
                ->on('hostel_rooms')
                ->onDelete('cascade');

            $table->foreign('hostel_bed_id')
                ->references('id')
                ->on('hostel_beds')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_room_allocations');
    }
};
