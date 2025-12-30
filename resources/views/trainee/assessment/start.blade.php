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
        <i class="fas fa-percentage"></i> Passing Score: {{ $course->passing_score ?? 70 }}%
    </p>
</div>

<form id="assessmentForm" method="POST" action="{{ route('trainee.assessment.submit', $course->id) }}" enctype="multipart/form-data">
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

    <!-- Optional File Upload Section -->
    <div class="file-upload-section">
        <div class="file-upload-card">
            <h3><i class="fas fa-paperclip"></i> Optional File Submission</h3>
            <p class="file-upload-note">You can optionally upload a file or provide a link to a file sharing service (e.g., transfernow.net)</p>
            
            <div class="file-upload-options">
                <div class="file-option">
                    <label for="assessment_file" class="file-upload-label">
                        <i class="fas fa-upload"></i>
                        <span>Upload File</span>
                    </label>
                    <input 
                        type="file" 
                        id="assessment_file" 
                        name="assessment_file" 
                        class="file-input"
                        accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip,.rar"
                    >
                    <small class="file-help">Accepted formats: PDF, DOC, DOCX, TXT, JPG, PNG, ZIP, RAR (Max: 10MB)</small>
                </div>
                
                <div class="file-divider">
                    <span>OR</span>
                </div>
                
                <div class="file-option">
                    <label for="file_link" class="file-link-label">
                        <i class="fas fa-link"></i>
                        <span>File Link</span>
                    </label>
                    <input 
                        type="url" 
                        id="file_link" 
                        name="file_link" 
                        class="form-control file-link-input"
                        placeholder="https://www.transfernow.net/..."
                    >
                    <small class="file-help">Paste the link to your file on transfernow.net or other file sharing service</small>
                </div>
            </div>
        </div>
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

