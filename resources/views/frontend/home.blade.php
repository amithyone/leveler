@extends('layouts.frontend')

@section('title', 'Home - Leveler')

@section('content')
@php
    // Get hero slides
    $heroSlides = $page->hero_slides ?? null;
    if ($heroSlides && is_string($heroSlides)) {
        $heroSlides = json_decode($heroSlides, true);
    }
    if (!is_array($heroSlides)) {
        $heroSlides = [];
    }
    // If no slides, create a default one
    if (empty($heroSlides)) {
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

<section class="hero">
    <div class="hero-slider">
        @foreach($heroSlides as $index => $slide)
            @php
                $imageUrl = !empty($slide['image']) ? asset('storage/' . $slide['image']) : '';
                $hasImage = !empty($slide['image']);
            @endphp
            <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" 
                 @if($hasImage) 
                 data-bg-image="{{ $imageUrl }}" 
                 style="background-image: url('{{ $imageUrl }}') !important; background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important;"
                 @else
                 style="background: linear-gradient(135deg, #6B46C1 0%, #9333EA 100%) !important; background-image: none !important;"
                 @endif>
                @if($hasImage)
                <div class="hero-overlay"></div>
                @endif
                <div class="container">
                    @if(!empty($slide['title']))
                    <h1>{!! $slide['title'] !!}</h1>
                    @endif
                    @if(!empty($slide['subtitle']))
                    <p class="hero-subtitle">{{ $slide['subtitle'] }}</p>
                    @endif
                    <div class="hero-buttons">
                        @if(!empty($slide['primary_button_text']))
                        <a href="{{ $slide['primary_button_link'] ?? route('contact') }}" class="btn btn-primary">{{ $slide['primary_button_text'] }}</a>
                        @endif
                        @if(!empty($slide['secondary_button_text']))
                        <a href="{{ $slide['secondary_button_link'] ?? route('contact') }}" class="btn btn-secondary">{{ $slide['secondary_button_text'] }}</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if(count($heroSlides) > 1)
    <div class="hero-indicators">
        @foreach($heroSlides as $index => $slide)
        <span class="indicator {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></span>
        @endforeach
    </div>
    @endif
</section>

@php
    $vulnerability = $page->sections['vulnerability'] ?? [];
@endphp
<section class="vulnerability-section">
    <div class="container">
        <h2>{{ $vulnerability['title'] ?? 'Are you vulnerable to disruption?' }}</h2>
        <p>{{ $vulnerability['text'] ?? 'Having the right product or service is fundamental, but it is not enough. What differentiates businesses is how they manage change in a continuously changing business climate.' }}</p>
        <a href="{{ route('contact') }}" class="btn btn-primary">{{ $vulnerability['button'] ?? 'Reach Out' }}</a>
    </div>
</section>

@php
    $features = $page->sections['features'] ?? [];
    if (empty($features)) {
        $features = [
            ['icon' => 'fas fa-users', 'title' => 'People', 'text' => 'Committed to developing and sourcing the right team for outstanding results.'],
            ['icon' => 'fas fa-chart-line', 'title' => 'Strategy', 'text' => 'Optimizing processes, interconnected network of professionals for result.'],
            ['icon' => 'fas fa-laptop-code', 'title' => 'Technology', 'text' => 'We partner with clients to work directly with them over the long-term.'],
        ];
    }
@endphp
@if(!empty($features))
<section class="features-section">
    <div class="container">
        <div class="feature-grid">
            @foreach($features as $feature)
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="{{ $feature['icon'] ?? 'fas fa-star' }}"></i>
                </div>
                <h3>{{ $feature['title'] ?? '' }}</h3>
                <p>{{ $feature['text'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@php
    $about = $page->sections['about'] ?? [];
    $aboutText = $about['text'] ?? 'Leveler is a Business & Management consulting company whose mandate is to aid growth and sustainability of businesses through strategy development.

Our reputation is built on the foundation of providing business and management solutions that deliver growth, increase profit and boost efficiency.';
@endphp
<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>{{ $about['title'] ?? 'About Us' }}</h2>
                <h3>{{ $about['subtitle'] ?? 'For over 10 years, we have supported businesses to accelerate growth.' }}</h3>
                @if(!empty($aboutText))
                    @foreach(explode("\n", $aboutText) as $paragraph)
                        @if(trim($paragraph))
                            <p>{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                @endif
                <a href="{{ route('about') }}" class="btn btn-primary">{{ $about['button'] ?? 'Know More' }}</a>
            </div>
        </div>
    </div>
</section>

@php
    $why = $page->sections['why'] ?? [];
    $whyItems = $why['items'] ?? [];
    if (empty($whyItems)) {
        $whyItems = [
            ['title' => 'Value', 'text' => 'We deliver value-driven and sustainable solutions that support the business growth aspirations of our clients.'],
            ['title' => 'Unity of Purpose', 'text' => 'We work closely with our clients to uncover business gaps, and design solutions for profit optimization.'],
        ];
    }
@endphp
@if(!empty($whyItems))
<section class="why-section">
    <div class="container">
        <h2>{{ $why['title'] ?? 'Why Leveler' }}</h2>
        <div class="why-grid">
            @foreach($whyItems as $item)
            <div class="why-card">
                <h3>{{ $item['title'] ?? '' }}</h3>
                <p>{{ $item['text'] ?? '' }}</p>
                <a href="#" class="read-more">Read more</a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@php
    $stats = $page->sections['stats'] ?? [];
    $statItems = $stats['items'] ?? [];
    if (empty($statItems)) {
        $statItems = [
            ['number' => '10+', 'label' => 'Years of Existence'],
            ['number' => '25+', 'label' => 'Consultants Nationwide'],
            ['number' => '70+', 'label' => 'Satisfied Clients'],
            ['number' => '10K+', 'label' => 'Trained and Certified'],
        ];
    }
@endphp
@if(!empty($statItems))
<section class="stats-section">
    <div class="container">
        <h2>{{ $stats['title'] ?? 'Enabling businesses to attain desired growth aspirations' }}</h2>
        <div class="stats-grid">
            @foreach($statItems as $stat)
            <div class="stat-card">
                <div class="stat-number">{{ $stat['number'] ?? '' }}</div>
                <div class="stat-label">{{ $stat['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@php
    $services = $page->sections['services'] ?? [];
    $serviceItems = $services['items'] ?? [];
    if (empty($serviceItems)) {
        $serviceItems = [
            ['icon' => 'fas fa-chart-bar', 'title' => 'Business Advisory', 'text' => 'We provide organizations with the insight, methodology, and framework necessary to successfully execute their business strategy.'],
            ['icon' => 'fas fa-users', 'title' => 'Training & Development', 'text' => 'The ability to learn and translate that learning to action rapidly is the ultimate accomplishment.'],
            ['icon' => 'fas fa-user-tie', 'title' => 'Recruitment & Selection', 'text' => 'We source for the most competent candidates using reliable techniques for talent acquisition.'],
        ];
    }
@endphp
@if(!empty($serviceItems))
<section class="services-section">
    <div class="container">
        <h2>{{ $services['title'] ?? 'Our Services' }}</h2>
        <div class="services-grid">
            @foreach($serviceItems as $service)
            <div class="service-card">
                <div class="service-icon">
                    <i class="{{ $service['icon'] ?? 'fas fa-star' }}"></i>
                </div>
                <h3>{{ $service['title'] ?? '' }}</h3>
                <p>{{ $service['text'] ?? '' }}</p>
                <a href="#" class="read-more">Read more</a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@php
    $newsletter = $page->sections['newsletter'] ?? [];
@endphp
<section class="newsletter-section">
    <div class="container">
        <h2>{{ $newsletter['text'] ?? 'Subscribing to our mailing list and receive weekly newsletter with latest news and offers.' }}</h2>
        <form class="newsletter-form">
            <input type="email" placeholder="Enter your email" required>
            <button type="submit" class="btn btn-primary">{{ $newsletter['button'] ?? 'Subscribe' }}</button>
        </form>
    </div>
</section>
@endsection

