@extends('layouts.trainee')

@section('title', $course->title)

@section('content')
<div class="course-detail-header">
    <div class="breadcrumb">
        <a href="{{ route('trainee.courses.index') }}"><i class="fas fa-arrow-left"></i> Back to Courses</a>
    </div>
    <h1>{{ $course->title }}</h1>
    <p class="course-code">Course Code: {{ $course->code }}</p>
</div>

<div class="course-detail-content">
    <div class="course-info-card">
        <div class="info-section">
            <h3><i class="fas fa-info-circle"></i> Course Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>Duration</strong>
                        <p>{{ $course->duration_hours }} Hours</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-question-circle"></i>
                    <div>
                        <strong>Questions</strong>
                        <p>{{ $course->questionPools->count() }} Questions</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-percentage"></i>
                    <div>
                        <strong>Passing Score</strong>
                        <p>{{ $course->passing_score ?? 70 }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3><i class="fas fa-align-left"></i> Description</h3>
            <p>{{ $course->description }}</p>
        </div>
    </div>

    @if($hasPassed)
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="success-content">
            <h3>Congratulations! You've Passed This Course</h3>
            <p>You scored {{ number_format($latestResult->percentage, 1) }}% on {{ $latestResult->completed_at->format('F d, Y') }}</p>
            <div class="success-actions">
                <a href="{{ route('trainee.certificates.view', $latestResult->id) }}" class="btn btn-primary">
                    <i class="fas fa-certificate"></i> View Certificate
                </a>
                <a href="{{ route('trainee.assessment.result', $latestResult->id) }}" class="btn btn-outline">
                    <i class="fas fa-chart-bar"></i> View Result Details
                </a>
            </div>
        </div>
    </div>
    @elseif($hasTaken)
    <div class="info-card">
        <div class="info-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="info-content">
            <h3>Previous Attempt</h3>
            <p>You scored {{ number_format($latestResult->percentage, 1) }}% on {{ $latestResult->completed_at->format('F d, Y') }}</p>
            <p class="note">You need {{ $course->passing_score ?? 70 }}% to pass. You can retake the assessment to improve your score.</p>
            <div class="info-actions">
                <a href="{{ route('trainee.assessment.start', $course->id) }}" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Retake Assessment
                </a>
                <a href="{{ route('trainee.assessment.result', $latestResult->id) }}" class="btn btn-outline">
                    <i class="fas fa-eye"></i> View Previous Result
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-play-circle"></i>
        </div>
        <div class="action-content">
            <h3>Ready to Start?</h3>
            <p>This assessment contains {{ $course->questionPools->count() }} questions. You need to score at least {{ $course->passing_score ?? 70 }}% to pass and earn your certificate.</p>
            <ul class="assessment-rules">
                <li><i class="fas fa-check"></i> Read each question carefully</li>
                <li><i class="fas fa-check"></i> Select the best answer</li>
                <li><i class="fas fa-check"></i> Review your answers before submitting</li>
                <li><i class="fas fa-check"></i> You can retake if you don't pass</li>
            </ul>
            <a href="{{ route('trainee.assessment.start', $course->id) }}" class="btn btn-primary btn-lg">
                <i class="fas fa-play"></i> Start Assessment
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

