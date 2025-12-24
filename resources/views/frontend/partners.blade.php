@extends('layouts.frontend')

@section('title', 'Partners - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>Our Partners</h1>
        <p>Collaborating with leading organizations to deliver excellence</p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-body" style="max-width: 900px; margin: 0 auto 40px; line-height: 1.8; color: #333; font-size: 16px;">
                {!! nl2br(e($page->content)) !!}
            </div>
        @endif

        <!-- Current Partners Section -->
        @if($partners && $partners->count() > 0)
        <div class="partners-section" style="margin-bottom: 60px;">
            <h2 style="text-align: center; margin-bottom: 40px; color: #667eea; font-size: 2.5rem;">Our Current Partners</h2>
            <div class="partners-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 40px;">
                @foreach($partners as $partner)
                <div class="partner-card" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    @if($partner->logo)
                    <div class="partner-logo" style="margin-bottom: 20px; height: 120px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    @endif
                    <h3 style="margin-bottom: 10px; color: #333; font-size: 1.3rem;">{{ $partner->name }}</h3>
                    @if($partner->description)
                    <p style="color: #666; font-size: 0.95rem; line-height: 1.6; margin-bottom: 15px;">{{ Str::limit($partner->description, 150) }}</p>
                    @endif
                    @if($partner->website)
                    <a href="{{ $partner->website }}" target="_blank" rel="noopener noreferrer" style="color: #667eea; text-decoration: none; font-weight: 500;">
                        Visit Website <i class="fas fa-external-link-alt"></i>
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Become a Partner Section -->
        <div class="become-partner-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 60px 40px; color: white; text-align: center; margin-top: 60px;">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px; color: white;">Become a Partner</h2>
            <p style="font-size: 1.2rem; margin-bottom: 30px; opacity: 0.95; max-width: 800px; margin-left: auto; margin-right: auto; line-height: 1.8;">
                Join us in our mission to empower individuals and organizations through quality training and development. 
                Partner with Leveler to create meaningful impact and drive sustainable growth.
            </p>
            
            <div class="partnership-benefits" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin: 40px 0; text-align: left;">
                <div class="benefit-item" style="background: rgba(255,255,255,0.1); padding: 25px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <div style="font-size: 2.5rem; margin-bottom: 15px;">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 style="margin-bottom: 10px; font-size: 1.3rem;">Collaborative Opportunities</h3>
                    <p style="opacity: 0.9; line-height: 1.6;">Work together on projects that create real value and drive positive change in communities.</p>
                </div>
                
                <div class="benefit-item" style="background: rgba(255,255,255,0.1); padding: 25px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <div style="font-size: 2.5rem; margin-bottom: 15px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="margin-bottom: 10px; font-size: 1.3rem;">Expanded Reach</h3>
                    <p style="opacity: 0.9; line-height: 1.6;">Leverage our network and expertise to reach new audiences and markets.</p>
                </div>
                
                <div class="benefit-item" style="background: rgba(255,255,255,0.1); padding: 25px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <div style="font-size: 2.5rem; margin-bottom: 15px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 style="margin-bottom: 10px; font-size: 1.3rem;">Shared Success</h3>
                    <p style="opacity: 0.9; line-height: 1.6;">Build lasting relationships and achieve mutual growth through strategic partnerships.</p>
                </div>
            </div>

            <div style="margin-top: 40px;">
                <a href="{{ route('contact') }}" class="btn btn-primary" style="background: white; color: #667eea; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; border-radius: 50px; text-decoration: none; display: inline-block; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    Get in Touch <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            @if($page && isset($page->sections['partnership_info']))
            <div style="margin-top: 40px; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.2); max-width: 900px; margin-left: auto; margin-right: auto;">
                <div style="text-align: left; line-height: 1.8; opacity: 0.95;">
                    {!! $page->sections['partnership_info'] !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<style>
.partner-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {
    .partners-grid {
        grid-template-columns: 1fr !important;
    }
    
    .partnership-benefits {
        grid-template-columns: 1fr !important;
    }
    
    .become-partner-section {
        padding: 40px 20px !important;
    }
    
    .become-partner-section h2 {
        font-size: 2rem !important;
    }
}
</style>
@endsection
