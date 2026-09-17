<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_buildings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->string('name');
            $table->string('code')->nullable();

            $table->string('building_type')->nullable();
            $table->unsignedInteger('total_floors')->default(1);
            $table->unsignedInteger('total_rooms')->default(0);
            $table->unsignedInteger('total_beds')->default(0);

            $table->string('warden_name')->nullable();
            $table->string('warden_phone')->nullable();

            $table->text('address')->nullable();
            $table->text('description')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('status');

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_buildings');
    }
};