<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_issues', function (Blueprint $table) {

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
            | Library Member
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
            | Physical Book Copy
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_copy_id')
                ->constrained('book_copies')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Issue Information
            |--------------------------------------------------------------------------
            */

            $table->string('issue_no');

            $table->date('issue_date');

            $table->date('due_date');


            /*
            |--------------------------------------------------------------------------
            | Return Information
            |--------------------------------------------------------------------------
            */

            $table->date('return_date')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'Issued',
                'Returned',
                'Overdue',
                'Lost',
                'Damaged',
            ])->default('Issued');


            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('returned_by')
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
                'issue_no',
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
                'status',
            ]);

            $table->index([
                'school_id',
                'due_date',
            ]);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};