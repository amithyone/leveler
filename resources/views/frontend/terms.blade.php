@extends('layouts.frontend')

@section('title', 'Terms of Use - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'Terms of Use' }}</h1>
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
                <h2>Terms and Conditions</h2>
                <p><strong>Last Updated:</strong> {{ date('F d, Y') }}</p>

                <div class="legal-section">
                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing and using the Leveler website and e-learning platform, you accept and agree to be bound by the terms and provision of this agreement.</p>
                </div>

                <div class="legal-section">
                    <h3>2. Use License</h3>
                    <p>Permission is granted to temporarily access the materials on Leveler's website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
                    <ul>
                        <li>Modify or copy the materials</li>
                        <li>Use the materials for any commercial purpose or for any public display</li>
                        <li>Attempt to decompile or reverse engineer any software contained on the website</li>
                        <li>Remove any copyright or other proprietary notations from the materials</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h3>3. Course Registration and Payment</h3>
                    <p>By registering for a course, you agree to:</p>
                    <ul>
                        <li>Provide accurate and complete information</li>
                        <li>Make payment as specified for your chosen course package</li>
                        <li>Complete course assessments honestly and independently</li>
                        <li>Respect intellectual property rights of course materials</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h3>4. Refund Policy</h3>
                    <p>Refund requests must be submitted within 7 days of payment. Refunds are subject to review and approval based on course access and completion status.</p>
                </div>

                <div class="legal-section">
                    <h3>5. Certificate Issuance</h3>
                    <p>Certificates are issued only upon:</p>
                    <ul>
                        <li>Successful completion of course assessments</li>
                        <li>Full payment completion (no outstanding balance)</li>
                        <li>Meeting minimum passing requirements</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h3>6. Limitation of Liability</h3>
                    <p>Leveler shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the service.</p>
                </div>

                <div class="legal-section">
                    <h3>7. Contact Information</h3>
                    <p>For questions about these Terms of Use, please contact us at <a href="mailto:info@leveler.com">info@leveler.com</a>.</p>
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

