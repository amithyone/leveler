<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        // Get site settings from home page
        $homePage = \App\Models\Page::where('slug', 'home')->first();
        $siteSettings = [];
        if ($homePage && isset($homePage->sections['site_settings'])) {
            $siteSettings = $homePage->sections['site_settings'];
            if (is_string($siteSettings)) {
                $siteSettings = json_decode($siteSettings, true) ?? [];
            }
        }
        if (!is_array($siteSettings)) {
            $siteSettings = [];
        }
        $siteName = $siteSettings['site_name'] ?? 'Leveler';
        $favicon = $siteSettings['favicon'] ?? '';
    @endphp
    <title>@yield('title', $siteName)</title>
    @if(!empty($favicon))
    <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    @php
        // Get header settings from any page (prefer home page, but any page with header settings will work)
        $headerPage = \App\Models\Page::where('slug', 'home')->first() ?? \App\Models\Page::whereNotNull('sections')->first();
        $headerSettings = [];
        if ($headerPage && isset($headerPage->sections['header'])) {
            $headerSettings = $headerPage->sections['header'];
        }
        
        $headerLogo = $headerSettings['logo'] ?? '';
        $brandName = $headerSettings['brand_name'] ?? 'Leveler';
        $menuItems = $headerSettings['menu_items'] ?? [
            ['label' => 'About DHC', 'url' => route('about')],
            ['label' => 'Our Services', 'url' => route('services')],
            ['label' => 'Courses', 'url' => route('courses')],
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => 'Partners', 'url' => route('partners')],
            ['label' => 'Tips & Updates', 'url' => route('tips-updates')],
            ['label' => 'Contact', 'url' => route('contact')],
        ];
    @endphp
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="{{ route('home') }}">
                    @if(!empty($headerLogo))
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($headerLogo) }}" alt="{{ $brandName }}" style="max-height: 50px; width: auto;">
                    @else
                        {{ $brandName }}
                    @endif
                </a>
            </div>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-menu" id="navMenu">
                @foreach($menuItems as $item)
                <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
                @auth('web')
                    {{-- Admin is logged in --}}
                    <li><a href="{{ route('trainee.dashboard') }}" class="nav-portal-btn"><i class="fas fa-user-graduate"></i> Portal</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-logout-btn">Logout</button>
                        </form>
                    </li>
                @elseauth('trainee')
                    {{-- Trainee is logged in --}}
                    <li><a href="{{ route('trainee.dashboard') }}" class="nav-portal-btn"><i class="fas fa-user-graduate"></i> Portal</a></li>
                    <li>
                        <form action="{{ route('trainee.logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-logout-btn">Logout</button>
                        </form>
                    </li>
                @else
                    {{-- Not logged in --}}
                    <li><a href="{{ route('trainee.login') }}" class="nav-login-btn">Login</a></li>
                    <li><a href="{{ route('trainee.register') }}" class="nav-register-btn">Register</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    @php
                        // Get site settings for footer
                        $homePage = \App\Models\Page::where('slug', 'home')->first();
                        $siteSettings = [];
                        if ($homePage && isset($homePage->sections['site_settings'])) {
                            $siteSettings = $homePage->sections['site_settings'];
                            if (is_string($siteSettings)) {
                                $siteSettings = json_decode($siteSettings, true) ?? [];
                            }
                        }
                        if (!is_array($siteSettings)) {
                            $siteSettings = [];
                        }
                        $footerLogo = $siteSettings['logo'] ?? '';
                        $footerSiteName = $siteSettings['site_name'] ?? 'Leveler';
                        $footerTagline = $siteSettings['site_tagline'] ?? 'Leveler is a development and management consulting company.';
                        $socialLinks = $siteSettings['social_links'] ?? [];
                    @endphp
                    <div class="footer-logo-section">
                        @if(!empty($footerLogo))
                            <a href="{{ route('home') }}" style="display: inline-block; margin-bottom: 15px;">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($footerLogo) }}" alt="{{ $footerSiteName }}" style="max-height: 60px; width: auto; max-width: 200px;">
                            </a>
                        @else
                            <h3>{{ $footerSiteName }}</h3>
                        @endif
                    </div>
                    @if(!empty($footerTagline))
                    <p>{{ $footerTagline }}</p>
                    @endif
                    
                    {{-- Social Media Links --}}
                    @if(!empty($socialLinks) && (isset($socialLinks['facebook']) || isset($socialLinks['twitter']) || isset($socialLinks['instagram']) || isset($socialLinks['linkedin']) || isset($socialLinks['youtube'])))
                    <div class="footer-social-links" style="margin-top: 20px;">
                        @if(!empty($socialLinks['facebook']))
                        <a href="{{ $socialLinks['facebook'] }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 15px; color: #cbd5e0; font-size: 20px; transition: color 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#cbd5e0'">
                            <i class="fab fa-facebook"></i>
                        </a>
                        @endif
                        @if(!empty($socialLinks['twitter']))
                        <a href="{{ $socialLinks['twitter'] }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 15px; color: #cbd5e0; font-size: 20px; transition: color 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#cbd5e0'">
                            <i class="fab fa-twitter"></i>
                        </a>
                        @endif
                        @if(!empty($socialLinks['instagram']))
                        <a href="{{ $socialLinks['instagram'] }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 15px; color: #cbd5e0; font-size: 20px; transition: color 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#cbd5e0'">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if(!empty($socialLinks['linkedin']))
                        <a href="{{ $socialLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 15px; color: #cbd5e0; font-size: 20px; transition: color 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#cbd5e0'">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        @endif
                        @if(!empty($socialLinks['youtube']))
                        <a href="{{ $socialLinks['youtube'] }}" target="_blank" rel="noopener noreferrer" style="display: inline-block; margin-right: 15px; color: #cbd5e0; font-size: 20px; transition: color 0.3s ease;" onmouseover="this.style.color='#667eea'" onmouseout="this.style.color='#cbd5e0'">
                            <i class="fab fa-youtube"></i>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="footer-section">
                    <h4>Site Navigation</h4>
                    <ul>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('services') }}">Services</a></li>
                        <li><a href="{{ route('courses') }}">Courses</a></li>
                        <li><a href="{{ route('blog.index') }}">Blog</a></li>
                        <li><a href="{{ route('partners') }}">Partners</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="{{ route('faqs') }}">FAQs</a></li>
                        <li><a href="{{ route('trainee.login') }}">Trainee Login</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="{{ route('faqs') }}">FAQ'S</a></li>
                        <li><a href="{{ route('news') }}">News</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Details</h4>
                    @php
                        $contactPage = \App\Models\Page::findBySlug('contact');
                        $contactDetails = $contactPage->contact_details ?? [];
                        $address = $contactDetails['address'] ?? '';
                        $addressLine2 = $contactDetails['address_line2'] ?? '';
                        $phone = $contactDetails['phone'] ?? '';
                        $email = $contactDetails['email'] ?? '';
                        $workingHours = $contactDetails['working_hours'] ?? '';
                    @endphp
                    
                    @if($address || $addressLine2)
                        <p><i class="fas fa-map-marker-alt"></i> 
                            @if($address){{ $address }}@endif
                            @if($address && $addressLine2), @endif
                            @if($addressLine2){{ $addressLine2 }}@endif
                        </p>
                    @endif
                    @if($phone)
                        <p><i class="fas fa-phone"></i> {{ $phone }}</p>
                    @endif
                    @if($email)
                        <p><i class="fas fa-envelope"></i> <a href="mailto:{{ $email }}" style="color: inherit; text-decoration: none;">{{ $email }}</a></p>
                    @endif
                    @if($workingHours)
                        <p><i class="fas fa-clock"></i> {{ $workingHours }}</p>
                    @endif
                </div>
            </div>
            <div class="footer-bottom">
                <p>Copyright © {{ date('Y') }} {{ $footerSiteName ?? 'Leveler' }}</p>
                <div class="footer-links">
                    <a href="{{ route('terms') }}">Terms of Use</a>
                    <a href="{{ route('privacy') }}">Privacy Policy</a>
                    <a href="{{ route('legal') }}">Legal</a>
                </div>
            </div>
            <div class="footer-credit" style="text-align: center; padding-top: 20px; margin-top: 20px; border-top: 1px solid #4a5568;">
                <p style="color: #cbd5e0; font-size: 14px; margin: 0;">
                    Designed and managed by <a href="#" style="color: #667eea; text-decoration: none; font-weight: 600;">Amithy One Media</a>
                </p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/frontend.js') }}"></script>
</body>
</html>

