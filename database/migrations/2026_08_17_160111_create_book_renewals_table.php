<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_renewals', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | School
            |--------------------------------------------------------------------------
            */

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Original Issue
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_issue_id')
                ->constrained('book_issues')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Member
            |--------------------------------------------------------------------------
            */

            $table->foreignId('library_member_id')
                ->constrained('library_members')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Book
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_id')
                ->constrained('books')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Book Copy
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_copy_id')
                ->constrained('book_copies')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Renewal Information
            |--------------------------------------------------------------------------
            */

            $table->string('renewal_no');

            $table->date('renewal_date');

            $table->date('old_due_date');

            $table->date('new_due_date');


            /*
            |--------------------------------------------------------------------------
            | Renewal Number
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('renewal_count')
                ->default(1);


            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Renewed By
            |--------------------------------------------------------------------------
            */

            $table->foreignId('renewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'school_id',
                'renewal_no',
            ]);

            $table->index([
                'school_id',
                'book_issue_id',
            ]);

            $table->index([
                'school_id',
                'library_member_id',
            ]);

            $table->index([
                'school_id',
                'book_id',
            ]);

            $table->index([
                'school_id',
                'book_copy_id',
            ]);

            $table->index([
                'school_id',
                'renewal_date',
            ]);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('book_renewals');
    }
};