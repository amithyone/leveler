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
        // Add course_access_count to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('course_access_count')->default(0)->after('amount');
            $table->string('package_type')->nullable()->after('course_access_count'); // 'single' or 'package'
        });

        // Add available_courses to trainees table
        Schema::table('trainees', function (Blueprint $table) {
            $table->integer('available_courses')->default(0)->after('status');
        });

        // Create trainee_course_access pivot table
        Schema::create('trainee_course_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('trainees')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['trainee_id', 'course_id']);
            $table->index('trainee_id');
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainee_course_access');
        
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn('available_courses');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['course_access_count', 'package_type']);
        });
    }
};
