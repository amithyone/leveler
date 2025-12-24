@extends('layouts.frontend')

@section('title', $course->title . ' - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb" style="margin-bottom: 15px;">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('courses') }}">Courses</a>
            <span>/</span>
            <span>{{ $course->title }}</span>
        </nav>
        <h1>{{ $course->title }}</h1>
        <div class="course-header-meta" style="display: flex; gap: 20px; margin-top: 15px; flex-wrap: wrap;">
            <span class="course-code-badge">{{ $course->code }}</span>
            @if($course->level)
            <span class="course-level-badge">{{ $course->level }}</span>
            @endif
            @if($course->duration_hours)
            <span><i class="fas fa-clock"></i> {{ $course->duration_hours }} hours</span>
            @endif
            @if($course->instructor)
            <span><i class="fas fa-chalkboard-teacher"></i> {{ $course->instructor }}</span>
            @endif
            @if($course->rating > 0)
            <span>
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= $course->rating ? 'filled' : '' }}"></i>
                @endfor
                ({{ number_format($course->rating, 1) }}) - {{ $course->total_reviews }} reviews
            </span>
            @endif
        </div>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="course-details-layout">
            <div class="course-main-content">
                @if($course->image)
                <div class="course-hero-image">
                    <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}">
                </div>
                @endif

                @if($course->overview)
                <div class="content-section">
                    <h2><i class="fas fa-info-circle"></i> Course Overview</h2>
                    <div class="course-overview-content">
                        {!! nl2br(e($course->overview)) !!}
                    </div>
                </div>
                @endif

                @if($course->objectives && count($course->objectives) > 0)
                <div class="content-section">
                    <h2><i class="fas fa-bullseye"></i> Learning Objectives</h2>
                    <ul class="detail-list">
                        @foreach($course->objectives as $objective)
                            @if(!empty($objective))
                                <li>{{ $objective }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($course->what_you_will_learn && count($course->what_you_will_learn) > 0)
                <div class="content-section">
                    <h2><i class="fas fa-check-circle"></i> What You Will Learn</h2>
                    <ul class="detail-list">
                        @foreach($course->what_you_will_learn as $item)
                            @if(!empty($item))
                                <li>{{ $item }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($course->curriculum && count($course->curriculum) > 0)
                <div class="content-section">
                    <h2><i class="fas fa-book"></i> Course Curriculum</h2>
                    <div class="curriculum-list">
                        @foreach($course->curriculum as $index => $module)
                            @if(!empty($module['title']))
                                <div class="curriculum-module">
                                    <div class="module-header">
                                        <span class="module-number">Module {{ $index + 1 }}</span>
                                        <h3>{{ $module['title'] }}</h3>
                                    </div>
                                    @if(!empty($module['description']))
                                        <p class="module-description">{{ $module['description'] }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if($course->requirements && count($course->requirements) > 0)
                <div class="content-section">
                    <h2><i class="fas fa-list-check"></i> Requirements</h2>
                    <ul class="detail-list">
                        @foreach($course->requirements as $requirement)
                            @if(!empty($requirement))
                                <li>{{ $requirement }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($course->who_is_this_for)
                <div class="content-section">
                    <h2><i class="fas fa-users"></i> Who Is This For?</h2>
                    <div class="course-overview-content">
                        {!! nl2br(e($course->who_is_this_for)) !!}
                    </div>
                </div>
                @endif
            </div>

            <div class="course-sidebar">
                <div class="sidebar-card">
                    <h3>Course Information</h3>
                    <div class="info-list">
                        <div class="info-item">
                            <strong>Course Code:</strong>
                            <span>{{ $course->code }}</span>
                        </div>
                        @if($course->duration_hours)
                        <div class="info-item">
                            <strong>Duration:</strong>
                            <span>{{ $course->duration_hours }} hours</span>
                        </div>
                        @endif
                        @if($course->level)
                        <div class="info-item">
                            <strong>Level:</strong>
                            <span>{{ $course->level }}</span>
                        </div>
                        @endif
                        @if($course->language)
                        <div class="info-item">
                            <strong>Language:</strong>
                            <span>{{ $course->language }}</span>
                        </div>
                        @endif
                        @if($course->instructor)
                        <div class="info-item">
                            <strong>Instructor:</strong>
                            <span>{{ $course->instructor }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <strong>Questions:</strong>
                            <span>{{ $course->question_pools_count ?? $course->questionPools()->count() }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Status:</strong>
                            <span class="status-badge {{ $course->status === 'Active' ? 'active' : 'inactive' }}">
                                {{ $course->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-block" style="margin-bottom: 15px;">
                        <i class="fas fa-user-plus"></i> Register Now
                    </a>
                    <a href="{{ route('courses') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Courses
                    </a>
                </div>

                @if($course->rating > 0)
                <div class="sidebar-card">
                    <h3>Course Rating</h3>
                    <div class="rating-display-large">
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $course->rating ? 'filled' : '' }}"></i>
                            @endfor
                        </div>
                        <div class="rating-value">{{ number_format($course->rating, 1) }}</div>
                        <div class="rating-reviews">{{ $course->total_reviews }} reviews</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
.breadcrumb {
    font-size: 14px;
    color: #666;
}

.breadcrumb a {
    color: #667eea;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    margin: 0 8px;
    color: #999;
}

.course-header-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: center;
}

.course-code-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
}

.course-level-badge {
    background: #e0f2fe;
    color: #0369a1;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
}

.course-details-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 40px;
    margin-top: 30px;
}

.course-main-content {
    min-width: 0;
}

.course-hero-image {
    width: 100%;
    height: 400px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.course-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.content-section {
    margin-bottom: 40px;
}

.content-section h2 {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.content-section h2 i {
    color: #667eea;
}

.course-overview-content {
    line-height: 1.8;
    color: #555;
    font-size: 16px;
}

.detail-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.detail-list li {
    padding: 12px 15px;
    margin-bottom: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
    position: relative;
    padding-left: 35px;
}

.detail-list li:before {
    content: "\f00c";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 12px;
    color: #667eea;
}

.curriculum-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.curriculum-module {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s;
}

.curriculum-module:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #667eea;
}

.module-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.module-number {
    display: inline-block;
    background: #667eea;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.module-header h3 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.module-description {
    color: #666;
    margin: 0;
    line-height: 1.6;
}

.course-sidebar {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.sidebar-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
}

.sidebar-card h3 {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item strong {
    color: #666;
    font-weight: 600;
}

.info-item span {
    color: #333;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.inactive {
    background: #fee2e2;
    color: #991b1b;
}

.rating-display-large {
    text-align: center;
}

.rating-stars {
    font-size: 24px;
    margin-bottom: 10px;
}

.rating-stars .fa-star {
    color: #ddd;
    margin: 0 2px;
}

.rating-stars .fa-star.filled {
    color: #fbbf24;
}

.rating-value {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.rating-reviews {
    color: #666;
    font-size: 14px;
}

.btn-block {
    width: 100%;
    display: block;
    text-align: center;
}

@media (max-width: 968px) {
    .course-details-layout {
        grid-template-columns: 1fr;
    }
    
    .course-sidebar {
        position: static;
    }
}
</style>
@endsection

