<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$page = \App\Models\Page::where('slug', 'home')->first();

if ($page) {
    echo "Page found: " . $page->title . "\n";
    echo "Slider images (raw): " . var_export($page->getRawOriginal('slider_images'), true) . "\n";
    echo "Slider images (cast): " . var_export($page->slider_images, true) . "\n";
    
    if ($page->slider_images && is_array($page->slider_images)) {
        echo "Number of slider images: " . count($page->slider_images) . "\n";
        foreach ($page->slider_images as $index => $imagePath) {
            echo "Image $index: $imagePath\n";
            $fullPath = storage_path('app/public/' . $imagePath);
            echo "  Full path: $fullPath\n";
            echo "  Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
            $publicPath = public_path('storage/' . $imagePath);
            echo "  Public path: $publicPath\n";
            echo "  Public exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "No slider images found or not an array\n";
    }
} else {
    echo "Home page not found\n";
}

