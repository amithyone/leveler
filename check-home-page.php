<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Page;

$page = Page::where('slug', 'home')->first();

if ($page) {
    echo "Home page found!\n";
    echo "ID: {$page->id}\n";
    echo "Title: {$page->title}\n";
    echo "Slug: {$page->slug}\n";
    echo "Is Active: " . ($page->is_active ? 'Yes' : 'No') . "\n";
    echo "Slider Images: " . (count($page->slider_images ?? []) > 0 ? count($page->slider_images) . ' images' : 'None') . "\n";
    if ($page->slider_images && count($page->slider_images) > 0) {
        echo "Slider Image Paths:\n";
        foreach ($page->slider_images as $index => $img) {
            echo "  [$index] {$img}\n";
        }
    }
} else {
    echo "Home page NOT found in database!\n";
    echo "Creating default home page...\n";
    Page::create([
        'slug' => 'home',
        'title' => 'Home',
        'content' => '',
        'page_type' => 'page',
        'is_active' => true,
        'order' => 0,
    ]);
    echo "Default home page created!\n";
}



