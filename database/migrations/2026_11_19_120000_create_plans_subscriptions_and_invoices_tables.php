<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->decimal('price', 10, 2);
                $table->integer('max_schools')->default(1);
                $table->json('features')->nullable();
                $table->text('description')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
                $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
                $table->string('status')->default('active'); // active, cancelled, expired
                $table->integer('max_schools')->default(1);
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
                $table->foreignId('current_plan_id')->nullable()->constrained('plans')->nullOnDelete();
                $table->foreignId('target_plan_id')->constrained('plans')->cascadeOnDelete();
                $table->string('type')->default('plan_upgrade'); // plan_upgrade, subscription
                $table->decimal('current_plan_price', 10, 2)->default(0);
                $table->decimal('target_plan_price', 10, 2)->default(0);
                $table->decimal('credit_amount', 10, 2)->default(0);
                $table->decimal('net_amount', 10, 2)->default(0);
                $table->string('status')->default('pending'); // pending, paid, cancelled
                $table->string('payment_method')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }

        // Seed the 3 standard subscription tiers as required
        $now = now();
        $plans = [
            [
                'name' => 'Starter / Basic Plan',
                'slug' => 'basic',
                'price' => 2499.00,
                'max_schools' => 1,
                'features' => json_encode([
                    'students', 'attendance', 'teachers', 'academics', 'fees', 'examinations', 'portal', 'online_classes'
                ]),
                'description' => 'Ideal for single institutions requiring core digital management.',
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Professional / Growth Plan',
                'slug' => 'professional',
                'price' => 4999.00,
                'max_schools' => 2,
                'features' => json_encode([
                    'students', 'attendance', 'teachers', 'academics', 'fees', 'examinations', 'portal', 'online_classes',
                    'website_cms', 'online_admissions', 'library', 'transport', 'hostel', 'inventory', 'payment_gateway'
                ]),
                'description' => 'For growing institutions managing up to 2 schools with advanced modules.',
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Enterprise / Multi-Branch Plan',
                'slug' => 'enterprise',
                'price' => 9999.00,
                'max_schools' => 5,
                'features' => json_encode([
                    'students', 'attendance', 'teachers', 'academics', 'fees', 'examinations', 'portal', 'online_classes',
                    'website_cms', 'online_admissions', 'library', 'transport', 'hostel', 'inventory', 'payment_gateway',
                    'custom_domain', 'multi_branch', 'api_access', 'advanced_reports'
                ]),
                'description' => 'Complete multi-campus suite supporting up to 5 schools with custom integrations.',
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($plans as $p) {
            DB::table('plans')->updateOrInsert(['slug' => $p['slug']], $p);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
