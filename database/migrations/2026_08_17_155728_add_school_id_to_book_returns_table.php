<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_returns', function (Blueprint $table) {

            $table->foreignId('school_id')
                ->nullable()
                ->after('id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->index([
                'school_id',
                'return_date',
            ]);

            $table->index([
                'school_id',
                'fine_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('book_returns', function (Blueprint $table) {

            $table->dropForeign([
                'school_id'
            ]);

            $table->dropIndex([
                'book_returns_school_id_return_date_index'
            ]);

            $table->dropIndex([
                'book_returns_school_id_fine_status_index'
            ]);

            $table->dropColumn('school_id');
        });
    }
};