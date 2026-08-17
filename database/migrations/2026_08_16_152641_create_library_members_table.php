<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_members', function (Blueprint $table) {

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
            | Member Information
            |--------------------------------------------------------------------------
            */

            $table->string('member_no');

            $table->enum('member_type', [
                'Student',
                'Staff',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Related Person
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('student_id')
                ->nullable();

            $table->unsignedBigInteger('staff_id')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            $table->string('phone')
                ->nullable();

            $table->string('email')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Membership
            |--------------------------------------------------------------------------
            */

            $table->date('membership_date');

            $table->date('expiry_date')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Issue Limit
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('issue_limit')
                ->default(3);


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('status')
                ->default(true);


            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index([
                'school_id',
                'member_type',
            ]);

            $table->index([
                'school_id',
                'student_id',
            ]);

            $table->index([
                'school_id',
                'staff_id',
            ]);

            $table->index([
                'school_id',
                'status',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Unique Member Number
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'school_id',
                'member_no',
            ]);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('library_members');
    }
};