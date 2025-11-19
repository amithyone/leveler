@extends('layouts.frontend')

@section('title', 'FAQs - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'Frequently Asked Questions' }}</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-body">
                {!! nl2br(e($page->content)) !!}
            </div>
        @else
            <div class="faqs-list">
                <div class="faq-item">
                    <h3>What courses are available?</h3>
                    <p>We offer a wide range of professional development courses including Business Communication & Diplomacy, Project Management, Human Resource Management, Digital Marketing, and many more. Visit our <a href="{{ route('courses') }}">Courses</a> page to see the full list.</p>
                </div>

                <div class="faq-item">
                    <h3>How do I register for a course?</h3>
                    <p>You can register for a course by clicking on the "Register For Course" link in the footer or navigation menu. You'll need to provide your personal information and select your preferred course package.</p>
                </div>

                <div class="faq-item">
                    <h3>What are the payment options?</h3>
                    <p>We offer flexible payment options including full payment or installment plans. You can pay ₦22,500 for access to 4 courses (registration fee) or ₦10,000 for a single course.</p>
                </div>

                <div class="faq-item">
                    <h3>How do I access my courses after payment?</h3>
                    <p>Once your payment is confirmed, you'll receive login credentials and can access your courses through the Trainee Login portal. Course access is granted automatically upon payment confirmation.</p>
                </div>

                <div class="faq-item">
                    <h3>When will I receive my certificate?</h3>
                    <p>Certificates are available for download once you have completed the course assessment and your payment is fully completed. You must pass the assessment and have no outstanding balance.</p>
                </div>

                <div class="faq-item">
                    <h3>Can I take courses in installments?</h3>
                    <p>Yes, we support installment payments. However, certificates will only be available once your payment is fully completed. You can track your payment progress in your trainee dashboard.</p>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
.faqs-list {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    transition: all 0.3s;
}

.faq-item:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.faq-item h3 {
    color: #333;
    margin-bottom: 15px;
    font-size: 18px;
    font-weight: 700;
}

.faq-item p {
    color: #666;
    line-height: 1.6;
}

.faq-item a {
    color: #667eea;
    text-decoration: none;
}

.faq-item a:hover {
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

