<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publishers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('code')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('phone', 30)
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('website')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->boolean('status')
                ->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'school_id',
                'status'
            ]);

            $table->index([
                'school_id',
                'name'
            ]);

            $table->unique([
                'school_id',
                'code'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publishers');
    }
};