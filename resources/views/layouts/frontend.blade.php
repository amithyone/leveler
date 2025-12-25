<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Leveler')</title>
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
                    <h3>Leveler</h3>
                    <p>Leveler is a development and management consulting company.</p>
                </div>
                <div class="footer-section">
                    <h4>Site Navigation</h4>
                    <ul>
                        <li><a href="{{ route('faqs') }}">FAQs</a></li>
                        <li><a href="{{ route('careers') }}">Careers</a></li>
                        <li><a href="{{ route('courses') }}">Courses</a></li>
                        <li><a href="{{ route('e-learning') }}">e-Learning</a></li>
                        <li><a href="{{ route('trainee.login') }}">Trainee Login</a></li>
                        <li><a href="{{ route('trainee.register') }}">Register For Course</a></li>
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
                        $address = $contactDetails['address'] ?? 'Nigeria';
                        $addressLine2 = $contactDetails['address_line2'] ?? 'Plot 559c, Capital Str., A11, Garki, Abuja';
                        $phone = $contactDetails['phone'] ?? '(+234) 806-141-3675';
                        $email = $contactDetails['email'] ?? '';
                        $workingHours = $contactDetails['working_hours'] ?? 'Mon - Fri: 9.00 to 17.00';
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
                <p>Copyright © 2024 Leveler</p>
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

