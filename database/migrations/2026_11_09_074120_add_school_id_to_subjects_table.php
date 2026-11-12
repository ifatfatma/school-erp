<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('subjects', 'school_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id')->index();
            });

            // Backfill existing records to school 1 if exists
            $defaultSchoolId = DB::table('schools')->orderBy('id')->value('id') ?? 1;
            DB::table('subjects')->whereNull('school_id')->update(['school_id' => $defaultSchoolId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('subjects', 'school_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn('school_id');
            });
        }
    }
};
