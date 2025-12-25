@extends('layouts.frontend')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Blog - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Blog</h1>
        <p style="margin-top: 10px; color: #ffffff;">Latest news, insights, and updates</p>
    </div>
</section>

<section class="page-content" style="padding: 60px 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 40px;">
            <!-- Main Content -->
            <div>
                @if($posts->count() > 0)
                <div class="blog-posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; margin-bottom: 40px;">
                    @foreach($posts as $post)
                    <article class="blog-post-card" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                        <a href="{{ route('blog.show', $post->slug) }}" style="text-decoration: none; color: inherit; display: block;">
                            @if($post->featured_image)
                            <div class="blog-post-image" style="width: 100%; height: 200px; overflow: hidden;">
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                            </div>
                            @endif
                            <div style="padding: 20px;">
                                @if($post->category)
                                <span class="blog-category" style="display: inline-block; padding: 4px 12px; background: #667eea; color: white; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 10px;">{{ $post->category->name }}</span>
                                @endif
                                <h3 style="margin: 10px 0; font-size: 1.3rem; color: #333; line-height: 1.4;">{{ $post->title }}</h3>
                                @if($post->excerpt)
                                <p style="color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 15px;">{{ Str::limit($post->excerpt, 120) }}</p>
                                @endif
                                <div style="display: flex; justify-content: space-between; align-items: center; color: #999; font-size: 0.85rem;">
                                    <span><i class="fas fa-calendar"></i> {{ $post->published_at->format('M d, Y') }}</span>
                                    <span><i class="fas fa-eye"></i> {{ number_format($post->views) }}</span>
                                </div>
                            </div>
                        </a>
                    </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div style="margin-top: 40px;">
                    {{ $posts->links() }}
                </div>
                @else
                <div style="padding: 60px; text-align: center; background: white; border-radius: 12px;">
                    <i class="fas fa-blog" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
                    <h3 style="color: #666; margin-bottom: 10px;">No blog posts found</h3>
                    <p style="color: #999;">Check back soon for new content!</p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="blog-sidebar">
                <!-- Search -->
                <div class="sidebar-widget" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 1.2rem;">Search</h3>
                    <form method="GET" action="{{ route('blog.index') }}">
                        <div style="display: flex; gap: 10px;">
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search posts..." style="flex: 1;">
                            <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Categories -->
                @if($categories->count() > 0)
                <div class="sidebar-widget" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 1.2rem;">Categories</h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach($categories as $category)
                        <li style="margin-bottom: 10px;">
                            <a href="{{ route('blog.index', ['category' => $category->id]) }}" style="display: flex; justify-content: space-between; text-decoration: none; color: #666; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                                <span>{{ $category->name }}</span>
                                <span style="color: #999;">({{ $category->posts_count }})</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Recent Posts -->
                @if($recentPosts->count() > 0)
                <div class="sidebar-widget" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 1.2rem;">Recent Posts</h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach($recentPosts as $recentPost)
                        <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                            <a href="{{ route('blog.show', $recentPost->slug) }}" style="text-decoration: none; color: #333; font-weight: 500; display: block; margin-bottom: 5px;">{{ Str::limit($recentPost->title, 60) }}</a>
                            <span style="color: #999; font-size: 0.85rem;"><i class="fas fa-calendar"></i> {{ $recentPost->published_at->format('M d, Y') }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Tags -->
                @if($tags->count() > 0)
                <div class="sidebar-widget" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 1.2rem;">Tags</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($tags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}" style="display: inline-block; padding: 6px 12px; background: #f9f9f9; color: #666; border-radius: 20px; text-decoration: none; font-size: 0.85rem; transition: background 0.3s ease;">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>
</section>

<style>
.blog-post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.blog-post-card:hover .blog-post-image img {
    transform: scale(1.05);
}

@media (max-width: 992px) {
    .page-content > .container > div {
        grid-template-columns: 1fr !important;
    }
    
    .blog-sidebar {
        order: -1;
    }
}
</style>
@endsection

