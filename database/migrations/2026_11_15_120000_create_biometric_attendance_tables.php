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
        // 1. Biometric Hardware & Live Kiosk Devices per School
        Schema::create('biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Main Gate Face Kiosk", "Staff Room Finger Scanner"
            $table->string('device_type')->default('webcam_face'); // webcam_face, fingerprint, combo, zkteco_push, essl
            $table->string('model')->nullable(); // e.g. "Live AI Face Kiosk", "ZKTeco MB20", "eSSL SilkBio", "Mantra MFS100"
            $table->string('serial_number')->nullable()->index(); // Machine Serial for Push Webhook API
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable()->default(4370);
            $table->string('location')->nullable(); // e.g. "Gate A", "Admin Block", "Library"
            $table->string('status')->default('active'); // active, inactive
            $table->string('direction')->default('both'); // in, out, both
            $table->time('late_cutoff_time')->nullable()->default('08:30:00'); // Scans after this time marked Late
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->string('api_token', 64)->nullable()->unique();
            $table->timestamps();

            $table->index(['school_id', 'status']);
        });

        // 2. Biometric Enrollments (Face Reference & Fingerprint Templates)
        Schema::create('biometric_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->enum('person_type', ['student', 'teacher'])->default('student');
            $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->cascadeOnDelete();
            $table->string('biometric_pin', 50)->nullable()->index(); // Machine User ID / Enroll PIN
            $table->string('rfid_card_no', 50)->nullable()->index(); // Smart RFID Card UID
            $table->string('face_photo_path')->nullable(); // Reference Photo URL
            $table->longText('face_descriptor')->nullable(); // JSON Array descriptor vector
            $table->longText('fingerprint_template')->nullable(); // ISO/ANSI base64 template
            $table->boolean('is_face_enrolled')->default(false);
            $table->boolean('is_fingerprint_enrolled')->default(false);
            $table->timestamp('enrolled_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'student_id']);
            $table->unique(['school_id', 'teacher_id']);
            $table->index(['school_id', 'person_type']);
        });

        // 3. Biometric Live Punch Logs
        Schema::create('biometric_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('biometric_device_id')->nullable()->constrained('biometric_devices')->nullOnDelete();
            $table->enum('person_type', ['student', 'teacher'])->default('student');
            $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->cascadeOnDelete();
            $table->date('punch_date');
            $table->time('punch_time');
            $table->string('punch_type')->default('check_in'); // check_in, check_out
            $table->string('verification_method')->default('face'); // face, fingerprint, rfid_card, device_push
            $table->decimal('confidence_score', 5, 2)->nullable(); // e.g. 98.50
            $table->string('device_serial')->nullable();
            $table->string('snapshot_path')->nullable();
            $table->string('status')->default('success'); // success, duplicate_cooldown, rejected
            $table->string('attendance_status')->default('Present'); // Present, Late
            $table->string('sync_status')->default('synced'); // synced, pending
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'punch_date']);
            $table->index(['school_id', 'person_type', 'punch_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_attendance_logs');
        Schema::dropIfExists('biometric_enrollments');
        Schema::dropIfExists('biometric_devices');
    }
};
