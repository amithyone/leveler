@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="{{ route('admin.courses.view') }}" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Edit Course
            </h1>
            <p style="margin: 0; color: #666;">Update course information</p>
        </div>
        <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-secondary">
            <i class="fas fa-eye"></i> View Details
        </a>
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
        <form method="POST" action="{{ route('admin.courses.update', $course->id) }}" class="course-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="code">
                    <i class="fas fa-code"></i> Course Code <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    class="form-control @error('code') error @enderror"
                    value="{{ old('code', $course->code) }}"
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
                    value="{{ old('title', $course->title) }}"
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
                >{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small class="form-help">Brief description of the course content and objectives</small>
            </div>

            <div class="form-group">
                <label for="overview">
                    <i class="fas fa-info-circle"></i> Course Overview
                </label>
                <textarea 
                    id="overview" 
                    name="overview" 
                    class="form-control @error('overview') error @enderror"
                    rows="6"
                    placeholder="Provide a comprehensive overview of the course..."
                >{{ old('overview', $course->overview) }}</textarea>
                @error('overview')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small class="form-help">Detailed overview of what the course covers</small>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-bullseye"></i> Learning Objectives
                </label>
                <div id="objectives-container">
                    @php
                        $objectives = old('objectives', $course->objectives ?? []);
                        if (empty($objectives)) $objectives = [''];
                    @endphp
                    @foreach($objectives as $index => $objective)
                        <div class="array-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input 
                                type="text" 
                                name="objectives[]" 
                                class="form-control" 
                                value="{{ $objective }}"
                                placeholder="Enter learning objective"
                            >
                            @if($index > 0)
                                <button type="button" class="btn btn-danger remove-item" onclick="removeArrayItem(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addArrayItem('objectives-container', 'objectives')">
                    <i class="fas fa-plus"></i> Add Objective
                </button>
                <small class="form-help">List the key learning objectives for this course</small>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-check-circle"></i> What You Will Learn
                </label>
                <div id="learn-container">
                    @php
                        $learn = old('what_you_will_learn', $course->what_you_will_learn ?? []);
                        if (empty($learn)) $learn = [''];
                    @endphp
                    @foreach($learn as $index => $item)
                        <div class="array-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input 
                                type="text" 
                                name="what_you_will_learn[]" 
                                class="form-control" 
                                value="{{ $item }}"
                                placeholder="Enter what students will learn"
                            >
                            @if($index > 0)
                                <button type="button" class="btn btn-danger remove-item" onclick="removeArrayItem(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addArrayItem('learn-container', 'what_you_will_learn')">
                    <i class="fas fa-plus"></i> Add Item
                </button>
                <small class="form-help">List what students will learn from this course</small>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-list-check"></i> Requirements
                </label>
                <div id="requirements-container">
                    @php
                        $requirements = old('requirements', $course->requirements ?? []);
                        if (empty($requirements)) $requirements = [''];
                    @endphp
                    @foreach($requirements as $index => $requirement)
                        <div class="array-input-group" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input 
                                type="text" 
                                name="requirements[]" 
                                class="form-control" 
                                value="{{ $requirement }}"
                                placeholder="Enter requirement"
                            >
                            @if($index > 0)
                                <button type="button" class="btn btn-danger remove-item" onclick="removeArrayItem(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addArrayItem('requirements-container', 'requirements')">
                    <i class="fas fa-plus"></i> Add Requirement
                </button>
                <small class="form-help">List prerequisites or requirements for this course</small>
            </div>

            <div class="form-group">
                <label for="who_is_this_for">
                    <i class="fas fa-users"></i> Who Is This For?
                </label>
                <textarea 
                    id="who_is_this_for" 
                    name="who_is_this_for" 
                    class="form-control @error('who_is_this_for') error @enderror"
                    rows="3"
                    placeholder="Describe who this course is designed for..."
                >{{ old('who_is_this_for', $course->who_is_this_for) }}</textarea>
                @error('who_is_this_for')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small class="form-help">Target audience for this course</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="level">
                        <i class="fas fa-layer-group"></i> Level
                    </label>
                    <select 
                        id="level" 
                        name="level" 
                        class="form-control @error('level') error @enderror"
                    >
                        <option value="">Select Level</option>
                        <option value="Beginner" {{ old('level', $course->level) === 'Beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="Intermediate" {{ old('level', $course->level) === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="Advanced" {{ old('level', $course->level) === 'Advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('level')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="language">
                        <i class="fas fa-language"></i> Language
                    </label>
                    <input 
                        type="text" 
                        id="language" 
                        name="language" 
                        class="form-control @error('language') error @enderror"
                        value="{{ old('language', $course->language ?? 'English') }}"
                        placeholder="e.g., English"
                    >
                    @error('language')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="instructor">
                    <i class="fas fa-chalkboard-teacher"></i> Instructor
                </label>
                <input 
                    type="text" 
                    id="instructor" 
                    name="instructor" 
                    class="form-control @error('instructor') error @enderror"
                    value="{{ old('instructor', $course->instructor) }}"
                    placeholder="Enter instructor name"
                >
                @error('instructor')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">
                    <i class="fas fa-image"></i> Course Image
                </label>
                @if($course->image)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ Storage::url($course->image) }}" alt="Course Image" style="max-width: 200px; border-radius: 8px;">
                    </div>
                @endif
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    class="form-control @error('image') error @enderror"
                    accept="image/*"
                >
                @error('image')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <small class="form-help">Upload a course image (max 2MB, JPEG/PNG/JPG/GIF)</small>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-book"></i> Curriculum/Modules (Course Outline)
                </label>
                <div id="curriculum-container">
                    @php
                        $curriculum = old('curriculum', $course->curriculum ?? []);
                        if (empty($curriculum)) $curriculum = [['module_title' => '', 'lessons' => []]];
                    @endphp
                    @foreach($curriculum as $index => $module)
                        <div class="curriculum-item" style="border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9fafb;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <strong style="color: #667eea;">Module {{ $index + 1 }}</strong>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeCurriculumItem(this)">
                                    <i class="fas fa-times"></i> Remove Module
                                </button>
                            </div>
                            
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Module Title</label>
                                <input 
                                    type="text" 
                                    name="curriculum[{{ $index }}][module_title]" 
                                    class="form-control" 
                                    value="{{ $module['module_title'] ?? '' }}"
                                    placeholder="e.g., Module 1 – Project Management Framework"
                                    required
                                >
                            </div>
                            
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Lessons/Topics</label>
                                <div class="lessons-container" data-module-index="{{ $index }}">
                                    @php
                                        $lessons = $module['lessons'] ?? [];
                                        if (empty($lessons)) $lessons = [''];
                                    @endphp
                                    @foreach($lessons as $lessonIndex => $lesson)
                                        <div class="lesson-item" style="display: flex; gap: 10px; margin-bottom: 8px;">
                                            <input 
                                                type="text" 
                                                name="curriculum[{{ $index }}][lessons][]" 
                                                class="form-control" 
                                                value="{{ $lesson }}"
                                                placeholder="Enter lesson/topic name"
                                            >
                                            @if($lessonIndex > 0 || count($lessons) > 1)
                                                <button type="button" class="btn btn-danger btn-sm remove-lesson" onclick="removeLesson(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addLesson({{ $index }})" style="margin-top: 8px;">
                                    <i class="fas fa-plus"></i> Add Lesson
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addCurriculumItem()" style="margin-top: 10px;">
                    <i class="fas fa-plus"></i> Add Module
                </button>
                <small class="form-help">Add course modules with their lessons/topics. This forms the course outline.</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="duration_hours">
                        <i class="fas fa-clock"></i> Duration (Hours)
                    </label>
                    <input 
                        type="number" 
                        id="duration_hours" 
                        name="duration_hours" 
                        class="form-control @error('duration_hours') error @enderror"
                        value="{{ old('duration_hours', $course->duration_hours) }}"
                        step="0.1"
                        min="0"
                        placeholder="e.g., 1.5"
                    >
                    @error('duration_hours')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Course duration in hours</small>
                </div>

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
                        <option value="Active" {{ old('status', $course->status) === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $course->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">Active courses are available for enrollment</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Course
                </button>
                <a href="{{ route('admin.courses.view') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Course Statistics -->
    <div class="content-section" style="margin-top: 30px;">
        <h2 class="section-title">
            <i class="fas fa-chart-bar"></i> Course Statistics
        </h2>
        
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div class="stat-item">
                <div class="stat-label">Questions</div>
                <div class="stat-value">{{ $course->questionPools()->count() }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Enrolled</div>
                <div class="stat-value">{{ $course->accessibleTrainees()->count() }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Results</div>
                <div class="stat-value">{{ $course->results()->count() }}</div>
            </div>
        </div>
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

.stat-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.stat-item .stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.stat-item .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #667eea;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.remove-item {
    padding: 8px 12px;
    font-size: 12px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}
</style>

<script>
function addArrayItem(containerId, fieldName) {
    const container = document.getElementById(containerId);
    const newItem = document.createElement('div');
    newItem.className = 'array-input-group';
    newItem.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
    newItem.innerHTML = `
        <input type="text" name="${fieldName}[]" class="form-control" placeholder="Enter item">
        <button type="button" class="btn btn-danger remove-item" onclick="removeArrayItem(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(newItem);
}

function removeArrayItem(button) {
    button.closest('.array-input-group').remove();
}

function addCurriculumItem() {
    const container = document.getElementById('curriculum-container');
    const index = container.children.length;
    const newItem = document.createElement('div');
    newItem.className = 'curriculum-item';
    newItem.style.cssText = 'border: 1px solid #e0e0e0; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #f9fafb;';
    newItem.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <strong style="color: #667eea;">Module ${index + 1}</strong>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeCurriculumItem(this)">
                <i class="fas fa-times"></i> Remove Module
            </button>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Module Title</label>
            <input type="text" name="curriculum[${index}][module_title]" class="form-control" placeholder="e.g., Module 1 – Project Management Framework" required>
        </div>
        <div>
            <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">Lessons/Topics</label>
            <div class="lessons-container" data-module-index="${index}">
                <div class="lesson-item" style="display: flex; gap: 10px; margin-bottom: 8px;">
                    <input type="text" name="curriculum[${index}][lessons][]" class="form-control" placeholder="Enter lesson/topic name">
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="addLesson(${index})" style="margin-top: 8px;">
                <i class="fas fa-plus"></i> Add Lesson
            </button>
        </div>
    `;
    container.appendChild(newItem);
}

function removeCurriculumItem(button) {
    button.closest('.curriculum-item').remove();
    // Renumber modules
    const items = document.querySelectorAll('.curriculum-item');
    items.forEach((item, index) => {
        item.querySelector('strong').textContent = `Module ${index + 1}`;
        // Update all input names in this module
        const moduleInputs = item.querySelectorAll('input[name*="[module_title]"], input[name*="[lessons]"]');
        moduleInputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/curriculum\[\d+\]/, `curriculum[${index}]`));
            }
        });
        // Update lesson buttons
        const lessonButtons = item.querySelectorAll('button[onclick*="addLesson"]');
        lessonButtons.forEach(btn => {
            btn.setAttribute('onclick', `addLesson(${index})`);
        });
        item.querySelector('.lessons-container').setAttribute('data-module-index', index);
    });
}

function addLesson(moduleIndex) {
    const container = document.querySelector(`.lessons-container[data-module-index="${moduleIndex}"]`);
    const lessonItem = document.createElement('div');
    lessonItem.className = 'lesson-item';
    lessonItem.style.cssText = 'display: flex; gap: 10px; margin-bottom: 8px;';
    lessonItem.innerHTML = `
        <input type="text" name="curriculum[${moduleIndex}][lessons][]" class="form-control" placeholder="Enter lesson/topic name">
        <button type="button" class="btn btn-danger btn-sm remove-lesson" onclick="removeLesson(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(lessonItem);
}

function removeLesson(button) {
    const container = button.closest('.lessons-container');
    const lessons = container.querySelectorAll('.lesson-item');
    if (lessons.length > 1) {
        button.closest('.lesson-item').remove();
    } else {
        // If it's the last lesson, just clear it
        button.closest('.lesson-item').querySelector('input').value = '';
    }
}
</script>
@endsection

