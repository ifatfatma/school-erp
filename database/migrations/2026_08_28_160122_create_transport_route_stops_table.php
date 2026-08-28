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
        Schema::create('transport_route_stops', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('transport_route_id');

            $table->string('stop_name');
            $table->string('stop_code')->nullable();

            $table->integer('stop_order')->default(1);

            $table->time('pickup_time')->nullable();
            $table->time('drop_time')->nullable();

            $table->decimal('fare', 12, 2)->default(0);

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('school_id');
            $table->index('transport_route_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_route_stops');
    }
};
