@extends('layouts.frontend')

@section('title', 'Privacy Policy - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'Privacy Policy' }}</h1>
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
                <h2>Privacy Policy</h2>
                <p><strong>Last Updated:</strong> {{ date('F d, Y') }}</p>

                <div class="legal-section">
                    <h3>1. Information We Collect</h3>
                    <p>We collect information that you provide directly to us, including:</p>
                    <ul>
                        <li>Personal information (name, email, phone number) when you register</li>
                        <li>Payment information processed through secure payment gateways</li>
                        <li>Course progress and assessment results</li>
                        <li>Communication records when you contact us</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h3>2. How We Use Your Information</h3>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Provide, maintain, and improve our services</li>
                        <li>Process your course registrations and payments</li>
                        <li>Send you course-related communications</li>
                        <li>Generate certificates upon course completion</li>
                        <li>Respond to your inquiries and provide customer support</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h3>3. Information Sharing</h3>
                    <p>We do not sell, trade, or rent your personal information to third parties. We may share your information only:</p>
                    <ul>
                        <li>With payment processors to complete transactions</li>
                        <li>When required by law or to protect our rights</li>
                        <li>With your explicit consent</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h3>4. Data Security</h3>
                    <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                </div>

                <div class="legal-section">
                    <h3>5. Your Rights</h3>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access your personal information</li>
                        <li>Correct inaccurate information</li>
                        <li>Request deletion of your information</li>
                        <li>Opt-out of marketing communications</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h3>6. Cookies</h3>
                    <p>We use cookies to enhance your experience, analyze site usage, and assist in our marketing efforts. You can control cookies through your browser settings.</p>
                </div>

                <div class="legal-section">
                    <h3>7. Contact Us</h3>
                    <p>If you have questions about this Privacy Policy, please contact us at <a href="mailto:privacy@leveler.com">privacy@leveler.com</a>.</p>
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
    margin-bottom: 10px;
    font-size: 28px;
}

.legal-content > p {
    color: #666;
    margin-bottom: 30px;
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

