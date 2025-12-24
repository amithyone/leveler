<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Page;

$page = Page::where('slug', 'home')->first();

if ($page) {
    $sliderImages = $page->slider_images ?? [];
    if (is_string($sliderImages)) {
        $sliderImages = json_decode($sliderImages, true) ?? [];
    }
    
    echo "=== SLIDER IMAGES TEST ===\n";
    echo "Count: " . count($sliderImages) . "\n\n";
    
    foreach ($sliderImages as $index => $img) {
        $url = asset('storage/' . $img);
        $fullUrl = url('storage/' . $img);
        echo "Image $index:\n";
        echo "  Path: $img\n";
        echo "  asset(): $url\n";
        echo "  url(): $fullUrl\n";
        echo "  Full path: " . storage_path('app/public/' . $img) . "\n";
        echo "  Exists: " . (file_exists(storage_path('app/public/' . $img)) ? 'YES' : 'NO') . "\n\n";
    }
}



