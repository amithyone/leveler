@extends('layouts.frontend')

@section('title', 'Register For Course - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'Register For Course' }}</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-intro" style="margin-bottom: 30px;">
                {!! nl2br(e($page->content)) !!}
            </div>
        @endif

        <div class="registration-info" style="max-width: 800px; margin: 0 auto;">
            <div class="info-card" style="background: #f8f9fa; padding: 25px; border-radius: 12px; margin-bottom: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Course Packages</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #e0e0e0;">
                        <h4 style="color: #667eea; margin-bottom: 10px;">Single Course</h4>
                        <div style="font-size: 24px; font-weight: 700; color: #333; margin-bottom: 10px;">₦10,000</div>
                        <p style="color: #666; font-size: 14px;">Access to 1 course of your choice</p>
                    </div>
                    <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #667eea;">
                        <h4 style="color: #667eea; margin-bottom: 10px;">4 Courses Package</h4>
                        <div style="font-size: 24px; font-weight: 700; color: #333; margin-bottom: 10px;">₦22,500</div>
                        <p style="color: #666; font-size: 14px;">Access to 4 courses (Registration Fee)</p>
                    </div>
                </div>
            </div>

            <div class="info-card" style="background: #f8f9fa; padding: 25px; border-radius: 12px; margin-bottom: 30px;">
                <h3 style="color: #333; margin-bottom: 15px;">Available Courses</h3>
                @if($courses->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                    @foreach($courses as $course)
                    <div style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e0e0e0;">
                        <strong style="color: #667eea;">{{ $course->code }}</strong>
                        <p style="font-size: 13px; color: #666; margin: 5px 0 0 0;">{{ $course->title }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p style="color: #666;">No courses available at the moment.</p>
                @endif
            </div>

            <div class="cta-section" style="text-align: center; padding: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white;">
                <h3 style="margin-bottom: 15px;">Ready to Get Started?</h3>
                <p style="margin-bottom: 25px; opacity: 0.9;">Login to your trainee account to register and make payment</p>
                <a href="{{ route('trainee.login') }}" class="btn" style="background: white; color: #667eea; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">Trainee Login</a>
            </div>

            <div class="payment-info" style="margin-top: 30px; padding: 25px; background: #fff3cd; border-radius: 12px; border-left: 4px solid #f59e0b;">
                <h4 style="color: #333; margin-bottom: 10px;"><i class="fas fa-info-circle"></i> Payment Information</h4>
                <ul style="color: #666; line-height: 1.8; margin: 0; padding-left: 20px;">
                    <li>We accept both full payment and installment plans</li>
                    <li>Certificates are only available upon full payment completion</li>
                    <li>You can track your payment progress in your dashboard</li>
                    <li>Payments are processed securely through PayVibe</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<style>
.page-intro {
    max-width: 900px;
    margin: 0 auto 30px;
    line-height: 1.8;
    color: #333;
    font-size: 16px;
}
</style>
@endsection

