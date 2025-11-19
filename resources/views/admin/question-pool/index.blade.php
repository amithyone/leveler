@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-question-circle"></i> Question Pool</h1>
    <p style="margin: 0; color: #666;">Select a course to view and manage questions</p>
</div>

<div class="page-content">
    @if($courses->count() > 0)
    <div class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @foreach($courses as $course)
        <div class="course-card" onclick="window.location.href='{{ route('admin.question-pool.course', $course->id) }}'">
            <div class="course-card-header">
                <span class="course-code-badge">{{ $course->code }}</span>
                <span class="status-badge {{ $course->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                    {{ $course->status }}
                </span>
            </div>
            <h3 class="course-title">{{ $course->title }}</h3>
            <div class="course-stats">
                <div class="stat-item">
                    <i class="fas fa-question-circle"></i>
                    <span>{{ $course->question_pools_count ?? 0 }} Questions</span>
                </div>
            </div>
            <div class="course-action">
                <a href="{{ route('admin.question-pool.course', $course->id) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-eye"></i> View Questions
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-book-open" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
        <h3>No Courses Found</h3>
        <p>No courses available. Please add courses first.</p>
    </div>
    @endif
</div>

<style>
.course-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s;
}

.course-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    transform: translateY(-2px);
}

.course-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.course-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    line-height: 1.4;
}

.course-stats {
    margin-bottom: 15px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 14px;
}

.course-action {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.btn-block {
    width: 100%;
    text-align: center;
}
</style>
@endsection

