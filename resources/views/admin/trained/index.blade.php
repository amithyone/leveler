@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title"><i class="fas fa-graduation-cap"></i> Trained Trainees</h1>
            <p style="margin: 0; color: #666;">Trainees who have completed courses and earned certificates</p>
        </div>
    </div>
</div>

<div class="page-content">
    <!-- Statistics -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Trained Trainees</div>
                <div class="stat-value">{{ $stats['total_trained'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Certificates</div>
                <div class="stat-value">{{ $stats['total_certificates'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Courses Completed</div>
                <div class="stat-value">{{ $stats['total_courses_completed'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-section" style="margin-bottom: 20px;">
        <form method="GET" action="{{ route('admin.trained.index') }}" class="filters-form">
            <div class="filters-row">
                <div class="filter-group">
                    <label for="course_id">Course:</label>
                    <select name="course_id" id="course_id" class="form-control">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->code }} - {{ $course->title }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group" style="flex: 1;">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by trainee name or username..." 
                           value="{{ request('search') }}">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.trained.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Trained Trainees Table -->
    <div class="content-section">
        @if($trainees->count() > 0)
        <div class="table-header" style="margin-bottom: 15px;">
            <strong>Showing {{ $trainees->firstItem() }} - {{ $trainees->lastItem() }} of {{ $trainees->total() }} trained trainees</strong>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Trainee</th>
                        <th>Certificates</th>
                        <th>Latest Certificate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainees as $index => $trainee)
                    <tr>
                        <td>{{ $trainees->firstItem() + $index }}</td>
                        <td>
                            <div style="font-weight: 600; color: #333;">{{ $trainee->full_name }}</div>
                            <small style="color: #666;">{{ $trainee->username }}</small>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                <i class="fas fa-certificate"></i> {{ $trainee->certificate_count }} Certificate(s)
                            </span>
                        </td>
                        <td>
                            @if($trainee->latest_certificate)
                                <div>
                                    <strong>{{ $trainee->latest_certificate->course->code }}</strong>
                                    <br>
                                    <small style="color: #666;">
                                        {{ $trainee->latest_certificate->completed_at ? $trainee->latest_certificate->completed_at->format('M d, Y') : 'N/A' }}
                                    </small>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge {{ $trainee->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                {{ $trainee->status }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <a href="{{ route('admin.trainees.show', $trainee->id) }}" class="action-btn" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($trainee->status === 'Active')
                                <a href="{{ route('admin.trainees.view-as', $trainee->id) }}" class="action-btn" title="View As Trainee" style="color: #667eea;">
                                    <i class="fas fa-user-secret"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($trainees->hasPages())
        <div class="pagination-wrapper" style="margin-top: 20px;">
            {{ $trainees->links() }}
        </div>
        @endif

        @else
        <div class="empty-state">
            <i class="fas fa-graduation-cap" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3>No Trained Trainees Found</h3>
            <p>No trainees have completed any courses yet.</p>
        </div>
        @endif
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

.filters-form {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.filters-row {
    display: grid;
    grid-template-columns: 250px 1fr auto;
    gap: 15px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-weight: 600;
    color: #333;
    font-size: 13px;
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.form-control {
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
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

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
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

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
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

.text-muted {
    color: #999;
    font-style: italic;
}

@media (max-width: 768px) {
    .filters-row {
        grid-template-columns: 1fr;
    }
    
    .filter-actions {
        width: 100%;
    }
    
    .filter-actions .btn {
        flex: 1;
    }
}
</style>
@endsection
