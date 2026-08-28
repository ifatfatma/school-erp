<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_units', function (Blueprint $table) {

            if (!Schema::hasColumn('inventory_units', 'code')) {
                $table->string('code', 100)
                    ->nullable()
                    ->after('name');
            }

            if (!Schema::hasColumn('inventory_units', 'description')) {
                $table->text('description')
                    ->nullable()
                    ->after('code');
            }

            if (!Schema::hasColumn('inventory_units', 'status')) {
                $table->boolean('status')
                    ->default(true)
                    ->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_units', function (Blueprint $table) {

            if (Schema::hasColumn('inventory_units', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('inventory_units', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('inventory_units', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};