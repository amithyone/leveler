@extends('layouts.frontend')

@section('title', 'Our Services - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Our Services</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-intro" style="margin-bottom: 40px;">
                {!! nl2br(e($page->content)) !!}
            </div>
        @endif
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
@endsection

