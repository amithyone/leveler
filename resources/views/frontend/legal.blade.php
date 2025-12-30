@extends('layouts.frontend')

@section('title', 'Legal - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'Legal Information' }}</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-body">
                {!! nl2br(e($page->content)) !!}
            </div>
        @else
            <div class="legal-content">
                <h2>Legal Information</h2>

                <div class="legal-section">
                    <h3>Company Information</h3>
                    @php
                        $contactPage = \App\Models\Page::findBySlug('contact');
                        $contactDetails = $contactPage->contact_details ?? [];
                        $address = $contactDetails['address'] ?? '';
                        $addressLine2 = $contactDetails['address_line2'] ?? '';
                        $phone = $contactDetails['phone'] ?? '';
                        $email = $contactDetails['email'] ?? '';
                    @endphp
                    <p><strong>Company Name:</strong> Leveler</p>
                    <p><strong>Business Type:</strong> Development and Management Consulting Company</p>
                    @if($address || $addressLine2)
                        <p><strong>Address:</strong> 
                            @if($address){{ $address }}@endif
                            @if($address && $addressLine2), @endif
                            @if($addressLine2){{ $addressLine2 }}@endif
                        </p>
                    @endif
                    @if($phone)
                        <p><strong>Phone:</strong> {{ $phone }}</p>
                    @endif
                    @if($email)
                        <p><strong>Email:</strong> {{ $email }}</p>
                    @endif
                </div>

                <div class="legal-section">
                    <h3>Intellectual Property</h3>
                    <p>All content on this website, including course materials, text, graphics, logos, and software, is the property of Leveler and is protected by copyright and other intellectual property laws.</p>
                </div>

                <div class="legal-section">
                    <h3>Disclaimer</h3>
                    <p>The information on this website is provided on an "as is" basis. To the fullest extent permitted by law, Leveler excludes all representations, warranties, and conditions relating to this website and the use of this website.</p>
                </div>

                <div class="legal-section">
                    <h3>Governing Law</h3>
                    <p>These terms and conditions are governed by and construed in accordance with the laws of Nigeria. Any disputes relating to these terms will be subject to the exclusive jurisdiction of the courts of Nigeria.</p>
                </div>

                <div class="legal-section">
                    <h3>Related Documents</h3>
                    <ul>
                        <li><a href="{{ route('terms') }}">Terms of Use</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
.legal-content {
    max-width: 900px;
    margin: 0 auto;
}

.legal-content h2 {
    color: #333;
    margin-bottom: 30px;
    font-size: 28px;
}

.legal-section {
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid #e0e0e0;
}

.legal-section:last-child {
    border-bottom: none;
}

.legal-section h3 {
    color: #667eea;
    margin-bottom: 15px;
    font-size: 20px;
}

.legal-section p {
    color: #666;
    line-height: 1.8;
    margin-bottom: 10px;
}

.legal-section ul {
    color: #666;
    line-height: 1.8;
    margin: 15px 0;
    padding-left: 25px;
}

.legal-section a {
    color: #667eea;
    text-decoration: none;
}

.legal-section a:hover {
    text-decoration: underline;
}

.page-body {
    max-width: 900px;
    margin: 0 auto;
    line-height: 1.8;
    color: #333;
}
</style>
@endsection

