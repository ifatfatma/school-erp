<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->default('co-ed'); // boys, girls, co-ed
            $table->string('warden_name')->nullable();
            $table->string('warden_phone')->nullable();
            $table->string('warden_email')->nullable();
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
        Schema::dropIfExists('hostels');
    }
};
