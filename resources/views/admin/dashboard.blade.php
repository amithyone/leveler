@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Admin Dashboard</h1>
</div>

<div class="page-content">
    <!-- Statistics Cards -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: #6B46C1;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ $stats['total_trainees'] }}</div>
                <div class="stat-label">Total Trainees</div>
                <div class="stat-detail">
                    <span class="stat-active">{{ $stats['active_trainees'] }} Active</span>
                    <span class="stat-inactive">{{ $stats['inactive_trainees'] }} Inactive</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #10b981;">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ $stats['total_courses'] }}</div>
                <div class="stat-label">Total Courses</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #f59e0b;">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ $stats['total_schedules'] }}</div>
                <div class="stat-label">Total Schedules</div>
                <div class="stat-detail">
                    <span class="stat-upcoming">{{ $stats['upcoming_schedules'] }} Upcoming</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #ef4444;">
                <i class="fas fa-file-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ $stats['total_results'] }}</div>
                <div class="stat-label">Total Results</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #3b82f6;">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ $stats['total_question_pools'] }}</div>
                <div class="stat-label">Question Pools</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background-color: #8b5cf6;">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ $stats['total_admin_users'] }}</div>
                <div class="stat-label">Admin Users</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="dashboard-content-grid">
        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-file-check"></i> Recent Results
            </h2>
            @if($stats['recent_results']->count() > 0)
            <div class="table-container">
                <table class="trainee-table">
                    <thead>
                        <tr>
                            <th>Trainee</th>
                            <th>Course</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['recent_results'] as $result)
                        <tr>
                            <td>{{ $result->trainee->full_name ?? 'N/A' }}</td>
                            <td>{{ $result->course->title ?? 'N/A' }}</td>
                            <td>{{ $result->score }}/{{ $result->total_questions }}</td>
                            <td>{{ number_format($result->percentage, 2) }}%</td>
                            <td>
                                <span class="status-badge {{ $result->status === 'passed' ? 'status-active' : ($result->status === 'failed' ? 'status-inactive' : '') }}">
                                    {{ ucfirst($result->status ?? 'pending') }}
                                </span>
                            </td>
                            <td>{{ $result->completed_at ? $result->completed_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p style="padding: 20px; text-align: center; color: #666;">No results found.</p>
            @endif
            <div style="margin-top: 15px;">
                <a href="{{ route('admin.results.index') }}" class="btn btn-primary">View All Results</a>
            </div>
        </div>

        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-user-plus"></i> Recent Trainees
            </h2>
            @if($stats['recent_trainees']->count() > 0)
            <div class="table-container">
                <table class="trainee-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['recent_trainees'] as $trainee)
                        <tr>
                            <td>{{ $trainee->full_name }}</td>
                            <td>{{ $trainee->username }}</td>
                            <td>{{ $trainee->gender }}</td>
                            <td>
                                <span class="status-badge {{ $trainee->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                    {{ $trainee->status ?? 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $trainee->phone_number }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p style="padding: 20px; text-align: center; color: #666;">No trainees found.</p>
            @endif
            <div style="margin-top: 15px;">
                <a href="{{ route('admin.trainees.index') }}" class="btn btn-primary">View All Trainees</a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-section">
        <h2 class="section-title">
            <i class="fas fa-bolt"></i> Quick Actions
        </h2>
        <div class="quick-actions">
            <a href="{{ route('admin.trainees.create') }}" class="quick-action-btn">
                <i class="fas fa-user-plus"></i>
                <span>Add Trainee</span>
            </a>
            <a href="{{ route('admin.schedules.index') }}" class="quick-action-btn">
                <i class="fas fa-calendar-plus"></i>
                <span>Create Schedule</span>
            </a>
            <a href="{{ route('admin.question-pool.index') }}" class="quick-action-btn">
                <i class="fas fa-plus-circle"></i>
                <span>Add Questions</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="quick-action-btn">
                <i class="fas fa-chart-line"></i>
                <span>View Reports</span>
            </a>
        </div>
    </div>
</div>

<style>
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
}

.stat-info {
    flex: 1;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
}

.stat-detail {
    display: flex;
    gap: 10px;
    margin-top: 5px;
    font-size: 12px;
}

.stat-active {
    color: #10b981;
    font-weight: 500;
}

.stat-inactive {
    color: #ef4444;
    font-weight: 500;
}

.stat-upcoming {
    color: #f59e0b;
    font-weight: 500;
}

.dashboard-content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
    border: 2px solid transparent;
}

.quick-action-btn:hover {
    background: #e9ecef;
    border-color: #6B46C1;
    color: #6B46C1;
    transform: translateY(-2px);
}

.quick-action-btn i {
    font-size: 24px;
    color: #6B46C1;
}

.quick-action-btn:hover i {
    color: #6B46C1;
}

.quick-action-btn span {
    font-size: 14px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .dashboard-stats {
        grid-template-columns: 1fr;
    }
    
    .dashboard-content-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endsection

