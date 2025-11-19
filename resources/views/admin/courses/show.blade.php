@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="{{ route('admin.courses.view') }}" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Course Details
            </h1>
            <p style="margin: 0; color: #666;">View comprehensive course information</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-primary">
                <i class="fas fa-pencil-alt"></i> Edit Course
            </a>
            <a href="{{ route('admin.question-pool.course', $course->id) }}" class="btn btn-secondary">
                <i class="fas fa-question-circle"></i> View Questions
            </a>
        </div>
    </div>
</div>

<div class="page-content">
    <!-- Course Information Card -->
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-info-circle"></i> Course Information
        </h2>
        
        <div class="info-grid">
            <div class="info-item">
                <label>Course Code:</label>
                <div class="info-value">
                    <span class="course-code-badge">{{ $course->code }}</span>
                </div>
            </div>

            <div class="info-item">
                <label>Course Title:</label>
                <div class="info-value">{{ $course->title }}</div>
            </div>

            <div class="info-item">
                <label>Status:</label>
                <div class="info-value">
                    <span class="status-badge {{ $course->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                        {{ $course->status }}
                    </span>
                </div>
            </div>

            <div class="info-item">
                <label>Duration:</label>
                <div class="info-value">
                    @if($course->duration_hours)
                        {{ number_format($course->duration_hours, 1) }} hours
                    @else
                        <span class="text-muted">Not set</span>
                    @endif
                </div>
            </div>

            <div class="info-item full-width">
                <label>Description:</label>
                <div class="info-value">
                    @if($course->description)
                        {{ $course->description }}
                    @else
                        <span class="text-muted">No description provided</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Questions</div>
                <div class="stat-value">{{ $course->total_questions }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Enrolled Trainees</div>
                <div class="stat-value">{{ $course->enrolled_trainees }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Results</div>
                <div class="stat-value">{{ $course->total_results }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Schedules</div>
                <div class="stat-value">{{ $course->total_schedules }}</div>
            </div>
        </div>
    </div>

    <!-- Recent Questions -->
    @if($course->questionPools->count() > 0)
    <div class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title">
                <i class="fas fa-question-circle"></i> Recent Questions
            </h2>
            <a href="{{ route('admin.question-pool.course', $course->id) }}" class="btn btn-secondary btn-sm">
                View All Questions
            </a>
        </div>
        
        <div class="questions-list">
            @foreach($course->questionPools->take(5) as $question)
            <div class="question-item">
                <div class="question-header">
                    <span class="question-number">Q{{ $loop->iteration }}</span>
                    <span class="question-points">{{ $question->points }} point(s)</span>
                </div>
                <div class="question-text">{{ $question->question }}</div>
                @if($question->options)
                <div class="question-options">
                    @foreach($question->options as $key => $option)
                    <div class="option-item {{ $key === $question->correct_answer ? 'correct' : '' }}">
                        <span class="option-key">{{ $key }}.</span>
                        <span class="option-text">{{ $option }}</span>
                        @if($key === $question->correct_answer)
                        <span class="correct-badge"><i class="fas fa-check-circle"></i></span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Results -->
    @if($recentResults->count() > 0)
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-file-alt"></i> Recent Assessment Results
        </h2>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Trainee</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentResults as $result)
                    <tr>
                        <td>{{ $result->trainee->full_name ?? $result->trainee->username }}</td>
                        <td>{{ $result->score }} / {{ $result->total_questions }}</td>
                        <td>{{ number_format($result->percentage, 1) }}%</td>
                        <td>
                            <span class="status-badge {{ $result->status === 'Pass' ? 'status-active' : 'status-inactive' }}">
                                {{ $result->status }}
                            </span>
                        </td>
                        <td>{{ $result->completed_at ? $result->completed_at->format('M d, Y H:i') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Enrolled Trainees -->
    @if($enrolledTrainees->count() > 0)
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-users"></i> Enrolled Trainees
        </h2>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Access Granted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrolledTrainees as $trainee)
                    <tr>
                        <td>{{ $trainee->full_name }}</td>
                        <td>{{ $trainee->username }}</td>
                        <td>
                            <span class="status-badge {{ $trainee->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                {{ $trainee->status }}
                            </span>
                        </td>
                        <td>{{ $trainee->pivot->granted_at ? $trainee->pivot->granted_at->format('M d, Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<style>
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-item label {
    font-weight: 600;
    color: #666;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 15px;
    color: #333;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

.questions-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.question-item {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.question-number {
    background: #667eea;
    color: white;
    padding: 4px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 13px;
}

.question-points {
    background: #f0f4ff;
    color: #667eea;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.question-text {
    font-size: 15px;
    color: #333;
    margin-bottom: 15px;
    line-height: 1.6;
}

.question-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 6px;
    background: #f8f9fa;
    transition: all 0.2s;
}

.option-item.correct {
    background: #d1fae5;
    border: 2px solid #10b981;
}

.option-key {
    font-weight: 700;
    color: #667eea;
    min-width: 20px;
}

.option-text {
    flex: 1;
    color: #333;
}

.correct-badge {
    color: #10b981;
    font-size: 18px;
}

.text-muted {
    color: #999;
    font-style: italic;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 14px;
}
</style>
@endsection

