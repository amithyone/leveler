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
        // Add course_status to trainee_course_access
        Schema::table('trainee_course_access', function (Blueprint $table) {
            $table->enum('course_status', ['active_training_pending', 'active_training_completed', 'inactive'])
                  ->default('inactive')
                  ->after('granted_at');
            $table->text('whatsapp_link')->nullable()->after('course_status');
            $table->timestamp('activated_at')->nullable()->after('whatsapp_link');
        });

        // Add user_type and additional fields to trainees
        Schema::table('trainees', function (Blueprint $table) {
            $table->enum('user_type', ['nysc', 'working_class'])->nullable()->after('user_id');
            $table->string('state_code', 10)->nullable()->after('user_type');
            $table->string('whatsapp_number', 20)->nullable()->after('phone_number');
            $table->timestamp('nysc_start_date')->nullable()->after('whatsapp_number'); // For 365-day countdown
        });

        // Add user_type to users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['nysc', 'working_class'])->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainee_course_access', function (Blueprint $table) {
            $table->dropColumn(['course_status', 'whatsapp_link', 'activated_at']);
        });

        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'state_code', 'whatsapp_number', 'nysc_start_date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
};

