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
        Schema::table('courses', function (Blueprint $table) {
            $table->text('overview')->nullable()->after('description');
            $table->text('objectives')->nullable()->after('overview'); // JSON array of learning objectives
            $table->text('what_you_will_learn')->nullable()->after('objectives'); // JSON array
            $table->text('requirements')->nullable()->after('what_you_will_learn'); // JSON array
            $table->text('who_is_this_for')->nullable()->after('requirements');
            $table->string('level')->nullable()->after('who_is_this_for'); // Beginner, Intermediate, Advanced
            $table->string('language')->default('English')->after('level');
            $table->string('instructor')->nullable()->after('language');
            $table->string('image')->nullable()->after('instructor');
            $table->text('curriculum')->nullable()->after('image'); // JSON array of modules/lessons
            $table->decimal('rating', 3, 2)->default(0)->after('curriculum');
            $table->integer('total_reviews')->default(0)->after('rating');
            $table->integer('total_enrollments')->default(0)->after('total_reviews');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'overview',
                'objectives',
                'what_you_will_learn',
                'requirements',
                'who_is_this_for',
                'level',
                'language',
                'instructor',
                'image',
                'curriculum',
                'rating',
                'total_reviews',
                'total_enrollments',
            ]);
        });
    }
};

