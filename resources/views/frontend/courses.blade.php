@extends('layouts.frontend')

@section('title', 'Courses - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'Our Courses' }}</h1>
        <p style="margin-top: 10px; color: #666;">Professional development courses designed to enhance your skills and career</p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        @if($page && $page->content)
            <div class="page-intro" style="margin-bottom: 40px;">
                {!! nl2br(e($page->content)) !!}
            </div>
        @endif

        @if($courses->count() > 0)
        <div class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
            @foreach($courses as $course)
            <div class="course-card">
                <div class="course-header">
                    <span class="course-code">{{ $course->code }}</span>
                    <span class="course-status {{ $course->status === 'Active' ? 'active' : 'inactive' }}">
                        {{ $course->status }}
                    </span>
                </div>
                <h3 class="course-title">{{ $course->title }}</h3>
                @if($course->description)
                <p class="course-description">{{ \Illuminate\Support\Str::limit($course->description, 120) }}</p>
                @endif
                <div class="course-meta">
                    @if($course->duration_hours)
                    <span><i class="fas fa-clock"></i> {{ $course->duration_hours }} hours</span>
                    @endif
                    <span><i class="fas fa-question-circle"></i> {{ $course->questionPools()->count() }} questions</span>
                </div>
                <div class="course-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register Now</a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-book-open" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3>No Courses Available</h3>
            <p>Please check back later for available courses.</p>
        </div>
        @endif
    </div>
</section>

<style>
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.course-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 25px;
    transition: all 0.3s;
}

.course-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.course-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.course-code {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
}

.course-status {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.course-status.active {
    background: #d1fae5;
    color: #065f46;
}

.course-status.inactive {
    background: #fee2e2;
    color: #991b1b;
}

.course-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.4;
}

.course-description {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 15px;
}

.course-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    font-size: 13px;
    color: #666;
}

.course-meta i {
    margin-right: 5px;
    color: #667eea;
}

.course-actions {
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 14px;
}

.page-intro {
    max-width: 900px;
    margin: 0 auto 40px;
    line-height: 1.8;
    color: #333;
    font-size: 16px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state h3 {
    color: #333;
    margin-bottom: 10px;
}

.empty-state p {
    color: #666;
}
</style>
@endsection

