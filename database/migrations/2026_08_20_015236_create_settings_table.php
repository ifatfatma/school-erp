<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | School
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('school_id');

            /*
            |--------------------------------------------------------------------------
            | Setting Group
            |--------------------------------------------------------------------------
            */

            $table->string('group')
                ->default('general');

            /*
            |--------------------------------------------------------------------------
            | Setting Key
            |--------------------------------------------------------------------------
            */

            $table->string('key');

            /*
            |--------------------------------------------------------------------------
            | Setting Value
            |--------------------------------------------------------------------------
            */

            $table->text('value')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Value Type
            |--------------------------------------------------------------------------
            */

            $table->enum('type', [
                'string',
                'text',
                'integer',
                'decimal',
                'boolean',
                'json',
            ])->default('string');

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Created / Updated By
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->unsignedBigInteger('updated_by')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Unique Setting Per School
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'school_id',
                'group',
                'key',
            ]);

            $table->index([
                'school_id',
                'group',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('school_id')
                ->references('id')
                ->on('schools')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};