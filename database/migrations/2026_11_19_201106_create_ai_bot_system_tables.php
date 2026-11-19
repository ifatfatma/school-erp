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
        // 1. AI Knowledge Base / FAQ rules table
        Schema::create('ai_bot_faqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable()->index();
            $table->string('category', 60)->default('general')->index(); // admission, fees, attendance, exams, contact, portal, etc.
            $table->text('question');
            $table->text('answer');
            $table->text('keywords')->nullable(); // comma-separated keywords for instant search
            $table->string('action_label', 100)->nullable(); // e.g. "Apply for Admission"
            $table->string('action_url')->nullable(); // e.g. "/apply-admission"
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('clicks_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // 2. AI Bot Conversation Logs / Analytics table
        Schema::create('ai_bot_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 100)->index();
            $table->text('user_query');
            $table->text('bot_response');
            $table->string('matched_category', 60)->nullable();
            $table->unsignedBigInteger('matched_faq_id')->nullable();
            $table->float('confidence_score')->default(1.0);
            $table->boolean('was_helpful')->nullable();
            $table->boolean('escalated_to_whatsapp')->default(false);
            $table->string('user_ip', 45)->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });

        // 3. AI Bot & WhatsApp Support Configuration table
        Schema::create('ai_bot_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable()->unique();
            $table->string('bot_name', 100)->default('School AI Assistant');
            $table->string('bot_avatar')->nullable();
            $table->text('welcome_message')->nullable();
            $table->string('whatsapp_number', 50)->default('+919331805678');
            $table->text('whatsapp_default_message')->nullable();
            $table->string('support_email', 150)->nullable();
            $table->string('support_phone', 50)->nullable();
            $table->string('office_hours', 150)->default('Mon - Sat: 8:00 AM - 4:00 PM');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('enable_whatsapp_button')->default(true);
            $table->boolean('enable_voice_speech')->default(true);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_bot_settings');
        Schema::dropIfExists('ai_bot_logs');
        Schema::dropIfExists('ai_bot_faqs');
    }
};
