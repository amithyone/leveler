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
        // Update existing courses without assessment_questions_count to have default of 50
        DB::table('courses')
            ->whereNull('assessment_questions_count')
            ->update(['assessment_questions_count' => 50]);
        
        // Set default value for future courses
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('assessment_questions_count')->default(50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('assessment_questions_count')->nullable()->change();
        });
    }
};
