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

            @if($page->slug === 'home' || old('slug') === 'home')
            <div class="home-sections" style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #e0e0e0;">
                <h3 style="margin-bottom: 20px; color: #667eea;">
                    <i class="fas fa-home"></i> Home Page Sections
                </h3>
                
                @php
                    $sections = old('sections', $page->sections ?? []);
                    if (is_string($sections)) {
                        $sections = json_decode($sections, true) ?? [];
                    }
                    if (!is_array($sections)) {
                        $sections = [];
                    }
                @endphp

                <div class="form-group">
                    <label>Hero Slider Content (when images are uploaded)</label>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hero_title">Hero Title</label>
                            <input type="text" id="hero_title" name="sections[hero][title]" class="form-control" value="{{ $sections['hero']['title'] ?? 'Welcome to<br>Leveler<br>A Human Capacity Development Company' }}" placeholder="Hero title">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hero_primary_btn">Primary Button Text</label>
                            <input type="text" id="hero_primary_btn" name="sections[hero][primary_button]" class="form-control" value="{{ $sections['hero']['primary_button'] ?? 'Get a quote' }}" placeholder="Primary button text">
                        </div>
                        <div class="form-group">
                            <label for="hero_secondary_btn">Secondary Button Text</label>
                            <input type="text" id="hero_secondary_btn" name="sections[hero][secondary_button]" class="form-control" value="{{ $sections['hero']['secondary_button'] ?? 'Contact us' }}" placeholder="Secondary button text">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Vulnerability Section</label>
                    <div class="form-group">
                        <label for="vulnerability_title">Title</label>
                        <input type="text" id="vulnerability_title" name="sections[vulnerability][title]" class="form-control" value="{{ $sections['vulnerability']['title'] ?? 'Are you vulnerable to disruption?' }}" placeholder="Section title">
                    </div>
                    <div class="form-group">
                        <label for="vulnerability_text">Text</label>
                        <textarea id="vulnerability_text" name="sections[vulnerability][text]" class="form-control" rows="3" placeholder="Section text">{{ $sections['vulnerability']['text'] ?? 'Having the right product or service is fundamental, but it is not enough. What differentiates businesses is how they manage change in a continuously changing business climate.' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="vulnerability_button">Button Text</label>
                        <input type="text" id="vulnerability_button" name="sections[vulnerability][button]" class="form-control" value="{{ $sections['vulnerability']['button'] ?? 'Reach Out' }}" placeholder="Button text">
                    </div>
                </div>

                <div class="form-group">
                    <label>Features Section</label>
                    <div id="features-container">
                        @php
                            $features = $sections['features'] ?? [
                                ['icon' => 'fas fa-users', 'title' => 'People', 'text' => 'Committed to developing and sourcing the right team for outstanding results.'],
                                ['icon' => 'fas fa-chart-line', 'title' => 'Strategy', 'text' => 'Optimizing processes, interconnected network of professionals for result.'],
                                ['icon' => 'fas fa-laptop-code', 'title' => 'Technology', 'text' => 'We partner with clients to work directly with them over the long-term.'],
                            ];
                        @endphp
                        @foreach($features as $index => $feature)
                        <div class="feature-item" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Feature {{ $index + 1 }}</strong>
                                @if($index > 0)
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeFeature(this)">Remove</button>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Icon Class (Font Awesome)</label>
                                <input type="text" name="sections[features][{{ $index }}][icon]" class="form-control" value="{{ $feature['icon'] ?? '' }}" placeholder="e.g., fas fa-users">
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="sections[features][{{ $index }}][title]" class="form-control" value="{{ $feature['title'] ?? '' }}" placeholder="Feature title">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="sections[features][{{ $index }}][text]" class="form-control" rows="2" placeholder="Feature description">{{ $feature['text'] ?? '' }}</textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addFeature()">Add Feature</button>
                </div>

                <div class="form-group">
                    <label>About Section</label>
                    <div class="form-group">
                        <label for="about_title">Title</label>
                        <input type="text" id="about_title" name="sections[about][title]" class="form-control" value="{{ $sections['about']['title'] ?? 'About Us' }}" placeholder="Section title">
                    </div>
                    <div class="form-group">
                        <label for="about_subtitle">Subtitle</label>
                        <input type="text" id="about_subtitle" name="sections[about][subtitle]" class="form-control" value="{{ $sections['about']['subtitle'] ?? 'For over 10 years, we have supported businesses to accelerate growth.' }}" placeholder="Subtitle">
                    </div>
                    <div class="form-group">
                        <label for="about_text">Text</label>
                        <textarea id="about_text" name="sections[about][text]" class="form-control" rows="4" placeholder="About text">{{ $sections['about']['text'] ?? 'Leveler is a Business & Management consulting company whose mandate is to aid growth and sustainability of businesses through strategy development.

Our reputation is built on the foundation of providing business and management solutions that deliver growth, increase profit and boost efficiency.' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="about_button">Button Text</label>
                        <input type="text" id="about_button" name="sections[about][button]" class="form-control" value="{{ $sections['about']['button'] ?? 'Know More' }}" placeholder="Button text">
                    </div>
                </div>

                <div class="form-group">
                    <label>Why Leveler Section</label>
                    <div class="form-group">
                        <label for="why_title">Title</label>
                        <input type="text" id="why_title" name="sections[why][title]" class="form-control" value="{{ $sections['why']['title'] ?? 'Why Leveler' }}" placeholder="Section title">
                    </div>
                    <div id="why-items-container">
                        @php
                            $whyItems = $sections['why']['items'] ?? [
                                ['title' => 'Value', 'text' => 'We deliver value-driven and sustainable solutions that support the business growth aspirations of our clients.'],
                                ['title' => 'Unity of Purpose', 'text' => 'We work closely with our clients to uncover business gaps, and design solutions for profit optimization.'],
                            ];
                        @endphp
                        @foreach($whyItems as $index => $item)
                        <div class="why-item" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Item {{ $index + 1 }}</strong>
                                @if($index > 0)
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeWhyItem(this)">Remove</button>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="sections[why][items][{{ $index }}][title]" class="form-control" value="{{ $item['title'] ?? '' }}" placeholder="Item title">
                            </div>
                            <div class="form-group">
                                <label>Text</label>
                                <textarea name="sections[why][items][{{ $index }}][text]" class="form-control" rows="2" placeholder="Item description">{{ $item['text'] ?? '' }}</textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addWhyItem()">Add Why Item</button>
                </div>

                <div class="form-group">
                    <label>Stats Section</label>
                    <div class="form-group">
                        <label for="stats_title">Title</label>
                        <input type="text" id="stats_title" name="sections[stats][title]" class="form-control" value="{{ $sections['stats']['title'] ?? 'Enabling businesses to attain desired growth aspirations' }}" placeholder="Section title">
                    </div>
                    <div id="stats-container">
                        @php
                            $stats = $sections['stats']['items'] ?? [
                                ['number' => '10+', 'label' => 'Years of Existence'],
                                ['number' => '25+', 'label' => 'Consultants Nationwide'],
                                ['number' => '70+', 'label' => 'Satisfied Clients'],
                                ['number' => '10K+', 'label' => 'Trained and Certified'],
                            ];
                        @endphp
                        @foreach($stats as $index => $stat)
                        <div class="stat-item" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Stat {{ $index + 1 }}</strong>
                                @if($index > 0)
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeStat(this)">Remove</button>
                                @endif
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Number</label>
                                    <input type="text" name="sections[stats][items][{{ $index }}][number]" class="form-control" value="{{ $stat['number'] ?? '' }}" placeholder="e.g., 10+">
                                </div>
                                <div class="form-group">
                                    <label>Label</label>
                                    <input type="text" name="sections[stats][items][{{ $index }}][label]" class="form-control" value="{{ $stat['label'] ?? '' }}" placeholder="e.g., Years of Existence">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addStat()">Add Stat</button>
                </div>

                <div class="form-group">
                    <label>Services Section</label>
                    <div class="form-group">
                        <label for="services_title">Title</label>
                        <input type="text" id="services_title" name="sections[services][title]" class="form-control" value="{{ $sections['services']['title'] ?? 'Our Services' }}" placeholder="Section title">
                    </div>
                    <div id="services-container">
                        @php
                            $services = $sections['services']['items'] ?? [
                                ['icon' => 'fas fa-chart-bar', 'title' => 'Business Advisory', 'text' => 'We provide organizations with the insight, methodology, and framework necessary to successfully execute their business strategy.'],
                                ['icon' => 'fas fa-users', 'title' => 'Training & Development', 'text' => 'The ability to learn and translate that learning to action rapidly is the ultimate accomplishment.'],
                                ['icon' => 'fas fa-user-tie', 'title' => 'Recruitment & Selection', 'text' => 'We source for the most competent candidates using reliable techniques for talent acquisition.'],
                            ];
                        @endphp
                        @foreach($services as $index => $service)
                        <div class="service-item" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Service {{ $index + 1 }}</strong>
                                @if($index > 0)
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeService(this)">Remove</button>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Icon Class (Font Awesome)</label>
                                <input type="text" name="sections[services][items][{{ $index }}][icon]" class="form-control" value="{{ $service['icon'] ?? '' }}" placeholder="e.g., fas fa-chart-bar">
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="sections[services][items][{{ $index }}][title]" class="form-control" value="{{ $service['title'] ?? '' }}" placeholder="Service title">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="sections[services][items][{{ $index }}][text]" class="form-control" rows="2" placeholder="Service description">{{ $service['text'] ?? '' }}</textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addService()">Add Service</button>
                </div>

                <div class="form-group">
                    <label>Newsletter Section</label>
                    <div class="form-group">
                        <label for="newsletter_text">Text</label>
                        <input type="text" id="newsletter_text" name="sections[newsletter][text]" class="form-control" value="{{ $sections['newsletter']['text'] ?? 'Subscribing to our mailing list and receive weekly newsletter with latest news and offers.' }}" placeholder="Newsletter text">
                    </div>
                    <div class="form-group">
                        <label for="newsletter_button">Button Text</label>
                        <input type="text" id="newsletter_button" name="sections[newsletter][button]" class="form-control" value="{{ $sections['newsletter']['button'] ?? 'Subscribe' }}" placeholder="Button text">
                    </div>
                </div>
            </div>
            @endif

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
// Home page sections dynamic functions
let featureIndex = {{ count($sections['features'] ?? []) }};
let whyItemIndex = {{ count($sections['why']['items'] ?? []) }};
let statIndex = {{ count($sections['stats']['items'] ?? []) }};
let serviceIndex = {{ count($sections['services']['items'] ?? []) }};

function addFeature() {
    const container = document.getElementById('features-container');
    const div = document.createElement('div');
    div.className = 'feature-item';
    div.style.cssText = 'border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;';
    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Feature ${featureIndex + 1}</strong>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeFeature(this)">Remove</button>
        </div>
        <div class="form-group">
            <label>Icon Class (Font Awesome)</label>
            <input type="text" name="sections[features][${featureIndex}][icon]" class="form-control" placeholder="e.g., fas fa-users">
        </div>
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="sections[features][${featureIndex}][title]" class="form-control" placeholder="Feature title">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="sections[features][${featureIndex}][text]" class="form-control" rows="2" placeholder="Feature description"></textarea>
        </div>
    `;
    container.appendChild(div);
    featureIndex++;
}

function removeFeature(btn) {
    btn.closest('.feature-item').remove();
}

function addWhyItem() {
    const container = document.getElementById('why-items-container');
    const div = document.createElement('div');
    div.className = 'why-item';
    div.style.cssText = 'border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;';
    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Item ${whyItemIndex + 1}</strong>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeWhyItem(this)">Remove</button>
        </div>
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="sections[why][items][${whyItemIndex}][title]" class="form-control" placeholder="Item title">
        </div>
        <div class="form-group">
            <label>Text</label>
            <textarea name="sections[why][items][${whyItemIndex}][text]" class="form-control" rows="2" placeholder="Item description"></textarea>
        </div>
    `;
    container.appendChild(div);
    whyItemIndex++;
}

function removeWhyItem(btn) {
    btn.closest('.why-item').remove();
}

function addStat() {
    const container = document.getElementById('stats-container');
    const div = document.createElement('div');
    div.className = 'stat-item';
    div.style.cssText = 'border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;';
    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Stat ${statIndex + 1}</strong>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeStat(this)">Remove</button>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Number</label>
                <input type="text" name="sections[stats][items][${statIndex}][number]" class="form-control" placeholder="e.g., 10+">
            </div>
            <div class="form-group">
                <label>Label</label>
                <input type="text" name="sections[stats][items][${statIndex}][label]" class="form-control" placeholder="e.g., Years of Existence">
            </div>
        </div>
    `;
    container.appendChild(div);
    statIndex++;
}

function removeStat(btn) {
    btn.closest('.stat-item').remove();
}

function addService() {
    const container = document.getElementById('services-container');
    const div = document.createElement('div');
    div.className = 'service-item';
    div.style.cssText = 'border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;';
    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <strong>Service ${serviceIndex + 1}</strong>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeService(this)">Remove</button>
        </div>
        <div class="form-group">
            <label>Icon Class (Font Awesome)</label>
            <input type="text" name="sections[services][items][${serviceIndex}][icon]" class="form-control" placeholder="e.g., fas fa-chart-bar">
        </div>
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="sections[services][items][${serviceIndex}][title]" class="form-control" placeholder="Service title">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="sections[services][items][${serviceIndex}][text]" class="form-control" rows="2" placeholder="Service description"></textarea>
        </div>
    `;
    container.appendChild(div);
    serviceIndex++;
}

function removeService(btn) {
    btn.closest('.service-item').remove();
}

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

