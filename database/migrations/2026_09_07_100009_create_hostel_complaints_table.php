<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('hostel_room_id')->nullable();
            $table->string('title');
            $table->string('category')->default('General'); // Room / Bed, Electrical, Plumbing, Cleanliness, Food / Mess, Discipline, Other
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('pending'); // pending, in_progress, resolved, closed
            $table->text('description');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('student_id');
            $table->index('status');
            $table->index('priority');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('set null');

            $table->foreign('hostel_room_id')
                ->references('id')
                ->on('hostel_rooms')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_complaints');
    }
};
