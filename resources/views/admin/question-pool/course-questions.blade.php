@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="{{ route('admin.courses.view') }}" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Questions for: {{ $course->title }}
            </h1>
            <p style="margin: 0; color: #666;">
                <span class="course-code-badge">{{ $course->code }}</span>
                - {{ $questions->total() }} question(s)
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-secondary">
                <i class="fas fa-info-circle"></i> Course Details
            </a>
            <a href="{{ route('admin.question-pool.create', ['course' => $course->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Question
            </a>
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

    <!-- Questions Summary -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Questions</div>
                <div class="stat-value">{{ $questions->total() }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Points</div>
                <div class="stat-value">{{ $questions->sum('points') }}</div>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    <div class="content-section">
        @if($questions->count() > 0)
        <div class="questions-list">
            @foreach($questions as $question)
            <div class="question-card">
                <div class="question-header">
                    <div class="question-meta">
                        <span class="question-number">Question #{{ $questions->firstItem() + $loop->index }}</span>
                        <span class="question-points">{{ $question->points }} point(s)</span>
                        <span class="question-type">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                    </div>
                    <div class="question-actions">
                        <a href="{{ route('admin.question-pool.edit', $question->id) }}" class="action-btn" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <form action="{{ route('admin.question-pool.destroy', $question->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this question?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn" title="Delete" style="background: #f0f0f0; border: none; cursor: pointer;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="question-text">
                    {{ $question->question }}
                </div>

                @if($question->options && is_array($question->options))
                <div class="question-options">
                    <div class="options-label">Options:</div>
                    @foreach($question->options as $key => $option)
                    <div class="option-item {{ $key === $question->correct_answer ? 'correct-answer' : '' }}">
                        <span class="option-key">{{ $key }}.</span>
                        <span class="option-text">{{ $option }}</span>
                        @if($key === $question->correct_answer)
                        <span class="correct-badge">
                            <i class="fas fa-check-circle"></i> Correct Answer
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="question-answer">
                    <strong>Correct Answer:</strong> {{ $question->correct_answer }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($questions->hasPages())
        <div class="pagination-wrapper" style="margin-top: 30px;">
            {{ $questions->links() }}
        </div>
        @endif

        @else
        <div class="empty-state">
            <i class="fas fa-question-circle" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3>No Questions Found</h3>
            <p>This course doesn't have any questions yet.</p>
            <a href="{{ route('admin.question-pool.create', ['course' => $course->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add First Question
            </a>
        </div>
        @endif
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

.questions-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.question-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 25px;
    transition: all 0.3s;
}

.question-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.question-meta {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.question-number {
    background: #667eea;
    color: white;
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
}

.question-points {
    background: #f0f4ff;
    color: #667eea;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.question-type {
    background: #e5e7eb;
    color: #374151;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.question-actions {
    display: flex;
    gap: 8px;
}

.question-text {
    font-size: 16px;
    color: #333;
    line-height: 1.6;
    margin-bottom: 20px;
    font-weight: 500;
}

.question-options {
    margin-top: 15px;
}

.options-label {
    font-weight: 600;
    color: #666;
    margin-bottom: 12px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    border-radius: 8px;
    background: #f8f9fa;
    margin-bottom: 10px;
    transition: all 0.2s;
}

.option-item:hover {
    background: #f0f0f0;
}

.option-item.correct-answer {
    background: #d1fae5;
    border: 2px solid #10b981;
}

.option-key {
    font-weight: 700;
    color: #667eea;
    min-width: 25px;
    font-size: 15px;
}

.option-text {
    flex: 1;
    color: #333;
    font-size: 15px;
}

.correct-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #10b981;
    font-weight: 600;
    font-size: 13px;
}

.question-answer {
    padding: 15px;
    background: #f0f4ff;
    border-radius: 8px;
    border-left: 4px solid #667eea;
    margin-top: 15px;
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
    margin-bottom: 25px;
}

.action-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: #f0f0f0;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    transition: all 0.2s;
}

.action-btn:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.action-btn:last-child:hover {
    background: #ef4444;
}

@media (max-width: 768px) {
    .question-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .question-meta {
        width: 100%;
    }
}
</style>
@endsection

