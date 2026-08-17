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
        Schema::create('books', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('book_categories')
                ->restrictOnDelete();

            $table->foreignId('publisher_id')
                ->nullable()
                ->constrained('publishers')
                ->nullOnDelete();

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
                'title'
            ]);

            $table->unique([
                'school_id',
                'book_code'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
