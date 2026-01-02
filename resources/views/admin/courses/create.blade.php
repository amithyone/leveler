@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="{{ route('admin.courses.view') }}" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Add New Course
            </h1>
            <p style="margin: 0; color: #666;">Create a new course for the training platform</p>
        </div>
    </div>
</div>

<div class="page-content">
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="content-section">
        <form method="POST" action="{{ route('admin.courses.store') }}" class="course-form" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="code">
                    <i class="fas fa-code"></i> Course Code <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    class="form-control @error('code') error @enderror"
                    value="{{ old('code') }}"
                    required
                    maxlength="10"
                    placeholder="e.g., BCD, CAO, CSR"
                >
                @error('code')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small class="form-help">Unique course identifier (max 10 characters)</small>
            </div>

            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading"></i> Course Title <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control @error('title') error @enderror"
                    value="{{ old('title') }}"
                    required
                    maxlength="255"
                    placeholder="Enter course title"
                >
                @error('title')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i> Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control @error('description') error @enderror"
                    rows="5"
                    placeholder="Enter course description"
                >{{ old('description') }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small class="form-help">Brief description of the course content and objectives</small>
            </div>

            <div class="form-group">
                <label for="image">
                    <i class="fas fa-image"></i> Course Image
                </label>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    class="form-control @error('image') error @enderror"
                    accept="image/*"
                    onchange="previewImage(this)"
                >
                @error('image')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small class="form-help">Upload a course image (JPEG, PNG, GIF, WebP - Max: 5MB)</small>
                <div id="image-preview" style="margin-top: 15px; display: none;">
                    <img id="preview-img" src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="assessment_questions_count">
                        <i class="fas fa-question-circle"></i> Assessment Questions Count
                    </label>
                    <input 
                        type="number" 
                        id="assessment_questions_count" 
                        name="assessment_questions_count" 
                        class="form-control @error('assessment_questions_count') error @enderror"
                        value="{{ old('assessment_questions_count') }}"
                        min="1"
                        placeholder="e.g., 50"
                    >
                    @error('assessment_questions_count')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Number of questions to randomly select from pool for assessment (leave empty to use all questions)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="passing_score">
                        <i class="fas fa-trophy"></i> Passing Score (%) <span class="required">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="passing_score" 
                        name="passing_score" 
                        class="form-control @error('passing_score') error @enderror"
                        value="{{ old('passing_score', 70) }}"
                        min="0"
                        max="100"
                        required
                        placeholder="e.g., 70"
                    >
                    @error('passing_score')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Minimum percentage score required to pass the assessment (0-100)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="training_link">
                        <i class="fas fa-link"></i> Training Link
                    </label>
                    <input 
                        type="url" 
                        id="training_link" 
                        name="training_link" 
                        class="form-control @error('training_link') error @enderror"
                        value="{{ old('training_link') }}"
                        placeholder="https://example.com/training"
                    >
                    @error('training_link')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Training link visible only to trainees who have paid for this course</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="whatsapp_link">
                        <i class="fab fa-whatsapp"></i> WhatsApp Group Link
                    </label>
                    <input 
                        type="url" 
                        id="whatsapp_link" 
                        name="whatsapp_link" 
                        class="form-control @error('whatsapp_link') error @enderror"
                        value="{{ old('whatsapp_link') }}"
                        placeholder="https://chat.whatsapp.com/..."
                    >
                    @error('whatsapp_link')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">WhatsApp group link visible only to trainees who have paid for this course</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">
                        <i class="fas fa-toggle-on"></i> Status <span class="required">*</span>
                    </label>
                    <select 
                        id="status" 
                        name="status" 
                        class="form-control @error('status') error @enderror"
                        required
                    >
                        <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Active courses are available for enrollment</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Course
                </button>
                <a href="{{ route('admin.courses.view') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.course-form {
    max-width: 800px;
}

.form-group {
    margin-bottom: 25px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    font-size: 14px;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control.error {
    border-color: #ef4444;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.error-message {
    color: #ef4444;
    font-size: 13px;
    margin-top: 5px;
    display: block;
}

.form-help {
    color: #666;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection
