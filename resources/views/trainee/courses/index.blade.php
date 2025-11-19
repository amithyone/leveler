@extends('layouts.trainee')

@section('title', 'My Courses')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-book"></i> Available Courses</h1>
    <p>Browse and enroll in courses to start your learning journey</p>
</div>

@if($courses->count() > 0)
<div class="courses-grid">
    @foreach($courses as $course)
    <div class="course-card">
        <div class="course-badge">
            @if(!$course->has_access)
                <span class="badge badge-danger"><i class="fas fa-lock"></i> Payment Required</span>
            @elseif($course->has_passed)
                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Passed</span>
            @elseif($course->has_taken)
                <span class="badge badge-warning"><i class="fas fa-redo"></i> Retake Available</span>
            @else
                <span class="badge badge-info"><i class="fas fa-play-circle"></i> Available</span>
            @endif
        </div>
        
        <div class="course-header">
            <h3>{{ $course->title }}</h3>
            <span class="course-code">{{ $course->code }}</span>
        </div>
        
        <p class="course-description">{{ $course->description }}</p>
        
        <div class="course-meta">
            <div class="meta-item">
                <i class="fas fa-clock"></i>
                <span>{{ $course->duration_hours }} Hours</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-question-circle"></i>
                <span>{{ $course->questionPools->count() }} Questions</span>
            </div>
        </div>

        @if($course->latest_result)
        <div class="course-result">
            <div class="result-info">
                <span>Last Score: <strong>{{ number_format($course->latest_result->percentage, 1) }}%</strong></span>
                <span class="result-date">{{ $course->latest_result->completed_at->format('M d, Y') }}</span>
            </div>
        </div>
        @endif

        <div class="course-actions">
            @if(!$course->has_access)
                <a href="{{ route('trainee.payments.create') }}" class="btn btn-primary btn-block">
                    <i class="fas fa-lock"></i> Get Access
                </a>
            @else
                <a href="{{ route('trainee.courses.show', $course->id) }}" class="btn btn-primary btn-block">
                    @if($course->has_passed)
                        <i class="fas fa-eye"></i> View Result
                    @elseif($course->has_taken)
                        <i class="fas fa-redo"></i> Retake Assessment
                    @else
                        <i class="fas fa-play"></i> Start Course
                    @endif
                </a>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="empty-state">
    <i class="fas fa-book-open"></i>
    <h3>No Courses Available</h3>
    <p>There are no courses available at the moment. Please check back later.</p>
</div>
@endif
@endsection

