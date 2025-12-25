<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the blog posts.
     */
    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'author', 'tags']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        $posts = $query->latest('created_at')->paginate(20);
        $categories = BlogCategory::active()->ordered()->get();

        return view('admin.blog.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        $categories = BlogCategory::active()->ordered()->get();
        $tags = BlogTag::orderBy('name')->get();
        return view('admin.blog.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created blog post in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
        ]);

        $data = $request->only([
            'title', 'slug', 'excerpt', 'content', 'category_id', 'status',
            'is_featured', 'meta_title', 'meta_description', 'meta_keywords', 'published_at'
        ]);

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
            // Ensure uniqueness
            $originalSlug = $data['slug'];
            $counter = 1;
            while (BlogPost::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $data['author_id'] = Auth::id();
        $data['is_featured'] = $request->has('is_featured');
        
        // Set published_at if status is published and not set
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog/posts', 'public');
        }

        $post = BlogPost::create($data);

        // Attach tags
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post created successfully!');
    }

    /**
     * Display the specified blog post.
     */
    public function show($id)
    {
        $post = BlogPost::with(['category', 'author', 'tags'])->findOrFail($id);
        return view('admin.blog.show', compact('post'));
    }

    /**
     * Show the form for editing the specified blog post.
     */
    public function edit($id)
    {
        $post = BlogPost::with('tags')->findOrFail($id);
        $categories = BlogCategory::active()->ordered()->get();
        $tags = BlogTag::orderBy('name')->get();
        return view('admin.blog.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified blog post in storage.
     */
    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
            'remove_featured_image' => 'boolean',
        ]);

        $data = $request->only([
            'title', 'slug', 'excerpt', 'content', 'category_id', 'status',
            'is_featured', 'meta_title', 'meta_description', 'meta_keywords', 'published_at'
        ]);

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
            // Ensure uniqueness
            $originalSlug = $data['slug'];
            $counter = 1;
            while (BlogPost::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $data['is_featured'] = $request->has('is_featured');
        
        // Set published_at if status is published and not set
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Handle featured image upload/removal
        if ($request->has('remove_featured_image') && $request->remove_featured_image) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = null;
        } elseif ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog/posts', 'public');
        }

        $post->update($data);

        // Sync tags
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post updated successfully!');
    }

    /**
     * Remove the specified blog post from storage.
     */
    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);

        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post deleted successfully!');
    }

    /**
     * Manage categories.
     */
    public function categories()
    {
        $categories = BlogCategory::ordered()->get();
        return view('admin.blog.categories', compact('categories'));
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.blog.categories')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.blog.categories')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a category.
     */
    public function deleteCategory($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.blog.categories')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Manage tags.
     */
    public function tags()
    {
        $tags = BlogTag::orderBy('name')->get();
        return view('admin.blog.tags', compact('tags'));
    }

    /**
     * Store a new tag.
     */
    public function storeTag(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_tags,name',
        ]);

        BlogTag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.blog.tags')
            ->with('success', 'Tag created successfully!');
    }

    /**
     * Delete a tag.
     */
    public function deleteTag($id)
    {
        $tag = BlogTag::findOrFail($id);
        $tag->delete();

        return redirect()->route('admin.blog.tags')
            ->with('success', 'Tag deleted successfully!');
    }
}
