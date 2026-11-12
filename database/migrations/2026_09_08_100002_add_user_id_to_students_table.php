<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('school_id')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('guardians', function (Blueprint $table) {
            if (!Schema::hasColumn('guardians', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('school_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('guardians', function (Blueprint $table) {
            if (Schema::hasColumn('guardians', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
