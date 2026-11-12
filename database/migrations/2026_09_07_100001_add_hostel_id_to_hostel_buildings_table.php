<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostel_buildings', function (Blueprint $table) {
            $table->unsignedBigInteger('hostel_id')->nullable()->after('school_id');

            $table->foreign('hostel_id')
                ->references('id')
                ->on('hostels')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('hostel_buildings', function (Blueprint $table) {
            $table->dropForeign(['hostel_id']);
            $table->dropColumn('hostel_id');
        });
    }
};
