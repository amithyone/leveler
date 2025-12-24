<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Page;
use Illuminate\Support\Facades\Storage;

$page = Page::where('slug', 'home')->first();

if ($page) {
    echo "=== HOME PAGE DEBUG ===\n";
    echo "ID: {$page->id}\n";
    echo "Title: {$page->title}\n";
    echo "Slug: {$page->slug}\n";
    echo "Is Active: " . ($page->is_active ? 'Yes' : 'No') . "\n";
    echo "\n=== SLIDER IMAGES ===\n";
    
    $sliderImages = $page->slider_images ?? [];
    if (is_string($sliderImages)) {
        $sliderImages = json_decode($sliderImages, true) ?? [];
    }
    
    echo "Count: " . count($sliderImages) . "\n";
    echo "Type: " . gettype($page->slider_images) . "\n";
    
    if (count($sliderImages) > 0) {
        echo "\nImage Details:\n";
        foreach ($sliderImages as $index => $img) {
            echo "  [$index] Path: {$img}\n";
            $fullPath = storage_path('app/public/' . $img);
            $exists = file_exists($fullPath);
            echo "       Exists: " . ($exists ? 'YES' : 'NO') . "\n";
            echo "       Full Path: {$fullPath}\n";
            
            $url = Storage::disk('public')->url($img);
            echo "       URL: {$url}\n";
            echo "\n";
        }
    } else {
        echo "No slider images found!\n";
    }
} else {
    echo "Home page NOT found!\n";
}

