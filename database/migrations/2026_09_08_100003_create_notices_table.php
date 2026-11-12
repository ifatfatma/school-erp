<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('content');
            $table->date('publish_date')->default(now()->toDateString());
            $table->date('expiry_date')->nullable();
            $table->enum('target_audience', ['all', 'public', 'students', 'parents', 'teachers'])->default('all');
            $table->string('attachment')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
