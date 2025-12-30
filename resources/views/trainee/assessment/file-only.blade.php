@extends('layouts.trainee')

@section('title', 'File Submission - ' . $course->title)

@section('content')
<div class="assessment-header">
    <div class="breadcrumb">
        <a href="{{ route('trainee.courses.show', $course->id) }}"><i class="fas fa-arrow-left"></i> Back to Course</a>
    </div>
    <h1>{{ $course->title }} - File Submission</h1>
    <p class="assessment-info">
        <i class="fas fa-info-circle"></i> This course requires file submission only
    </p>
</div>

<form id="fileSubmissionForm" method="POST" action="{{ route('trainee.assessment.submit', $course->id) }}" enctype="multipart/form-data">
    @csrf
    
    <div class="file-upload-section">
        <div class="file-upload-card">
            <h3><i class="fas fa-paperclip"></i> Submit Your Work</h3>
            <p class="file-upload-note">Please upload your completed work file or provide a link to a file sharing service (e.g., transfernow.net)</p>
            
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
            <p><i class="fas fa-info-circle"></i> Please ensure your file is ready before submitting</p>
        </div>
        <div class="footer-actions">
            <button type="button" class="btn btn-outline" onclick="if(confirm('Are you sure you want to cancel? Your progress will be lost.')) window.location.href='{{ route('trainee.courses.show', $course->id) }}'">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Submit File
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('fileSubmissionForm').addEventListener('submit', function(e) {
    const hasFile = document.getElementById('assessment_file').files.length > 0;
    const hasLink = document.getElementById('file_link').value.trim() !== '';
    
    if (!hasFile && !hasLink) {
        e.preventDefault();
        alert('Please upload a file or provide a file link before submitting.');
        return false;
    }
    
    if (!confirm('Are you sure you want to submit your file? You cannot change it after submission.')) {
        e.preventDefault();
        return false;
    }
    
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
});
</script>
@endpush
@endsection

