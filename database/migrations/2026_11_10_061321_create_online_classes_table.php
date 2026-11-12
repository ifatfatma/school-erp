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
        Schema::create('online_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->unsignedBigInteger('academic_session_id')->nullable()->index();
            $table->unsignedBigInteger('class_id')->index();
            $table->unsignedBigInteger('section_id')->nullable()->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->unsignedBigInteger('teacher_id')->nullable()->index();

            $table->string('title');
            $table->text('description')->nullable();

            // Platform: 'google_meet', 'zoom', 'teams', 'jitsi', 'other'
            $table->string('platform')->default('google_meet');
            $table->text('meeting_link');
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();

            $table->date('class_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->nullable();

            // Status: 'scheduled', 'live', 'completed', 'cancelled'
            $table->string('status')->default('scheduled')->index();

            $table->text('join_instructions')->nullable();
            $table->text('recorded_link')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_classes');
    }
};
