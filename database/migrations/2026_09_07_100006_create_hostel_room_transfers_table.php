<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_room_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('from_room_id');
            $table->unsignedBigInteger('from_bed_id')->nullable();
            $table->unsignedBigInteger('to_room_id');
            $table->unsignedBigInteger('to_bed_id')->nullable();
            $table->date('transfer_date');
            $table->text('reason')->nullable();
            $table->string('status')->default('approved'); // pending, approved, rejected
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('student_id');
            $table->index('from_room_id');
            $table->index('to_room_id');
            $table->index('status');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->foreign('from_room_id')
                ->references('id')
                ->on('hostel_rooms')
                ->onDelete('cascade');

            $table->foreign('to_room_id')
                ->references('id')
                ->on('hostel_rooms')
                ->onDelete('cascade');

            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_room_transfers');
    }
};
