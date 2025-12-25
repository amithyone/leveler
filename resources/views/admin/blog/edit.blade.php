@extends('layouts.admin')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-pencil-alt"></i> Edit Blog Post: {{ $post->title }}</h1>
</div>

<div class="page-content">
    <div class="content-section">
        @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.blog.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="default-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
            </div>

            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $post->slug) }}">
                <small class="form-text">Leave empty to auto-generate from title</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="published_at">Publish Date</label>
                    <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            <div class="form-group">
                <label for="content">Content <span class="required">*</span></label>
                <textarea id="content" name="content" class="form-control" rows="15" required>{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                @if($post->featured_image)
                <div class="current-image" style="margin-bottom: 15px;">
                    <img src="{{ Storage::url($post->featured_image) }}" alt="Current Featured Image" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0; display: block; margin-bottom: 10px;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remove_featured_image" value="1">
                        <span>Remove current image</span>
                    </label>
                </div>
                @endif
                <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*" onchange="previewImage(this, 'featured_preview')">
                <div id="featured_preview" class="image-preview" style="margin-top: 10px; display: none;">
                    <img src="" alt="Featured Image Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
                </div>
            </div>

            <div class="form-group">
                <label>Tags</label>
                <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                    @foreach($tags as $tag)
                    <label class="checkbox-label" style="display: flex; align-items: center; padding: 8px 15px; background: #f9f9f9; border-radius: 20px; cursor: pointer;">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <span style="margin-left: 8px;">{{ $tag->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                    <span>Feature this post</span>
                </label>
            </div>

            <div style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #e0e0e0;">
                <h3 style="margin-bottom: 20px; color: #667eea;">SEO Settings</h3>
                
                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title) }}">
                </div>

                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3">{{ old('meta_description', $post->meta_description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="meta_keywords">Meta Keywords</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $post->meta_keywords) }}">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Post
                </button>
                <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: true,
        plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media | code fullscreen | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        branding: false,
        promotion: false,
    });

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.style.display = 'block';
                preview.querySelector('img').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endsection

