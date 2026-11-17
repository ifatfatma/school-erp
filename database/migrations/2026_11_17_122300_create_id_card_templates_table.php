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
        Schema::create('id_card_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name')->default('Student Identity Card');
            $table->string('layout')->default('vertical'); // 'vertical' (54x86mm) or 'horizontal' (86x54mm)
            $table->string('theme')->default('modern'); // 'modern', 'classic', 'minimal', 'tech'
            $table->string('primary_color', 30)->default('#2563eb');
            $table->string('accent_color', 30)->default('#f59e0b');
            $table->string('bg_color', 30)->default('#ffffff');
            $table->string('header_title')->nullable();
            $table->string('header_subtitle')->nullable();
            $table->string('contact_info')->nullable();
            $table->string('academic_session')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('signature_url')->nullable();
            $table->string('signatory_title')->default('Principal');
            $table->string('photo_shape', 30)->default('rounded'); // 'rounded', 'circle', 'square'
            $table->string('barcode_type', 30)->default('qr'); // 'qr', 'barcode', 'none'
            $table->boolean('show_roll_no')->default(true);
            $table->boolean('show_admission_no')->default(true);
            $table->boolean('show_class_section')->default(true);
            $table->boolean('show_blood_group')->default(true);
            $table->boolean('show_dob')->default(true);
            $table->boolean('show_father_name')->default(true);
            $table->boolean('show_emergency_phone')->default(true);
            $table->boolean('show_address')->default(false);
            $table->boolean('show_back_side')->default(true);
            $table->text('back_instructions')->nullable();
            $table->text('custom_css')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('id_card_templates');
    }
};
