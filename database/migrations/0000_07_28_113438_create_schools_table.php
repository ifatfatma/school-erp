<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('code')->unique();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->text('address')->nullable();

            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            $table->string('logo')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();

            // Add this later only if you want soft deletes
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};