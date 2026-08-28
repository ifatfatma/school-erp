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
        Schema::create('transport_drivers', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('school_id');

    $table->string('name');
    $table->string('code')->nullable();
    $table->string('phone')->nullable();
    $table->string('email')->nullable();

    $table->string('license_no')->nullable();
    $table->date('license_expiry')->nullable();

    $table->date('joining_date')->nullable();

    $table->text('address')->nullable();
    $table->text('remarks')->nullable();

    $table->string('status')->default('Active');

    $table->timestamps();
    $table->softDeletes();

    $table->index('school_id');
    $table->index('license_no');
    $table->index('status');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_drivers');
    }
};
