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
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->string('name');
            $table->string('code')->nullable();

            $table->string('start_location')->nullable();
            $table->string('end_location')->nullable();

            $table->decimal('distance', 10, 2)->nullable();
            $table->string('travel_time')->nullable();

            $table->decimal('fare', 12, 2)->default(0);

            $table->string('status')->default('Active');

            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_routes');
    }
};
