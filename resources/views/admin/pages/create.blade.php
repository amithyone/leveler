@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus"></i> Create New Page</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="slug">Slug <span class="required">*</span></label>
                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>
                <small class="form-text">URL-friendly identifier (e.g., "about-us", "faqs")</small>
                @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="title">Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="15">{{ old('content') }}</textarea>
                <small class="form-text">Page content (supports line breaks)</small>
                @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <input type="file" id="featured_image" name="featured_image" class="form-control @error('featured_image') is-invalid @enderror" accept="image/*" onchange="previewImage(this, 'featured_preview')">
                <small class="form-text">Main image for this page (max 5MB, formats: jpeg, png, jpg, gif, webp)</small>
                <div id="featured_preview" class="image-preview" style="margin-top: 10px; display: none;">
                    <img src="" alt="Featured Image Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
                </div>
                @error('featured_image')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="slider_images">Slider Images</label>
                <input type="file" id="slider_images" name="slider_images[]" class="form-control @error('slider_images.*') is-invalid @enderror" accept="image/*" multiple onchange="previewSliderImages(this)">
                <small class="form-text">Multiple images for slider/carousel (max 5MB each, formats: jpeg, png, jpg, gif, webp)</small>
                <div id="slider_preview" class="slider-preview" style="margin-top: 10px; display: none;">
                    <div class="preview-grid"></div>
                </div>
                @error('slider_images.*')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="page_type">Page Type <span class="required">*</span></label>
                    <select id="page_type" name="page_type" class="form-control @error('page_type') is-invalid @enderror" required>
                        <option value="page" {{ old('page_type') == 'page' ? 'selected' : '' }}>Page</option>
                        <option value="section" {{ old('page_type') == 'section' ? 'selected' : '' }}>Section</option>
                        <option value="footer_link" {{ old('page_type') == 'footer_link' ? 'selected' : '' }}>Footer Link</option>
                    </select>
                    @error('page_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="order">Order</label>
                    <input type="number" id="order" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}">
                    <small class="form-text">Display order (lower numbers appear first)</small>
                    @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="3">{{ old('meta_description') }}</textarea>
                <small class="form-text">SEO meta description (max 500 characters)</small>
                @error('meta_description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="meta_keywords">Meta Keywords</label>
                <input type="text" id="meta_keywords" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" value="{{ old('meta_keywords') }}">
                <small class="form-text">Comma-separated keywords for SEO</small>
                @error('meta_keywords')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}>
                    <span>Active (visible on website)</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Page
                </button>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
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

function previewSliderImages(input) {
    const preview = document.getElementById('slider_preview');
    const grid = preview.querySelector('.preview-grid');
    grid.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        preview.style.display = 'block';
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'position: relative; margin-bottom: 10px;';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0;">
                `;
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else {
        preview.style.display = 'none';
    }
}
</script>

<style>
.image-preview img, .current-image img {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.slider-preview .preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}

.slider-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}
</style>
@endsection

