@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="{{ route('admin.results.index') }}" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Result Details
            </h1>
            <p style="margin: 0; color: #666;">Assessment Result Information</p>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="content-section">
        <div class="result-header">
            <div class="result-status-badge {{ $result->status === 'passed' ? 'status-active' : 'status-inactive' }}">
                <i class="fas fa-{{ $result->status === 'passed' ? 'check-circle' : 'times-circle' }}"></i>
                {{ ucfirst($result->status) }}
            </div>
            <div class="result-score">
                <div class="score-value">{{ number_format($result->percentage, 1) }}%</div>
                <div class="score-detail">{{ $result->score }} / {{ $result->total_questions }} questions</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>Trainee</label>
                <div class="info-value">
                    <strong>{{ $result->trainee->full_name }}</strong>
                    <br>
                    <small style="color: #666;">{{ $result->trainee->username }}</small>
                </div>
            </div>

            <div class="info-item">
                <label>Course</label>
                <div class="info-value">
                    <strong>{{ $result->course->code }}</strong>
                    <br>
                    <small style="color: #666;">{{ $result->course->title }}</small>
                </div>
            </div>

            <div class="info-item">
                <label>Date Completed</label>
                <div class="info-value">
                    {{ $result->completed_at ? $result->completed_at->format('F d, Y') : 'N/A' }}
                    <br>
                    <small style="color: #666;">{{ $result->completed_at ? $result->completed_at->format('h:i A') : '' }}</small>
                </div>
            </div>

            <div class="info-item">
                <label>Score</label>
                <div class="info-value">
                    <strong style="font-size: 20px;">{{ $result->score }} / {{ $result->total_questions }}</strong>
                </div>
            </div>

            <div class="info-item">
                <label>Percentage</label>
                <div class="info-value" style="font-size: 20px; color: {{ $result->percentage >= 50 ? '#10b981' : '#ef4444' }}; font-weight: 700;">
                    {{ number_format($result->percentage, 2) }}%
                </div>
            </div>

            <div class="info-item">
                <label>Status</label>
                <div class="info-value">
                    <span class="status-badge {{ $result->status === 'passed' ? 'status-active' : 'status-inactive' }}">
                        {{ ucfirst($result->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    margin-bottom: 30px;
    color: white;
}

.result-status-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
}

.result-score {
    text-align: right;
}

.score-value {
    font-size: 48px;
    font-weight: 700;
    line-height: 1;
}

.score-detail {
    font-size: 14px;
    opacity: 0.9;
    margin-top: 5px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item label {
    font-size: 12px;
    color: #666;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 16px;
    color: #333;
    font-weight: 500;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}
</style>
@endsection

