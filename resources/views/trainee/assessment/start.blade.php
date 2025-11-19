@extends('layouts.trainee')

@section('title', 'Assessment - ' . $course->title)

@section('content')
<div class="assessment-header">
    <div class="breadcrumb">
        <a href="{{ route('trainee.courses.show', $course->id) }}"><i class="fas fa-arrow-left"></i> Back to Course</a>
    </div>
    <h1>{{ $course->title }} - Assessment</h1>
    <p class="assessment-info">
        <i class="fas fa-question-circle"></i> {{ $questions->count() }} Questions
        <span class="separator">|</span>
        <i class="fas fa-percentage"></i> Passing Score: 70%
    </p>
</div>

<form id="assessmentForm" method="POST" action="{{ route('trainee.assessment.submit', $course->id) }}">
    @csrf
    
    <div class="assessment-container">
        @foreach($questions as $index => $question)
        <div class="question-card" data-question="{{ $index + 1 }}">
            <div class="question-header">
                <span class="question-number">Question {{ $index + 1 }} of {{ $questions->count() }}</span>
                <span class="question-points">{{ $question->points ?? 1 }} Point(s)</span>
            </div>
            
            <div class="question-body">
                <h3 class="question-text">{{ $question->question }}</h3>
                
                <div class="options-list">
                    @if(is_array($question->options))
                        @foreach($question->options as $key => $option)
                        <label class="option-item">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" required>
                            <span class="option-text">{{ $option }}</span>
                        </label>
                        @endforeach
                    @else
                        <label class="option-item">
                            <input type="radio" name="answers[{{ $question->id }}]" value="A" required>
                            <span class="option-text">A</span>
                        </label>
                        <label class="option-item">
                            <input type="radio" name="answers[{{ $question->id }}]" value="B" required>
                            <span class="option-text">B</span>
                        </label>
                        <label class="option-item">
                            <input type="radio" name="answers[{{ $question->id }}]" value="C" required>
                            <span class="option-text">C</span>
                        </label>
                        <label class="option-item">
                            <input type="radio" name="answers[{{ $question->id }}]" value="D" required>
                            <span class="option-text">D</span>
                        </label>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="assessment-footer">
        <div class="footer-info">
            <p><i class="fas fa-info-circle"></i> Please review all answers before submitting</p>
        </div>
        <div class="footer-actions">
            <button type="button" class="btn btn-outline" onclick="if(confirm('Are you sure you want to cancel? Your progress will be lost.')) window.location.href='{{ route('trainee.courses.show', $course->id) }}'">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Submit Assessment
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('assessmentForm').addEventListener('submit', function(e) {
    const allAnswered = document.querySelectorAll('input[type="radio"]:checked').length === {{ $questions->count() }};
    if (!allAnswered) {
        e.preventDefault();
        alert('Please answer all questions before submitting.');
        return false;
    }
    
    if (!confirm('Are you sure you want to submit your assessment? You cannot change your answers after submission.')) {
        e.preventDefault();
        return false;
    }
    
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
});
</script>
@endpush
@endsection

