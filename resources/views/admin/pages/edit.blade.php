@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-pencil-alt"></i> Edit Page: {{ $page->title }}</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="slug">Slug <span class="required">*</span></label>
                <input type="text" id="slug" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $page->slug) }}" required>
                <small class="form-text">URL-friendly identifier (e.g., "about-us", "faqs")</small>
                @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="title">Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="15">{{ old('content', $page->content) }}</textarea>
                <small class="form-text">Page content (supports line breaks)</small>
                @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                @if($page->featured_image)
                <div class="current-image" style="margin-bottom: 15px;">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($page->featured_image) }}" alt="Current Featured Image" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0; display: block; margin-bottom: 10px;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remove_featured_image" value="1">
                        <span>Remove current featured image</span>
                    </label>
                </div>
                @endif
                <input type="file" id="featured_image" name="featured_image" class="form-control @error('featured_image') is-invalid @enderror" accept="image/*" onchange="previewImage(this, 'featured_preview')">
                <small class="form-text">Upload new featured image to replace current one (max 5MB, formats: jpeg, png, jpg, gif, webp)</small>
                <div id="featured_preview" class="image-preview" style="margin-top: 10px; display: none;">
                    <img src="" alt="Featured Image Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
                </div>
                @error('featured_image')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="slider_images">Slider Images</label>
                @if($page->slider_images && count($page->slider_images) > 0)
                <div class="current-slider-images" style="margin-bottom: 15px;">
                    <p style="font-weight: 600; margin-bottom: 10px;">Current Slider Images:</p>
                    <div class="slider-images-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-bottom: 15px;">
                        @foreach($page->slider_images as $index => $sliderImage)
                        <div class="slider-image-item" style="position: relative;">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($sliderImage) }}" alt="Slider Image {{ $index + 1 }}" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0;">
                            <label class="checkbox-label" style="margin-top: 8px; display: block;">
                                <input type="checkbox" name="remove_slider_images[]" value="{{ $sliderImage }}">
                                <span style="font-size: 12px;">Remove</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                <input type="file" id="slider_images" name="slider_images[]" class="form-control @error('slider_images.*') is-invalid @enderror" accept="image/*" multiple onchange="previewSliderImages(this)">
                <small class="form-text">Add more slider images (max 5MB each, formats: jpeg, png, jpg, gif, webp)</small>
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
                        <option value="page" {{ old('page_type', $page->page_type) == 'page' ? 'selected' : '' }}>Page</option>
                        <option value="section" {{ old('page_type', $page->page_type) == 'section' ? 'selected' : '' }}>Section</option>
                        <option value="footer_link" {{ old('page_type', $page->page_type) == 'footer_link' ? 'selected' : '' }}>Footer Link</option>
                    </select>
                    @error('page_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="order">Order</label>
                    <input type="number" id="order" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $page->order) }}">
                    <small class="form-text">Display order (lower numbers appear first)</small>
                    @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="3">{{ old('meta_description', $page->meta_description) }}</textarea>
                <small class="form-text">SEO meta description (max 500 characters)</small>
                @error('meta_description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="meta_keywords">Meta Keywords</label>
                <input type="text" id="meta_keywords" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" value="{{ old('meta_keywords', $page->meta_keywords) }}">
                <small class="form-text">Comma-separated keywords for SEO</small>
                @error('meta_keywords')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                    <span>Active (visible on website)</span>
                </label>
            </div>

            @if($page->slug === 'contact' || old('slug') === 'contact')
            <div class="contact-details-section" style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #e0e0e0;">
                <h3 style="margin-bottom: 20px; color: #667eea;">
                    <i class="fas fa-address-book"></i> Contact Details
                </h3>
                
                @php
                    $contactDetails = old('contact_details', $page->contact_details ?? []);
                @endphp

                <div class="form-group">
                    <label for="contact_address">Address Line 1</label>
                    <input type="text" id="contact_address" name="contact_address" class="form-control" value="{{ old('contact_address', $contactDetails['address'] ?? 'Nigeria') }}" placeholder="e.g., Nigeria">
                </div>

                <div class="form-group">
                    <label for="contact_address_line2">Address Line 2</label>
                    <input type="text" id="contact_address_line2" name="contact_address_line2" class="form-control" value="{{ old('contact_address_line2', $contactDetails['address_line2'] ?? 'Plot 559c, Capital Str., A11, Garki, Abuja') }}" placeholder="e.g., Plot 559c, Capital Str., A11, Garki, Abuja">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_phone">Phone Number</label>
                        <input type="text" id="contact_phone" name="contact_phone" class="form-control" value="{{ old('contact_phone', $contactDetails['phone'] ?? '(+234) 806-141-3675') }}" placeholder="e.g., (+234) 806-141-3675">
                    </div>

                    <div class="form-group">
                        <label for="contact_email">Email Address</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-control" value="{{ old('contact_email', $contactDetails['email'] ?? '') }}" placeholder="e.g., info@levelercc.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_working_hours">Working Hours</label>
                    <input type="text" id="contact_working_hours" name="contact_working_hours" class="form-control" value="{{ old('contact_working_hours', $contactDetails['working_hours'] ?? 'Mon - Fri: 9.00 to 17.00') }}" placeholder="e.g., Mon - Fri: 9.00 to 17.00">
                </div>
            </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Page
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

