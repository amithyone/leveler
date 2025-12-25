<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    Schema::dropIfExists('blog_post_tag');
    Schema::dropIfExists('blog_posts');
    Schema::dropIfExists('blog_tags');
    Schema::dropIfExists('blog_categories');
    
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    echo "Blog tables dropped successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

