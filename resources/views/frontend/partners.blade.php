@extends('layouts.frontend')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Partners - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page && isset($page->sections['header_title']) ? $page->sections['header_title'] : 'Our Partners' }}</h1>
        @if($page && isset($page->sections['header_subtitle']))
        <p>{{ $page->sections['header_subtitle'] }}</p>
        @else
        <p>Collaborating with leading organizations to deliver excellence</p>
        @endif
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-body" style="max-width: 900px; margin: 0 auto 40px; line-height: 1.8; color: #333; font-size: 16px;">
                {!! nl2br(e($page->content)) !!}
            </div>
        @endif

        <!-- Partner Logos Section -->
        @php
            $partnerLogos = $page->sections['partner_logos'] ?? [];
            if (is_string($partnerLogos)) {
                $partnerLogos = json_decode($partnerLogos, true) ?? [];
            }
            if (!is_array($partnerLogos)) {
                $partnerLogos = [];
            }
        @endphp
        @if(!empty($partnerLogos))
        <div class="partner-logos-section" style="margin-bottom: 60px; text-align: center;">
            <h2 style="margin-bottom: 40px; color: #3f3f40; font-size: 2.5rem;">Our Partners</h2>
            <div class="partner-logos-grid" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; max-width: 1200px; margin: 0 auto;">
                @foreach($partnerLogos as $logo)
                @if(isset($logo['image']) && $logo['image'])
                <div class="partner-logo-item" style="flex: 0 0 calc(20% - 32px); max-width: calc(20% - 32px); min-width: 150px; display: flex; align-items: center; justify-content: center; height: 120px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <img src="{{ Storage::url($logo['image']) }}" alt="{{ $logo['name'] ?? 'Partner Logo' }}" style="max-width: 100%; max-height: 100%; object-fit: contain; filter: grayscale(100%); opacity: 0.7; transition: all 0.3s ease;">
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Current Partners Section -->
        @if($partners && $partners->count() > 0)
        <div class="partners-section" style="margin-bottom: 60px;">
            <h2 style="text-align: center; margin-bottom: 40px; color: #667eea; font-size: 2.5rem;">
                {{ $page && isset($page->sections['partners_title']) ? $page->sections['partners_title'] : 'Our Current Partners' }}
            </h2>
            <div class="partners-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 40px;">
                @foreach($partners as $partner)
                <div class="partner-card" style="background: rgb(141, 136, 136); border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease, box-shadow 0.3s ease;">
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
        @php
            $partnerSections = $page->sections ?? [];
            $becomePartnerTitle = $partnerSections['become_partner_title'] ?? 'Become a Partner';
            $becomePartnerDescription = $partnerSections['become_partner_description'] ?? 'Join us in our mission to empower individuals and organizations through quality training and development. Partner with Leveler to create meaningful impact and drive sustainable growth.';
            $becomePartnerButtonText = $partnerSections['become_partner_button_text'] ?? 'Get in Touch';
            $benefits = $partnerSections['benefits'] ?? [
                ['icon' => 'fas fa-handshake', 'title' => 'Collaborative Opportunities', 'text' => 'Work together on projects that create real value and drive positive change in communities.'],
                ['icon' => 'fas fa-users', 'title' => 'Expanded Reach', 'text' => 'Leverage our network and expertise to reach new audiences and markets.'],
                ['icon' => 'fas fa-chart-line', 'title' => 'Shared Success', 'text' => 'Build lasting relationships and achieve mutual growth through strategic partnerships.'],
            ];
        @endphp
        <div class="become-partner-section" style="background: #f8f9fa; border-radius: 16px; padding: 60px 40px; color: #333; text-align: center; margin-top: 60px; border: 1px solid #e0e0e0;">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px; color: #333;">{{ $becomePartnerTitle }}</h2>
            <p style="font-size: 1.2rem; margin-bottom: 30px; color: #666; max-width: 800px; margin-left: auto; margin-right: auto; line-height: 1.8;">
                {{ $becomePartnerDescription }}
            </p>
            
            <div class="partnership-benefits" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin: 40px 0; text-align: left;">
                @foreach($benefits as $benefit)
                <div class="benefit-item" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #e0e0e0;">
                    <div style="font-size: 2.5rem; margin-bottom: 15px; color: #667eea;">
                        <i class="{{ $benefit['icon'] ?? 'fas fa-star' }}"></i>
                    </div>
                    <h3 style="margin-bottom: 10px; font-size: 1.3rem; color: #333;">{{ $benefit['title'] ?? '' }}</h3>
                    <p style="color: #666; line-height: 1.6;">{{ $benefit['text'] ?? '' }}</p>
                </div>
                @endforeach
            </div>

            <div style="margin-top: 40px;">
                <a href="{{ route('contact') }}" class="btn btn-primary" style="background: #667eea; color: white; padding: 15px 40px; font-size: 1.1rem; font-weight: 600; border-radius: 50px; text-decoration: none; display: inline-block; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    {{ $becomePartnerButtonText }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            @if($page && isset($page->sections['partnership_info']))
            <div style="margin-top: 40px; padding-top: 40px; border-top: 1px solid #e0e0e0; max-width: 900px; margin-left: auto; margin-right: auto;">
                <div style="text-align: left; line-height: 1.8; color: #666;">
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

.partner-logo-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.partner-logo-item:hover img {
    filter: grayscale(0%) !important;
    opacity: 1 !important;
}

@media (max-width: 1200px) {
    .partner-logos-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

@media (max-width: 992px) {
    .partner-logos-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
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
    
    .partner-logos-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px !important;
    }
}

@media (max-width: 480px) {
    .partner-logos-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
