@extends('layouts.frontend')

@section('title', 'e-Learning - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'e-Learning Platform' }}</h1>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-body">
                {!! nl2br(e($page->content)) !!}
            </div>
        @else
            <div class="elearning-content">
                <h2>Welcome to Our e-Learning Platform</h2>
                <p>Access professional development courses from anywhere, at any time. Our comprehensive e-learning platform provides you with the tools and resources you need to advance your career.</p>

                <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin: 40px 0;">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3>Online Learning</h3>
                        <p>Access courses from any device, anywhere in the world. Learn at your own pace.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h3>Certification</h3>
                        <p>Earn recognized certificates upon successful completion of courses and assessments.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Track Progress</h3>
                        <p>Monitor your learning progress and assessment results in real-time.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Expert Instructors</h3>
                        <p>Learn from industry experts with years of practical experience.</p>
                    </div>
                </div>

                <div class="cta-section" style="text-align: center; margin-top: 50px; padding: 40px; background: #f8f9fa; border-radius: 12px;">
                    <h3>Ready to Start Learning?</h3>
                    <p style="margin: 20px 0;">Register for a course today and begin your professional development journey.</p>
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="{{ route('register') }}" class="btn btn-primary">Register Now</a>
                        <a href="{{ route('trainee.login') }}" class="btn btn-secondary">Trainee Login</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<style>
.elearning-content {
    max-width: 1000px;
    margin: 0 auto;
}

.elearning-content h2 {
    color: #333;
    margin-bottom: 20px;
    font-size: 28px;
}

.elearning-content > p {
    font-size: 18px;
    color: #666;
    line-height: 1.8;
    margin-bottom: 30px;
}

.feature-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    transition: all 0.3s;
}

.feature-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
    font-size: 24px;
}

.feature-card h3 {
    color: #333;
    margin-bottom: 10px;
    font-size: 18px;
}

.feature-card p {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
}

.page-body {
    max-width: 900px;
    margin: 0 auto;
    line-height: 1.8;
    color: #333;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}
</style>
@endsection

