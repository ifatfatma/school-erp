<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('custom_domain', 191)->nullable()->unique()->after('code');
            $table->string('domain_status', 30)->default('none')->after('custom_domain'); // 'none', 'pending', 'active', 'failed'
            $table->timestamp('domain_verified_at')->nullable()->after('domain_status');
            $table->string('dns_cname_target', 191)->nullable()->after('domain_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'custom_domain',
                'domain_status',
                'domain_verified_at',
                'dns_cname_target',
            ]);
        });
    }
};
