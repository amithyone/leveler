@extends('layouts.admin')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-pencil-alt"></i> Edit Page: {{ $page->title }}</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($page->slug !== 'home' && old('slug') !== 'home')
            {{-- Regular page fields - hidden for home page --}}
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
            @endif

            {{-- Hidden fields for home page --}}
            @if($page->slug === 'home' || old('slug') === 'home')
            <input type="hidden" name="slug" value="home">
            <input type="hidden" name="title" value="Home">
            <input type="hidden" name="page_type" value="page">
            <input type="hidden" name="order" value="1">
            @endif

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
                    
                    $heroSlides = old('hero_slides', $page->hero_slides ?? []);
                    if (is_string($heroSlides)) {
                        $heroSlides = json_decode($heroSlides, true) ?? [];
                    }
                    if (!is_array($heroSlides)) {
                        $heroSlides = [];
                    }
                    if (empty($heroSlides)) {
                        // Default slide
                        $heroSlides = [[
                            'image' => '',
                            'title' => 'Welcome to<br>Leveler<br>A Human Capacity Development Company',
                            'subtitle' => '',
                            'primary_button_text' => 'Get a quote',
                            'primary_button_link' => route('contact'),
                            'secondary_button_text' => 'Contact us',
                            'secondary_button_link' => route('contact'),
                        ]];
                    }
                @endphp

                <!-- Hero Slider Section -->
                <div class="form-group" style="margin-bottom: 30px;">
                    <h4 style="margin-bottom: 15px; color: #667eea; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px;">
                        <i class="fas fa-images"></i> Hero Slider
                    </h4>
                    <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Manage your homepage hero slider. Each slide can have its own image, title, and buttons.</p>
                    
                    <div id="hero-slides-container">
                        @foreach($heroSlides as $index => $slide)
                        <div class="hero-slide-item" style="border: 2px solid #e0e0e0; padding: 20px; margin-bottom: 20px; border-radius: 8px; background: #f9f9f9;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <h5 style="margin: 0; color: #667eea;">Slide {{ $index + 1 }}</h5>
                                @if($index > 0)
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeHeroSlide(this)">
                                    <i class="fas fa-trash"></i> Remove Slide
                                </button>
                                @endif
                            </div>
                            
                            <div class="form-group">
                                <label>Slide Image <span class="text-danger">*</span></label>
                                @if(!empty($slide['image']))
                                <input type="hidden" name="hero_slides[{{ $index }}][old_image]" value="{{ $slide['image'] }}">
                                <div style="margin-bottom: 10px;">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($slide['image']) }}" alt="Slide Image" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0; display: block; margin-bottom: 10px;">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="hero_slides[{{ $index }}][remove_image]" value="1">
                                        <span>Remove current image</span>
                                    </label>
                                </div>
                                @endif
                                <input type="file" name="hero_slides[{{ $index }}][image]" class="form-control" accept="image/*" onchange="previewSlideImage(this, {{ $index }})">
                                <small class="form-text">Upload slide background image (max 5MB, formats: jpeg, png, jpg, gif, webp)</small>
                                <div id="slide_preview_{{ $index }}" class="image-preview" style="margin-top: 10px; display: none;">
                                    <img src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Title</label>
                                <textarea name="hero_slides[{{ $index }}][title]" class="form-control" rows="2" placeholder="e.g., Welcome to<br>Leveler<br>A Human Capacity Development Company">{{ $slide['title'] ?? '' }}</textarea>
                                <small class="form-text">You can use &lt;br&gt; tags for line breaks</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Subtitle (Optional)</label>
                                <input type="text" name="hero_slides[{{ $index }}][subtitle]" class="form-control" value="{{ $slide['subtitle'] ?? '' }}" placeholder="Optional subtitle text">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Primary Button Text</label>
                                    <input type="text" name="hero_slides[{{ $index }}][primary_button_text]" class="form-control" value="{{ $slide['primary_button_text'] ?? 'Get a quote' }}" placeholder="Button text">
                                </div>
                                <div class="form-group">
                                    <label>Primary Button Link</label>
                                    <input type="text" name="hero_slides[{{ $index }}][primary_button_link]" class="form-control" value="{{ $slide['primary_button_link'] ?? route('contact') }}" placeholder="Button URL">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Secondary Button Text</label>
                                    <input type="text" name="hero_slides[{{ $index }}][secondary_button_text]" class="form-control" value="{{ $slide['secondary_button_text'] ?? 'Contact us' }}" placeholder="Button text">
                                </div>
                                <div class="form-group">
                                    <label>Secondary Button Link</label>
                                    <input type="text" name="hero_slides[{{ $index }}][secondary_button_link]" class="form-control" value="{{ $slide['secondary_button_link'] ?? route('contact') }}" placeholder="Button URL">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Display Duration (seconds)</label>
                                <input type="number" name="hero_slides[{{ $index }}][duration]" class="form-control" value="{{ $slide['duration'] ?? 5 }}" min="1" max="30" placeholder="5">
                                <small class="form-text">How long this slide should be displayed before auto-advancing (1-30 seconds, default: 5)</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <button type="button" class="btn btn-secondary" onclick="addHeroSlide()">
                        <i class="fas fa-plus"></i> Add Another Slide
                    </button>
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

            {{-- Partners Page Sections --}}
            @if($page->slug === 'partners' || old('slug') === 'partners')
            <div class="partners-sections" style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #e0e0e0;">
                <h3 style="margin-bottom: 20px; color: #667eea;">
                    <i class="fas fa-handshake"></i> Partners Page Sections
                </h3>
                
                @php
                    $partnerSections = old('sections', $page->sections ?? []);
                    if (is_string($partnerSections)) {
                        $partnerSections = json_decode($partnerSections, true) ?? [];
                    }
                    if (!is_array($partnerSections)) {
                        $partnerSections = [];
                    }
                @endphp

                <div class="form-group">
                    <label>Page Header</label>
                    <div class="form-group">
                        <label for="partners_header_title">Header Title</label>
                        <input type="text" id="partners_header_title" name="sections[header_title]" class="form-control" value="{{ $partnerSections['header_title'] ?? 'Our Partners' }}" placeholder="Page header title">
                    </div>
                    <div class="form-group">
                        <label for="partners_header_subtitle">Header Subtitle</label>
                        <input type="text" id="partners_header_subtitle" name="sections[header_subtitle]" class="form-control" value="{{ $partnerSections['header_subtitle'] ?? 'Collaborating with leading organizations to deliver excellence' }}" placeholder="Page header subtitle">
                    </div>
                </div>

                <div class="form-group">
                    <label for="partners_title">Current Partners Section Title</label>
                    <input type="text" id="partners_title" name="sections[partners_title]" class="form-control" value="{{ $partnerSections['partners_title'] ?? 'Our Current Partners' }}" placeholder="Section title">
                </div>

                <div class="form-group">
                    <label>Become a Partner Section</label>
                    <div class="form-group">
                        <label for="become_partner_title">Section Title</label>
                        <input type="text" id="become_partner_title" name="sections[become_partner_title]" class="form-control" value="{{ $partnerSections['become_partner_title'] ?? 'Become a Partner' }}" placeholder="Section title">
                    </div>
                    <div class="form-group">
                        <label for="become_partner_description">Description</label>
                        <textarea id="become_partner_description" name="sections[become_partner_description]" class="form-control" rows="4" placeholder="Description text">{{ $partnerSections['become_partner_description'] ?? 'Join us in our mission to empower individuals and organizations through quality training and development. Partner with Leveler to create meaningful impact and drive sustainable growth.' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="become_partner_button_text">Button Text</label>
                        <input type="text" id="become_partner_button_text" name="sections[become_partner_button_text]" class="form-control" value="{{ $partnerSections['become_partner_button_text'] ?? 'Get in Touch' }}" placeholder="Button text">
                    </div>
                </div>

                <div class="form-group">
                    <label>Partnership Benefits</label>
                    <div id="benefits-container">
                        @php
                            $benefits = $partnerSections['benefits'] ?? [
                                ['icon' => 'fas fa-handshake', 'title' => 'Collaborative Opportunities', 'text' => 'Work together on projects that create real value and drive positive change in communities.'],
                                ['icon' => 'fas fa-users', 'title' => 'Expanded Reach', 'text' => 'Leverage our network and expertise to reach new audiences and markets.'],
                                ['icon' => 'fas fa-chart-line', 'title' => 'Shared Success', 'text' => 'Build lasting relationships and achieve mutual growth through strategic partnerships.'],
                            ];
                        @endphp
                        @foreach($benefits as $index => $benefit)
                        <div class="benefit-item" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <strong>Benefit {{ $index + 1 }}</strong>
                                @if($index > 0)
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeBenefit(this)">Remove</button>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Icon Class (Font Awesome)</label>
                                <input type="text" name="sections[benefits][{{ $index }}][icon]" class="form-control" value="{{ $benefit['icon'] ?? '' }}" placeholder="e.g., fas fa-handshake">
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="sections[benefits][{{ $index }}][title]" class="form-control" value="{{ $benefit['title'] ?? '' }}" placeholder="Benefit title">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="sections[benefits][{{ $index }}][text]" class="form-control" rows="2" placeholder="Benefit description">{{ $benefit['text'] ?? '' }}</textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addBenefit()">Add Benefit</button>
                </div>

                <div class="form-group">
                    <label for="partnership_info">Additional Partnership Information</label>
                    <textarea id="partnership_info" name="sections[partnership_info]" class="form-control" rows="6" placeholder="Additional information about partnerships (supports HTML)">{{ $partnerSections['partnership_info'] ?? '' }}</textarea>
                    <small class="form-text">This will appear at the bottom of the "Become a Partner" section. HTML is supported.</small>
                </div>

                <div class="form-group">
                    <label>Partner Logos</label>
                    <p class="form-text" style="margin-bottom: 20px;">Upload partner logos to display on the partners page. Maximum 5 logos per row. Logos will be displayed in a centered grid.</p>
                    
                    <div id="partner-logos-container">
                        @php
                            $partnerLogos = $partnerSections['partner_logos'] ?? [];
                            if (is_string($partnerLogos)) {
                                $partnerLogos = json_decode($partnerLogos, true) ?? [];
                            }
                            if (!is_array($partnerLogos)) {
                                $partnerLogos = [];
                            }
                            // Add default dummy logos if empty
                            if (empty($partnerLogos)) {
                                $partnerLogos = [
                                    ['name' => 'Partner 1', 'image' => ''],
                                    ['name' => 'Partner 2', 'image' => ''],
                                    ['name' => 'Partner 3', 'image' => ''],
                                    ['name' => 'Partner 4', 'image' => ''],
                                    ['name' => 'Partner 5', 'image' => ''],
                                ];
                            }
                        @endphp
                        @foreach($partnerLogos as $index => $logo)
                        <div class="partner-logo-item" style="border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 20px; border-radius: 8px; background: #f9f9f9;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <strong>Logo {{ $index + 1 }}</strong>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removePartnerLogo(this)">Remove</button>
                            </div>
                            
                            @if(!empty($logo['image']))
                            <div class="current-logo" style="margin-bottom: 15px;">
                                <img src="{{ Storage::url($logo['image']) }}" alt="Current Logo" style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 2px solid #e0e0e0; display: block; margin-bottom: 10px; object-fit: contain; background: white; padding: 10px;">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="partner_logos[{{ $index }}][remove_image]" value="1">
                                    <span>Remove current logo</span>
                                </label>
                                <input type="hidden" name="partner_logos[{{ $index }}][existing_image]" value="{{ $logo['image'] }}">
                            </div>
                            @endif
                            
                            <div class="form-group">
                                <label>Partner Name</label>
                                <input type="text" name="partner_logos[{{ $index }}][name]" class="form-control" value="{{ $logo['name'] ?? '' }}" placeholder="Partner name (for alt text)">
                            </div>
                            
                            <div class="form-group">
                                <label>Logo Image</label>
                                <input type="file" name="partner_logos[{{ $index }}][image]" class="form-control" accept="image/*" onchange="previewPartnerLogo(this, 'partner_logo_preview_{{ $index }}')">
                                <small class="form-text">Recommended: PNG with transparent background, max 2MB. Logo will be displayed at max 200px width.</small>
                                <div id="partner_logo_preview_{{ $index }}" class="image-preview" style="margin-top: 10px; display: none;">
                                    <img src="" alt="Logo Preview" style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 2px solid #e0e0e0; background: white; padding: 10px;">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addPartnerLogo()">Add Another Logo</button>
                </div>
            </div>
            @endif

            {{-- Header Management Section --}}
            @php
                // Always get header settings from home page (global settings)
                $homePage = \App\Models\Page::where('slug', 'home')->first();
                $headerSettings = [];
                if ($homePage && isset($homePage->sections['header'])) {
                    $headerSettings = $homePage->sections['header'];
                    if (is_string($headerSettings)) {
                        $headerSettings = json_decode($headerSettings, true) ?? [];
                    }
                }
                if (!is_array($headerSettings)) {
                    $headerSettings = [];
                }
                $headerLogo = $headerSettings['logo'] ?? '';
                $headerMenuItems = $headerSettings['menu_items'] ?? [
                    ['label' => 'About DHC', 'url' => route('about'), 'order' => 1],
                    ['label' => 'Our Services', 'url' => route('services'), 'order' => 2],
                    ['label' => 'Courses', 'url' => route('courses'), 'order' => 3],
                    ['label' => 'Partners', 'url' => route('partners'), 'order' => 4],
                    ['label' => 'Tips & Updates', 'url' => route('tips-updates'), 'order' => 5],
                    ['label' => 'Contact', 'url' => route('contact'), 'order' => 6],
                ];
            @endphp
            
            <div class="header-management-section" style="margin-top: 40px; padding-top: 30px; border-top: 2px solid #e0e0e0;">
                <h3 style="margin-bottom: 20px; color: #667eea;">
                    <i class="fas fa-header"></i> Header Management
                </h3>
                <p class="form-text" style="margin-bottom: 20px; color: #666;">
                    <strong>Note:</strong> Header settings are global and will be applied site-wide. Changes are saved to the Home page.
                </p>
                
                <div class="form-group">
                    <label>Header Logo</label>
                    @if(!empty($headerLogo))
                    <div class="current-image" style="margin-bottom: 15px;">
                        <img src="{{ Storage::url($headerLogo) }}" alt="Current Header Logo" style="max-width: 200px; max-height: 80px; border-radius: 8px; border: 2px solid #e0e0e0; display: block; margin-bottom: 10px; object-fit: contain; background: white; padding: 10px;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="header[remove_logo]" value="1">
                            <span>Remove current logo</span>
                        </label>
                        <input type="hidden" name="header[existing_logo]" value="{{ $headerLogo }}">
                    </div>
                    @endif
                    <input type="file" name="header[logo]" class="form-control" accept="image/*" onchange="previewHeaderLogo(this, 'header_logo_preview')">
                    <small class="form-text">Upload header logo (Recommended: PNG with transparent background, max 2MB, max height 80px)</small>
                    <div id="header_logo_preview" class="image-preview" style="margin-top: 10px; display: none;">
                        <img src="" alt="Logo Preview" style="max-width: 200px; max-height: 80px; border-radius: 8px; border: 2px solid #e0e0e0; background: white; padding: 10px; object-fit: contain;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Brand Name</label>
                    <input type="text" name="header[brand_name]" class="form-control" value="{{ old('header.brand_name', $headerSettings['brand_name'] ?? 'Leveler') }}" placeholder="e.g., Leveler">
                    <small class="form-text">Text to display if no logo is uploaded</small>
                </div>

                <div class="form-group">
                    <label>Menu Items</label>
                    <p class="form-text" style="margin-bottom: 20px;">Manage navigation menu items. Drag to reorder.</p>
                    
                    <div id="header-menu-items-container">
                        @foreach($headerMenuItems as $index => $item)
                        <div class="menu-item-row" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9f9f9;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <strong>Menu Item {{ $index + 1 }}</strong>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeMenuItem(this)">Remove</button>
                            </div>
                            <div class="form-row">
                                <div class="form-group" style="flex: 1;">
                                    <label>Label</label>
                                    <input type="text" name="header[menu_items][{{ $index }}][label]" class="form-control" value="{{ old("header.menu_items.{$index}.label", $item['label'] ?? '') }}" placeholder="e.g., About Us" required>
                                </div>
                                <div class="form-group" style="flex: 2;">
                                    <label>URL</label>
                                    <input type="text" name="header[menu_items][{{ $index }}][url]" class="form-control" value="{{ old("header.menu_items.{$index}.url", $item['url'] ?? '') }}" placeholder="e.g., /about or {{ route('about') }}" required>
                                </div>
                                <div class="form-group" style="flex: 0 0 80px;">
                                    <label>Order</label>
                                    <input type="number" name="header[menu_items][{{ $index }}][order]" class="form-control" value="{{ old("header.menu_items.{$index}.order", $item['order'] ?? $index + 1) }}" min="1">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addMenuItem()">Add Menu Item</button>
                </div>
            </div>

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

let heroSlideIndex = {{ count($heroSlides ?? []) }};

function addHeroSlide() {
    const container = document.getElementById('hero-slides-container');
    const div = document.createElement('div');
    div.className = 'hero-slide-item';
    div.style.cssText = 'border: 2px solid #e0e0e0; padding: 20px; margin-bottom: 20px; border-radius: 8px; background: #f9f9f9;';
    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h5 style="margin: 0; color: #667eea;">Slide ${heroSlideIndex + 1}</h5>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeHeroSlide(this)">
                <i class="fas fa-trash"></i> Remove Slide
            </button>
        </div>
        
        <div class="form-group">
            <label>Slide Image <span class="text-danger">*</span></label>
            <input type="file" name="hero_slides[${heroSlideIndex}][image]" class="form-control" accept="image/*" onchange="previewSlideImage(this, ${heroSlideIndex})">
            <small class="form-text">Upload slide background image (max 5MB, formats: jpeg, png, jpg, gif, webp)</small>
            <div id="slide_preview_${heroSlideIndex}" class="image-preview" style="margin-top: 10px; display: none;">
                <img src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
            </div>
        </div>
        
        <div class="form-group">
            <label>Title</label>
            <textarea name="hero_slides[${heroSlideIndex}][title]" class="form-control" rows="2" placeholder="e.g., Welcome to<br>Leveler<br>A Human Capacity Development Company"></textarea>
            <small class="form-text">You can use &lt;br&gt; tags for line breaks</small>
        </div>
        
        <div class="form-group">
            <label>Subtitle (Optional)</label>
            <input type="text" name="hero_slides[${heroSlideIndex}][subtitle]" class="form-control" placeholder="Optional subtitle text">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Primary Button Text</label>
                <input type="text" name="hero_slides[${heroSlideIndex}][primary_button_text]" class="form-control" value="Get a quote" placeholder="Button text">
            </div>
            <div class="form-group">
                <label>Primary Button Link</label>
                <input type="text" name="hero_slides[${heroSlideIndex}][primary_button_link]" class="form-control" value="{{ route('contact') }}" placeholder="Button URL">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Secondary Button Text</label>
                <input type="text" name="hero_slides[${heroSlideIndex}][secondary_button_text]" class="form-control" value="Contact us" placeholder="Button text">
            </div>
            <div class="form-group">
                <label>Secondary Button Link</label>
                <input type="text" name="hero_slides[${heroSlideIndex}][secondary_button_link]" class="form-control" value="{{ route('contact') }}" placeholder="Button URL">
            </div>
        </div>
        
        <div class="form-group">
            <label>Display Duration (seconds)</label>
            <input type="number" name="hero_slides[${heroSlideIndex}][duration]" class="form-control" value="5" min="1" max="30" placeholder="5">
            <small class="form-text">How long this slide should be displayed before auto-advancing (1-30 seconds, default: 5)</small>
        </div>
    `;
    container.appendChild(div);
    heroSlideIndex++;
}

function removeHeroSlide(btn) {
    if (confirm('Are you sure you want to remove this slide?')) {
        btn.closest('.hero-slide-item').remove();
    }
}

function previewSlideImage(input, index) {
    const preview = document.getElementById(`slide_preview_${index}`);
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

function addBenefit() {
    const container = document.getElementById('benefits-container');
    const index = container.children.length;
    const benefitHtml = `
        <div class="benefit-item" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <strong>Benefit ${index + 1}</strong>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeBenefit(this)">Remove</button>
            </div>
            <div class="form-group">
                <label>Icon Class (Font Awesome)</label>
                <input type="text" name="sections[benefits][${index}][icon]" class="form-control" value="" placeholder="e.g., fas fa-handshake">
            </div>
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="sections[benefits][${index}][title]" class="form-control" value="" placeholder="Benefit title">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="sections[benefits][${index}][text]" class="form-control" rows="2" placeholder="Benefit description"></textarea>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', benefitHtml);
}

function removeBenefit(element) {
    element.closest('.benefit-item').remove();
}

function addPartnerLogo() {
    const container = document.getElementById('partner-logos-container');
    const index = container.children.length;
    const logoHtml = `
        <div class="partner-logo-item" style="border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 20px; border-radius: 8px; background: #f9f9f9;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <strong>Logo ${index + 1}</strong>
                <button type="button" class="btn btn-danger btn-sm" onclick="removePartnerLogo(this)">Remove</button>
            </div>
            <div class="form-group">
                <label>Partner Name</label>
                <input type="text" name="partner_logos[${index}][name]" class="form-control" value="" placeholder="Partner name (for alt text)">
            </div>
            <div class="form-group">
                <label>Logo Image</label>
                <input type="file" name="partner_logos[${index}][image]" class="form-control" accept="image/*" onchange="previewPartnerLogo(this, 'partner_logo_preview_${index}')">
                <small class="form-text">Recommended: PNG with transparent background, max 2MB. Logo will be displayed at max 200px width.</small>
                <div id="partner_logo_preview_${index}" class="image-preview" style="margin-top: 10px; display: none;">
                    <img src="" alt="Logo Preview" style="max-width: 200px; max-height: 100px; border-radius: 8px; border: 2px solid #e0e0e0; background: white; padding: 10px;">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', logoHtml);
}

function removePartnerLogo(element) {
    if (confirm('Are you sure you want to remove this logo? The logo file will be deleted if saved.')) {
        element.closest('.partner-logo-item').remove();
    }
}

function previewPartnerLogo(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

function previewHeaderLogo(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

let menuItemIndex = {{ count($headerMenuItems) }};

function addMenuItem() {
    const container = document.getElementById('header-menu-items-container');
    const index = menuItemIndex;
    const menuItemHtml = `
        <div class="menu-item-row" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9f9f9;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <strong>Menu Item ${index + 1}</strong>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeMenuItem(this)">Remove</button>
            </div>
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Label</label>
                    <input type="text" name="header[menu_items][${index}][label]" class="form-control" value="" placeholder="e.g., About Us" required>
                </div>
                <div class="form-group" style="flex: 2;">
                    <label>URL</label>
                    <input type="text" name="header[menu_items][${index}][url]" class="form-control" value="" placeholder="e.g., /about or {{ route('about') }}" required>
                </div>
                <div class="form-group" style="flex: 0 0 80px;">
                    <label>Order</label>
                    <input type="number" name="header[menu_items][${index}][order]" class="form-control" value="${index + 1}" min="1">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', menuItemHtml);
    menuItemIndex++;
}

function removeMenuItem(element) {
    if (confirm('Are you sure you want to remove this menu item?')) {
        element.closest('.menu-item-row').remove();
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

