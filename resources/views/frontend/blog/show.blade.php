@extends('layouts.frontend')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', ($post->meta_title ?? $post->title) . ' - Blog - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <nav style="margin-bottom: 20px;">
            <a href="{{ route('blog.index') }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">Blog</a>
            <span style="color: rgba(255,255,255,0.8); margin: 0 10px;">/</span>
            <span style="color: white;">{{ $post->title }}</span>
        </nav>
        <h1>{{ $post->title }}</h1>
    </div>
</section>

<section class="page-content" style="padding: 60px 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 300px; gap: 40px;">
            <!-- Main Content -->
            <article class="blog-post-content" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <!-- Post Meta -->
                <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f0;">
                    @if($post->category)
                    <a href="{{ route('blog.category', $post->category->slug) }}" style="display: inline-block; padding: 6px 15px; background: #667eea; color: white; border-radius: 20px; text-decoration: none; font-size: 0.9rem; font-weight: 600;">{{ $post->category->name }}</a>
                    @endif
                    <span style="color: #999;"><i class="fas fa-calendar"></i> {{ $post->published_at->format('F d, Y') }}</span>
                    <span style="color: #999;"><i class="fas fa-user"></i> {{ $post->author->name ?? 'Admin' }}</span>
                    <span style="color: #999;"><i class="fas fa-eye"></i> {{ number_format($post->views) }} views</span>
                </div>

                <!-- Featured Image -->
                @if($post->featured_image)
                <div style="margin-bottom: 30px;">
                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" style="width: 100%; border-radius: 12px;">
                </div>
                @endif

                <!-- Post Content -->
                <div class="blog-content" style="line-height: 1.8; color: #333; font-size: 1.05rem;">
                    {!! $post->content !!}
                </div>

                <!-- Tags -->
                @if($post->tags->count() > 0)
                <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #f0f0f0;">
                    <h4 style="margin-bottom: 15px; color: #666;">Tags:</h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @foreach($post->tags as $tag)
                        <a href="{{ route('blog.tag', $tag->slug) }}" style="display: inline-block; padding: 6px 15px; background: #f9f9f9; color: #667eea; border-radius: 20px; text-decoration: none; font-size: 0.9rem; transition: background 0.3s ease;">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Share Buttons -->
                <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #f0f0f0;">
                    <h4 style="margin-bottom: 15px; color: #666;">Share this post:</h4>
                    <div style="display: flex; gap: 10px;">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" style="display: inline-block; padding: 10px 20px; background: #1877f2; color: white; border-radius: 8px; text-decoration: none;">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" style="display: inline-block; padding: 10px 20px; background: #1da1f2; color: white; border-radius: 8px; text-decoration: none;">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" style="display: inline-block; padding: 10px 20px; background: #0077b5; color: white; border-radius: 8px; text-decoration: none;">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </a>
                    </div>
                </div>
            </article>

            <!-- Sidebar -->
            <aside class="blog-sidebar">
                <!-- Categories -->
                @if($categories->count() > 0)
                <div class="sidebar-widget" style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 1.2rem;">Categories</h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach($categories as $category)
                        <li style="margin-bottom: 10px;">
                            <a href="{{ route('blog.category', $category->slug) }}" style="display: flex; justify-content: space-between; text-decoration: none; color: #666; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
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

                <!-- Related Posts -->
                @if($relatedPosts->count() > 0)
                <div class="sidebar-widget" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 1.2rem;">Related Posts</h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach($relatedPosts as $relatedPost)
                        <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0;">
                            <a href="{{ route('blog.show', $relatedPost->slug) }}" style="text-decoration: none; color: #333; font-weight: 500; display: block; margin-bottom: 5px;">{{ Str::limit($relatedPost->title, 60) }}</a>
                            <span style="color: #999; font-size: 0.85rem;"><i class="fas fa-calendar"></i> {{ $relatedPost->published_at->format('M d, Y') }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </aside>
        </div>
    </div>
</section>

<style>
.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
}

.blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4 {
    margin-top: 30px;
    margin-bottom: 15px;
    color: #333;
}

.blog-content p {
    margin-bottom: 20px;
}

.blog-content ul, .blog-content ol {
    margin-bottom: 20px;
    padding-left: 30px;
}

.blog-content a {
    color: #667eea;
    text-decoration: underline;
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

