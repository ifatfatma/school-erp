<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_id');
            $table->string('visitor_name');
            $table->string('relation');
            $table->string('visitor_phone')->nullable();
            $table->date('visit_date');
            $table->string('in_time')->nullable();
            $table->string('out_time')->nullable();
            $table->string('id_proof_type')->nullable();
            $table->string('id_proof_number')->nullable();
            $table->string('purpose')->nullable();
            $table->string('status')->default('inside'); // inside, checked_out
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('student_id');
            $table->index('visit_date');
            $table->index('status');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_visitors');
    }
};
