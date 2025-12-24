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
        Schema::table('pages', function (Blueprint $table) {
            $table->json('hero_slides')->nullable()->after('slider_images');
        });

        // Populate home page with 3 default slides
        $homePage = DB::table('pages')->where('slug', 'home')->first();
        if ($homePage) {
            $defaultSlides = [
                [
                    'image' => '',
                    'title' => 'Welcome to<br>Leveler<br>A Human Capacity Development Company',
                    'subtitle' => '',
                    'primary_button_text' => 'Get a quote',
                    'primary_button_link' => '/contact',
                    'secondary_button_text' => 'Contact us',
                    'secondary_button_link' => '/contact',
                ],
                [
                    'image' => '',
                    'title' => 'Empowering Businesses<br>Through Strategic Growth',
                    'subtitle' => 'We deliver value-driven solutions that support your business growth aspirations',
                    'primary_button_text' => 'Our Services',
                    'primary_button_link' => '/services',
                    'secondary_button_text' => 'Learn More',
                    'secondary_button_link' => '/about',
                ],
                [
                    'image' => '',
                    'title' => 'Transform Your Workforce<br>With Expert Training',
                    'subtitle' => 'Professional development programs designed to accelerate your team\'s success',
                    'primary_button_text' => 'View Courses',
                    'primary_button_link' => '/courses',
                    'secondary_button_text' => 'Contact Us',
                    'secondary_button_link' => '/contact',
                ],
            ];

            DB::table('pages')
                ->where('slug', 'home')
                ->update(['hero_slides' => json_encode($defaultSlides)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('hero_slides');
        });
    }
};
