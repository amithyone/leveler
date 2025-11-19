@extends('layouts.trainee')

@section('title', 'Assessment Result')

@section('content')
<div class="result-header">
    <h1>Assessment Result</h1>
    <p>{{ $result->course->title }}</p>
</div>

<div class="result-container">
    <div class="result-card {{ $result->status === 'passed' ? 'result-passed' : 'result-failed' }}">
        <div class="result-icon">
            @if($result->status === 'passed')
                <i class="fas fa-check-circle"></i>
            @else
                <i class="fas fa-times-circle"></i>
            @endif
        </div>
        
        <div class="result-title">
            @if($result->status === 'passed')
                <h2>Congratulations! You Passed!</h2>
                <p>You have successfully completed the assessment</p>
            @else
                <h2>Assessment Not Passed</h2>
                <p>Don't worry, you can retake the assessment</p>
            @endif
        </div>

        <div class="result-score">
            <div class="score-circle {{ $result->status === 'passed' ? 'score-passed' : 'score-failed' }}">
                <div class="score-value">{{ number_format($result->percentage, 1) }}%</div>
                <div class="score-label">Score</div>
            </div>
        </div>

        <div class="result-details">
            <div class="detail-item">
                <span class="detail-label">Correct Answers</span>
                <span class="detail-value">{{ $result->score }} / {{ $result->total_questions }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Percentage</span>
                <span class="detail-value">{{ number_format($result->percentage, 2) }}%</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Passing Score</span>
                <span class="detail-value">70%</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Date Completed</span>
                <span class="detail-value">{{ $result->completed_at->format('F d, Y h:i A') }}</span>
            </div>
        </div>

        <div class="result-actions">
            @if($result->status === 'passed')
                <a href="{{ route('trainee.certificates.view', $result->id) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-certificate"></i> View Certificate
                </a>
                <a href="{{ route('trainee.certificates.download', $result->id) }}" class="btn btn-outline btn-lg">
                    <i class="fas fa-download"></i> Download Certificate
                </a>
            @else
                <a href="{{ route('trainee.courses.show', $result->course_id) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-redo"></i> Retake Assessment
                </a>
            @endif
            <a href="{{ route('trainee.dashboard') }}" class="btn btn-outline btn-lg">
                <i class="fas fa-home"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

