@extends('layouts.frontend')

@section('title', 'Home - Leveler')

@section('content')
<section class="hero">
    <div class="hero-slider">
        @php
            $hasSliderImages = $page && $page->slider_images && is_array($page->slider_images) && count($page->slider_images) > 0;
        @endphp
        @if($hasSliderImages)
            @foreach($page->slider_images as $index => $sliderImage)
            @php
                $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($sliderImage);
            @endphp
            <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $imageUrl }}');">
                <div class="hero-overlay"></div>
                <div class="container">
                    <h1>Welcome to<br>Leveler<br>A Human Capacity Development Company</h1>
                    <div class="hero-buttons">
                        <a href="{{ route('contact') }}" class="btn btn-primary">Get a quote</a>
                        <a href="{{ route('contact') }}" class="btn btn-secondary">Contact us</a>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="hero-slide active">
                <div class="container">
                    <h1>Welcome to<br>Leveler<br>A Human Capacity Development Company</h1>
                    <div class="hero-buttons">
                        <a href="{{ route('contact') }}" class="btn btn-primary">Get a quote</a>
                        <a href="{{ route('contact') }}" class="btn btn-secondary">Contact us</a>
                    </div>
                </div>
            </div>
            <div class="hero-slide">
                <div class="container">
                    <h1>We project you to new<br>Heights of Excellence</h1>
                    <p>Helping you to find and keep the right people, create the winning strategy using the appropriate technology within resource limits.</p>
                    <div class="hero-buttons">
                        <a href="{{ route('contact') }}" class="btn btn-primary">Get a quote</a>
                        <a href="{{ route('contact') }}" class="btn btn-secondary">Contact us</a>
                    </div>
                </div>
            </div>
            <div class="hero-slide">
                <div class="container">
                    <h1>Let's help you<br>Translate Learning to results</h1>
                    <p>Our reputation is based on providing relevant, practical and on-target learning solutions that are cost-effective and client-focused.</p>
                    <div class="hero-buttons">
                        <a href="{{ route('contact') }}" class="btn btn-primary">Get a quote</a>
                        <a href="{{ route('contact') }}" class="btn btn-secondary">Contact us</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="hero-indicators">
        @if($hasSliderImages)
            @foreach($page->slider_images as $index => $sliderImage)
            <span class="indicator {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></span>
            @endforeach
        @else
            <span class="indicator active" data-slide="0"></span>
            <span class="indicator" data-slide="1"></span>
            <span class="indicator" data-slide="2"></span>
        @endif
    </div>
</section>

<section class="vulnerability-section">
    <div class="container">
        <h2>Are you vulnerable to disruption?</h2>
        <p>Having the right product or service is fundamental, but it is not enough. What differentiates businesses is how they manage change in a continuously changing business climate.</p>
        <a href="{{ route('contact') }}" class="btn btn-primary">Reach Out</a>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>People</h3>
                <p>Committed to developing and sourcing the right team for outstanding results.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Strategy</h3>
                <p>Optimizing processes, interconnected network of professionals for result.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <h3>Technology</h3>
                <p>We partner with clients to work directly with them over the long-term.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>About Us</h2>
                <h3>For over 10 years, we have supported businesses to accelerate growth.</h3>
                <p>Leveler is a Business & Management consulting company whose mandate is to aid growth and sustainability of businesses through strategy development.</p>
                <p>Our reputation is built on the foundation of providing business and management solutions that deliver growth, increase profit and boost efficiency.</p>
                <a href="{{ route('about') }}" class="btn btn-primary">Know More</a>
            </div>
        </div>
    </div>
</section>

<section class="why-section">
    <div class="container">
                <h2>Why Leveler</h2>
        <div class="why-grid">
            <div class="why-card">
                <h3>Value</h3>
                <p>We deliver value-driven and sustainable solutions that support the business growth aspirations of our clients.</p>
                <a href="#" class="read-more">Read more</a>
            </div>
            <div class="why-card">
                <h3>Unity of Purpose</h3>
                <p>We work closely with our clients to uncover business gaps, and design solutions for profit optimization.</p>
                <a href="#" class="read-more">Read more</a>
            </div>
        </div>
    </div>
</section>

<section class="stats-section">
    <div class="container">
        <h2>Enabling businesses to attain desired growth aspirations</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">10+</div>
                <div class="stat-label">Years of Existence</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">25+</div>
                <div class="stat-label">Consultants Nationwide</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">70+</div>
                <div class="stat-label">Satisfied Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">10K+</div>
                <div class="stat-label">Trained and Certified</div>
            </div>
        </div>
    </div>
</section>

<section class="services-section">
    <div class="container">
        <h2>Our Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Business Advisory</h3>
                <p>We provide organizations with the insight, methodology, and framework necessary to successfully execute their business strategy.</p>
                <a href="#" class="read-more">Read more</a>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Training & Development</h3>
                <p>The ability to learn and translate that learning to action rapidly is the ultimate accomplishment.</p>
                <a href="#" class="read-more">Read more</a>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3>Recruitment & Selection</h3>
                <p>We source for the most competent candidates using reliable techniques for talent acquisition.</p>
                <a href="#" class="read-more">Read more</a>
            </div>
        </div>
    </div>
</section>

<section class="newsletter-section">
    <div class="container">
        <h2>Subscribing to our mailing list and receive weekly newsletter with latest news and offers.</h2>
        <form class="newsletter-form">
            <input type="email" placeholder="Enter your email" required>
            <button type="submit" class="btn btn-primary">Subscribe</button>
        </form>
    </div>
</section>
@endsection

