@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title"><i class="fas fa-book"></i> View Courses</h1>
            <p style="margin: 0; color: #666;">Manage and view all available courses</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Course
            </a>
            <button class="btn btn-secondary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
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

    @if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Courses Summary -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Courses</div>
                <div class="stat-value">{{ $courses->count() }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Courses</div>
                <div class="stat-value">{{ $courses->where('status', 'Active')->count() }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Questions</div>
                <div class="stat-value">{{ $courses->sum('total_questions') }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Enrollments</div>
                <div class="stat-value">{{ $courses->sum('enrolled_trainees') }}</div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="content-section">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Number of Questions</th>
                        <th>Assessment Duration</th>
                        <th>Enrolled Trainees</th>
                        <th>Total Results</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $index => $course)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="course-code-badge">{{ $course->code }}</span>
                        </td>
                        <td>
                            <div class="course-title-cell">
                                <strong>{{ $course->title }}</strong>
                                @if($course->description)
                                <small class="course-description">{{ \Illuminate\Support\Str::limit($course->description, 60) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                <i class="fas fa-question-circle"></i>
                                {{ $course->total_questions }}
                            </span>
                        </td>
                        <td>
                            @if($course->estimated_duration > 0)
                                <span class="duration-badge">
                                    <i class="fas fa-clock"></i>
                                    {{ $course->estimated_duration }} mins
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-secondary">
                                <i class="fas fa-users"></i>
                                {{ $course->enrolled_trainees }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-primary">
                                <i class="fas fa-file-alt"></i>
                                {{ $course->total_results }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge {{ $course->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                {{ $course->status }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <a href="{{ route('admin.question-pool.course', $course->id) }}" 
                                   class="action-btn" 
                                   title="View Questions">
                                    <i class="fas fa-question-circle"></i>
                                </a>
                                <a href="{{ route('admin.courses.show', $course->id) }}" 
                                   class="action-btn" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" 
                                   class="action-btn" 
                                   title="Edit Course">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @if($course->enrolled_trainees == 0 && $course->total_results == 0)
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" 
                                      method="POST" 
                                      style="display: inline;"
                                      onsubmit="return confirm('Are you sure you want to delete this course? This will also delete all associated questions. This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="action-btn action-btn-danger" 
                                            title="Delete Course">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="action-btn action-btn-disabled" 
                                      title="Cannot delete: Course has enrollments or results">
                                    <i class="fas fa-trash"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px;">
                            <div class="empty-state">
                                <i class="fas fa-book-open" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                                <p>No courses found.</p>
                                <p style="color: #999; font-size: 14px;">Courses will appear here once they are added to the system.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #333;
}

.course-code-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.course-title-cell {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.course-title-cell strong {
    color: #333;
    font-size: 15px;
}

.course-description {
    color: #666;
    font-size: 12px;
    font-style: italic;
}

.duration-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f0f4ff;
    color: #667eea;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.data-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.data-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table td {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: #f0f0f0;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    transition: all 0.2s;
    text-decoration: none;
}

.action-btn:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.action-btn-danger {
    background: #fee2e2;
    color: #dc2626;
}

.action-btn-danger:hover {
    background: #ef4444;
    color: white;
}

.action-btn-disabled {
    background: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.6;
}

.action-btn-disabled:hover {
    background: #f3f4f6;
    color: #9ca3af;
    transform: none;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.badge-secondary {
    background: #e5e7eb;
    color: #374151;
}

.badge-primary {
    background: #dbeafe;
    color: #2563eb;
}

.text-muted {
    color: #999;
    font-style: italic;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 15px;
}

.empty-state p {
    color: #666;
    margin: 5px 0;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .data-table {
        font-size: 12px;
    }
    
    .data-table th,
    .data-table td {
        padding: 10px 8px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endsection
