<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$page = \App\Models\Page::where('slug', 'home')->first();

if ($page) {
    echo "Page found: " . $page->title . "\n";
    echo "Hero slides (raw): " . var_export($page->getRawOriginal('hero_slides'), true) . "\n";
    echo "Hero slides (cast): " . var_export($page->hero_slides, true) . "\n";
    
    if ($page->hero_slides && is_array($page->hero_slides)) {
        echo "Number of hero slides: " . count($page->hero_slides) . "\n";
        foreach ($page->hero_slides as $index => $slide) {
            echo "Slide $index:\n";
            echo "  Image: " . ($slide['image'] ?? 'N/A') . "\n";
            if (!empty($slide['image'])) {
                $fullPath = storage_path('app/public/' . $slide['image']);
                echo "  Full path: $fullPath\n";
                echo "  Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
                $publicPath = public_path('storage/' . $slide['image']);
                echo "  Public path: $publicPath\n";
                echo "  Public exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
            }
            echo "  Title: " . ($slide['title'] ?? 'N/A') . "\n";
        }
    } else {
        echo "No hero slides found or not an array\n";
    }
} else {
    echo "Home page not found\n";
}

