<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'featured_image',
        'slider_images',
        'hero_slides',
        'meta_description',
        'meta_keywords',
        'is_active',
        'order',
        'page_type',
        'sections',
        'contact_details',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sections' => 'array',
        'slider_images' => 'array',
        'hero_slides' => 'array',
        'contact_details' => 'array',
    ];

    /**
     * Get page by slug
     */
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }
}
