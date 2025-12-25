<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request)
    {
        $query = BlogPost::published()->with(['category', 'author', 'tags']);

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $tag = BlogTag::where('slug', $request->tag)->first();
            if ($tag) {
                $query->whereHas('tags', function($q) use ($tag) {
                    $q->where('blog_tags.id', $tag->id);
                });
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->latest('published_at')->paginate(12);
        $categories = BlogCategory::active()->ordered()->withCount('posts')->get();
        $recentPosts = BlogPost::published()->latest('published_at')->take(5)->get();
        $tags = BlogTag::withCount('posts')->orderBy('name')->get();

        return view('frontend.blog.index', compact('posts', 'categories', 'recentPosts', 'tags'));
    }

    /**
     * Display the specified blog post.
     */
    public function show($slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with(['category', 'author', 'tags'])
            ->firstOrFail();

        // Increment views
        $post->incrementViews();

        // Get related posts (same category, excluding current post)
        $relatedPosts = BlogPost::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        // Get recent posts
        $recentPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        // Get categories
        $categories = BlogCategory::active()->ordered()->withCount('posts')->get();

        return view('frontend.blog.show', compact('post', 'relatedPosts', 'recentPosts', 'categories'));
    }

    /**
     * Display posts by category.
     */
    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        $posts = BlogPost::published()
            ->where('category_id', $category->id)
            ->with(['author', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::active()->ordered()->withCount('posts')->get();
        $recentPosts = BlogPost::published()->latest('published_at')->take(5)->get();
        $tags = BlogTag::withCount('posts')->orderBy('name')->get();

        return view('frontend.blog.category', compact('category', 'posts', 'categories', 'recentPosts', 'tags'));
    }

    /**
     * Display posts by tag.
     */
    public function tag($slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();
        
        $posts = BlogPost::published()
            ->whereHas('tags', function($q) use ($tag) {
                $q->where('blog_tags.id', $tag->id);
            })
            ->with(['category', 'author', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::active()->ordered()->withCount('posts')->get();
        $recentPosts = BlogPost::published()->latest('published_at')->take(5)->get();
        $tags = BlogTag::withCount('posts')->orderBy('name')->get();

        return view('frontend.blog.tag', compact('tag', 'posts', 'categories', 'recentPosts', 'tags'));
    }
}
