@extends('layouts.frontend')

@section('title', 'Courses - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <h1>{{ $page->title ?? 'Our Courses' }}</h1>
        <p style="margin-top: 10px; color: #ffffff;">Professional development courses designed to enhance your skills and career</p>
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
        <div class="courses-grid">
            @foreach($courses as $course)
            <div class="course-card" onclick="window.location='{{ route('course.details', $course->id) }}'">
                @if($course->image)
                <div class="course-image">
                    <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}">
                </div>
                @endif
                <div class="course-header">
                    <span class="course-code">{{ $course->code }}</span>
                    <div class="course-badges">
                        @if($course->level)
                        <span class="course-level">{{ $course->level }}</span>
                        @endif
                        <span class="course-status {{ $course->status === 'Active' ? 'active' : 'inactive' }}">
                            {{ $course->status }}
                        </span>
                    </div>
                </div>
                <h3 class="course-title">{{ $course->title }}</h3>
                @if($course->overview)
                <p class="course-overview">{{ \Illuminate\Support\Str::limit($course->overview, 150) }}</p>
                @elseif($course->description)
                <p class="course-description">{{ \Illuminate\Support\Str::limit($course->description, 150) }}</p>
                @endif
                <div class="course-meta">
                    @if($course->duration_hours)
                    <span><i class="fas fa-clock"></i> {{ $course->duration_hours }}h</span>
                    @endif
                    @if($course->instructor)
                    <span><i class="fas fa-chalkboard-teacher"></i> {{ $course->instructor }}</span>
                    @endif
                    <span><i class="fas fa-question-circle"></i> {{ $course->questionPools()->count() }} questions</span>
                    @if($course->rating > 0)
                    <span><i class="fas fa-star"></i> {{ number_format($course->rating, 1) }}</span>
                    @endif
                </div>
                @if($course->what_you_will_learn && count($course->what_you_will_learn) > 0)
                <div class="course-highlights">
                    <strong>You'll learn:</strong>
                    <ul>
                        @foreach(array_slice($course->what_you_will_learn, 0, 3) as $item)
                            @if(!empty($item))
                            <li>{{ $item }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="course-actions" onclick="event.stopPropagation();">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register Now</a>
                    <a href="{{ route('course.details', $course->id) }}" class="btn btn-secondary btn-sm">View Details</a>
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
    padding: 0;
    transition: all 0.3s;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.course-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.course-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.course-card > .course-header,
.course-card > .course-title,
.course-card > .course-overview,
.course-card > .course-description,
.course-card > .course-meta,
.course-card > .course-highlights,
.course-card > .course-actions {
    padding: 0 25px;
}

.course-card > .course-header {
    padding-top: 20px;
    padding-bottom: 15px;
}

.course-card > .course-title {
    padding-top: 0;
    padding-bottom: 10px;
}

.course-card > .course-overview,
.course-card > .course-description {
    padding-top: 0;
    padding-bottom: 15px;
    flex-grow: 1;
}

.course-card > .course-meta {
    padding-top: 0;
    padding-bottom: 15px;
}

.course-card > .course-highlights {
    padding-top: 0;
    padding-bottom: 15px;
    margin-top: auto;
}

.course-card > .course-actions {
    padding-top: 15px;
    padding-bottom: 25px;
    margin-top: auto;
}

.course-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.course-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.course-level {
    background: #e0f2fe;
    color: #0369a1;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
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
    flex-wrap: wrap;
    font-size: 13px;
    color: #666;
}

.course-overview {
    color: #555;
    font-size: 14px;
    line-height: 1.6;
}

.course-highlights {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 10px;
}

.course-highlights strong {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-size: 13px;
}

.course-highlights ul {
    margin: 0;
    padding-left: 20px;
    list-style: none;
}

.course-highlights li {
    color: #666;
    font-size: 12px;
    margin-bottom: 5px;
    position: relative;
    padding-left: 15px;
}

.course-highlights li:before {
    content: "\f00c";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    position: absolute;
    left: 0;
    color: #667eea;
    font-size: 10px;
}

.course-meta i {
    margin-right: 5px;
    color: #667eea;
}

.course-actions {
    display: flex;
    gap: 10px;
    border-top: 1px solid #e0e0e0;
}

.course-actions .btn {
    flex: 1;
    text-align: center;
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

