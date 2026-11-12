<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_beds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('hostel_room_id');
            $table->string('bed_no');
            $table->string('status')->default('available'); // available, occupied, maintenance
            $table->decimal('monthly_fee', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('hostel_room_id');
            $table->index('status');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');

            $table->foreign('hostel_room_id')
                ->references('id')
                ->on('hostel_rooms')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_beds');
    }
};
