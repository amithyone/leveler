@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="{{ isset($course) ? route('admin.question-pool.course', $course->id) : route('admin.question-pool.index') }}" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Add New Question
            </h1>
            <p style="margin: 0; color: #666;">
                @if(isset($course))
                    <span class="course-code-badge">{{ $course->code }}</span>
                    - {{ $course->title }}
                @else
                    Select a course and add a question to its question pool
                @endif
            </p>
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
        <ul style="margin: 10px 0 0 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="content-section">
        <form action="{{ route('admin.question-pool.store') }}" method="POST" id="questionForm">
            @csrf

            <div class="form-group">
                <label for="course_id">Course <span class="required">*</span></label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <option value="">Select a course</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ (isset($course) && $course->id == $c->id) || old('course_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->code }} - {{ $c->title }}
                    </option>
                    @endforeach
                </select>
                <small style="color: #666; margin-top: 5px; display: block;">Select the course this question belongs to</small>
            </div>

            <div class="form-group">
                <label for="question">Question Text <span class="required">*</span></label>
                <textarea name="question" id="question" rows="4" class="form-control" required placeholder="Enter the question text">{{ old('question') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="type">Question Type <span class="required">*</span></label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="multiple_choice" {{ old('type', 'multiple_choice') === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                        <option value="true_false" {{ old('type') === 'true_false' ? 'selected' : '' }}>True/False</option>
                        <option value="essay" {{ old('type') === 'essay' ? 'selected' : '' }}>Essay</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="points">Points <span class="required">*</span></label>
                    <input type="number" name="points" id="points" class="form-control" value="{{ old('points', 1) }}" min="1" required>
                    <small style="color: #666; margin-top: 5px; display: block;">Points awarded for correct answer</small>
                </div>
            </div>

            <!-- Multiple Choice Options -->
            <div id="multipleChoiceOptions" style="display: {{ old('type', 'multiple_choice') === 'multiple_choice' ? 'block' : 'none' }};">
                <h3 style="margin: 25px 0 15px 0; color: #333; font-size: 18px;">Options</h3>
                
                <div class="form-group">
                    <label for="option_a">Option A <span class="required">*</span></label>
                    <input type="text" name="option_a" id="option_a" class="form-control" value="{{ old('option_a') }}" placeholder="Enter option A">
                </div>

                <div class="form-group">
                    <label for="option_b">Option B <span class="required">*</span></label>
                    <input type="text" name="option_b" id="option_b" class="form-control" value="{{ old('option_b') }}" placeholder="Enter option B">
                </div>

                <div class="form-group">
                    <label for="option_c">Option C <span class="required">*</span></label>
                    <input type="text" name="option_c" id="option_c" class="form-control" value="{{ old('option_c') }}" placeholder="Enter option C">
                </div>

                <div class="form-group">
                    <label for="option_d">Option D <span class="required">*</span></label>
                    <input type="text" name="option_d" id="option_d" class="form-control" value="{{ old('option_d') }}" placeholder="Enter option D">
                </div>

                <div class="form-group">
                    <label for="correct_answer">Correct Answer <span class="required">*</span></label>
                    <select name="correct_answer" id="correct_answer" class="form-control">
                        <option value="A" {{ old('correct_answer') === 'A' ? 'selected' : '' }}>Option A</option>
                        <option value="B" {{ old('correct_answer') === 'B' ? 'selected' : '' }}>Option B</option>
                        <option value="C" {{ old('correct_answer') === 'C' ? 'selected' : '' }}>Option C</option>
                        <option value="D" {{ old('correct_answer') === 'D' ? 'selected' : '' }}>Option D</option>
                    </select>
                </div>
            </div>

            <!-- True/False Options -->
            <div id="trueFalseOptions" style="display: {{ old('type') === 'true_false' ? 'block' : 'none' }};">
                <div class="form-group">
                    <label for="correct_answer_tf">Correct Answer <span class="required">*</span></label>
                    <select name="correct_answer" id="correct_answer_tf" class="form-control">
                        <option value="A" {{ old('correct_answer') === 'A' ? 'selected' : '' }}>True</option>
                        <option value="B" {{ old('correct_answer') === 'B' ? 'selected' : '' }}>False</option>
                    </select>
                </div>
            </div>

            <!-- Essay Options -->
            <div id="essayOptions" style="display: {{ old('type') === 'essay' ? 'block' : 'none' }};">
                <div class="form-group">
                    <label for="correct_answer_essay">Expected Answer / Key Points</label>
                    <textarea name="correct_answer" id="correct_answer_essay" rows="6" class="form-control" placeholder="Enter key points or expected answer for essay questions">{{ old('correct_answer') }}</textarea>
                    <small style="color: #666; margin-top: 5px; display: block;">Provide key points or expected answer for essay questions</small>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ isset($course) ? route('admin.question-pool.course', $course->id) : route('admin.question-pool.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Question
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.course-code-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.content-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
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
    transition: all 0.2s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #f0f0f0;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 2px solid #ef4444;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const multipleChoiceDiv = document.getElementById('multipleChoiceOptions');
    const trueFalseDiv = document.getElementById('trueFalseOptions');
    const essayDiv = document.getElementById('essayOptions');
    const correctAnswerSelect = document.getElementById('correct_answer');
    const correctAnswerTf = document.getElementById('correct_answer_tf');
    const correctAnswerEssay = document.getElementById('correct_answer_essay');

    function toggleOptions() {
        const type = typeSelect.value;
        
        // Hide all option divs
        multipleChoiceDiv.style.display = 'none';
        trueFalseDiv.style.display = 'none';
        essayDiv.style.display = 'none';
        
        // Show relevant option div
        if (type === 'multiple_choice') {
            multipleChoiceDiv.style.display = 'block';
            if (correctAnswerSelect) correctAnswerSelect.required = true;
            if (correctAnswerTf) correctAnswerTf.required = false;
            if (correctAnswerEssay) correctAnswerEssay.required = false;
        } else if (type === 'true_false') {
            trueFalseDiv.style.display = 'block';
            if (correctAnswerSelect) correctAnswerSelect.required = false;
            if (correctAnswerTf) correctAnswerTf.required = true;
            if (correctAnswerEssay) correctAnswerEssay.required = false;
        } else if (type === 'essay') {
            essayDiv.style.display = 'block';
            if (correctAnswerSelect) correctAnswerSelect.required = false;
            if (correctAnswerTf) correctAnswerTf.required = false;
            if (correctAnswerEssay) correctAnswerEssay.required = false;
        }
    }

    typeSelect.addEventListener('change', toggleOptions);
    
    // Initial toggle on page load
    toggleOptions();
});
</script>
@endsection
