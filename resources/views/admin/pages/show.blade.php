@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div class="page-title-actions">
        <h1 class="page-title"><i class="fas fa-eye"></i> View Page: {{ $page->title }}</h1>
        <div>
            <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-primary">
                <i class="fas fa-pencil-alt"></i> Edit
            </a>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="content-section">
        <div class="page-details">
            <div class="detail-row">
                <strong>Title:</strong>
                <span>{{ $page->title }}</span>
            </div>
            <div class="detail-row">
                <strong>Slug:</strong>
                <span><code>{{ $page->slug }}</code></span>
            </div>
            <div class="detail-row">
                <strong>Type:</strong>
                <span><span class="badge badge-info">{{ ucfirst($page->page_type) }}</span></span>
            </div>
            <div class="detail-row">
                <strong>Status:</strong>
                <span>
                    <span class="status-badge {{ $page->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <strong>Order:</strong>
                <span>{{ $page->order }}</span>
            </div>
            @if($page->meta_description)
            <div class="detail-row">
                <strong>Meta Description:</strong>
                <span>{{ $page->meta_description }}</span>
            </div>
            @endif
            @if($page->meta_keywords)
            <div class="detail-row">
                <strong>Meta Keywords:</strong>
                <span>{{ $page->meta_keywords }}</span>
            </div>
            @endif
            <div class="detail-row">
                <strong>Created:</strong>
                <span>{{ $page->created_at->format('Y-m-d H:i') }}</span>
            </div>
            <div class="detail-row">
                <strong>Updated:</strong>
                <span>{{ $page->updated_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        @if($page->featured_image)
        <div class="image-preview-section">
            <h3>Featured Image</h3>
            <div class="preview-box">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($page->featured_image) }}" alt="Featured Image" style="max-width: 100%; height: auto; border-radius: 8px;">
            </div>
        </div>
        @endif

        @if($page->slider_images && count($page->slider_images) > 0)
        <div class="slider-preview-section">
            <h3>Slider Images ({{ count($page->slider_images) }})</h3>
            <div class="preview-box">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                    @foreach($page->slider_images as $sliderImage)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($sliderImage) }}" alt="Slider Image" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0;">
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($page->content)
        <div class="content-preview">
            <h3>Content Preview</h3>
            <div class="preview-box">
                {!! nl2br(e($page->content)) !!}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.page-details {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 30px;
}

.detail-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row strong {
    min-width: 150px;
    color: #333;
}

.detail-row span {
    color: #666;
}

.content-preview {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
}

.content-preview h3 {
    color: #333;
    margin-bottom: 20px;
}

.preview-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 6px;
    line-height: 1.8;
    color: #333;
}
</style>
@endsection

