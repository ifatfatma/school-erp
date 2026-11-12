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
            $table->string('subscription_plan')->default('starter')->after('status');
            $table->string('subscription_status')->default('active')->after('subscription_plan');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');
            $table->json('subscription_features')->nullable()->after('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_plan',
                'subscription_status',
                'subscription_expires_at',
                'subscription_features',
            ]);
        });
    }
};
