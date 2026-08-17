<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {

            $table->id();

            // School
            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            // Category
            $table->foreignId('category_id')
                ->constrained('book_categories')
                ->restrictOnDelete();

            // Publisher
            $table->foreignId('publisher_id')
                ->nullable()
                ->constrained('publishers')
                ->nullOnDelete();

            // Book information
            $table->string('title');

            $table->string('book_code');

            $table->string('isbn')
                ->nullable();

            $table->string('edition')
                ->nullable();

            $table->string('language')
                ->nullable();

            $table->unsignedSmallInteger('publication_year')
                ->nullable();

            $table->unsignedInteger('pages')
                ->nullable();

            $table->decimal('price', 12, 2)
                ->default(0);

            $table->text('description')
                ->nullable();

            $table->string('cover_image')
                ->nullable();

            // Status
            $table->boolean('status')
                ->default(true);

            // Audit
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

            // Indexes
            $table->index([
                'school_id',
                'status'
            ]);

            $table->index([
                'school_id',
                'title'
            ]);

            $table->index([
                'school_id',
                'category_id'
            ]);

            $table->index([
                'school_id',
                'publisher_id'
            ]);

            // Book code must be unique within a school
            $table->unique([
                'school_id',
                'book_code'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};