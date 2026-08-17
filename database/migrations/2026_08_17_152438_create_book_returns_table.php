<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_returns', function (Blueprint $table) {

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
            | Book Issue
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_issue_id')
                ->constrained('book_issues')
                ->restrictOnDelete();


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
            | Book Copy
            |--------------------------------------------------------------------------
            */

            $table->foreignId('book_copy_id')
                ->constrained('book_copies')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Return Information
            |--------------------------------------------------------------------------
            */

            $table->string('return_no');

            $table->date('return_date');

            $table->date('due_date');


            /*
            |--------------------------------------------------------------------------
            | Overdue
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('overdue_days')
                ->default(0);


            /*
            |--------------------------------------------------------------------------
            | Fine
            |--------------------------------------------------------------------------
            */

            $table->decimal(
                'fine_amount',
                12,
                2
            )->default(0);


            /*
            |--------------------------------------------------------------------------
            | Fine Status
            |--------------------------------------------------------------------------
            */

            $table->enum('fine_status', [
                'No Fine',
                'Pending',
                'Paid',
            ])->default('No Fine');


            /*
            |--------------------------------------------------------------------------
            | Book Condition
            |--------------------------------------------------------------------------
            */

            $table->enum('book_condition', [
                'Good',
                'Damaged',
                'Lost',
            ])->default('Good');


            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Returned By
            |--------------------------------------------------------------------------
            */

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
                'return_no',
            ]);

            /*
             * One issue can only be returned once.
             */
            $table->unique(
                'book_issue_id'
            );

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
                'return_date',
            ]);

            $table->index([
                'school_id',
                'fine_status',
            ]);

            $table->index([
                'school_id',
                'book_condition',
            ]);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('book_returns');
    }
};