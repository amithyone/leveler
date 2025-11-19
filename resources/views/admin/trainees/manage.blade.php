@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title"><i class="fas fa-users-cog"></i> Manage Trainees</h1>
            <p style="margin: 0; color: #666;">Activate, deactivate, and manage all trainees</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.trainees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Trainee
            </a>
            <a href="{{ route('admin.payments.create') }}" class="btn btn-secondary">
                <i class="fas fa-money-bill"></i> Record Payment
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

    @if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Trainees</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active</div>
                <div class="stat-value">{{ $stats['active'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Inactive</div>
                <div class="stat-value">{{ $stats['inactive'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">With Payment</div>
                <div class="stat-value">{{ $stats['with_payment'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="content-section" style="margin-bottom: 20px;">
        <form method="GET" action="{{ route('admin.trainees.manage') }}" class="filters-form">
            <div class="filters-row">
                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="filter-group" style="flex: 1;">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by name, username, or phone..." 
                           value="{{ request('search') }}">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.trainees.manage') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Form -->
    <form method="POST" action="{{ route('admin.trainees.bulk-action') }}" id="bulkActionForm" style="display: none;">
        @csrf
        <input type="hidden" name="action" id="bulkAction">
        <input type="hidden" name="trainee_ids" id="bulkTraineeIds">
    </form>

    <!-- Trainees Table -->
    <div class="content-section">
        @if($trainees->count() > 0)
        <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div>
                <strong>Showing {{ $trainees->firstItem() }} - {{ $trainees->lastItem() }} of {{ $trainees->total() }} trainees</strong>
            </div>
            <div class="bulk-actions" style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-success btn-sm" onclick="bulkAction('activate')" id="bulkActivateBtn" style="display: none;">
                    <i class="fas fa-check"></i> Activate Selected
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="bulkAction('deactivate')" id="bulkDeactivateBtn" style="display: none;">
                    <i class="fas fa-pause"></i> Deactivate Selected
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="select-all">
                        </th>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Package</th>
                        <th>Courses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainees as $index => $trainee)
                    <tr>
                        <td>
                            <input type="checkbox" name="trainee_ids[]" value="{{ $trainee->id }}" 
                                   class="trainee-checkbox" 
                                   data-status="{{ $trainee->status }}">
                        </td>
                        <td>{{ $trainees->firstItem() + $index }}</td>
                        <td>
                            <div style="font-weight: 600; color: #333;">{{ $trainee->full_name }}</div>
                            <small style="color: #666;">{{ $trainee->gender }}</small>
                        </td>
                        <td>
                            <code style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                {{ $trainee->username }}
                            </code>
                        </td>
                        <td>{{ $trainee->phone_number }}</td>
                        <td>
                            <span class="status-badge {{ $trainee->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                {{ $trainee->status }}
                            </span>
                        </td>
                        <td>
                            @if($trainee->has_payment)
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span class="status-badge status-active" style="font-size: 11px;">
                                        <i class="fas fa-check"></i> Paid
                                    </span>
                                    @if($trainee->remaining_balance > 0)
                                    <small style="color: #f59e0b; font-size: 11px;">
                                        Balance: ₦{{ number_format($trainee->remaining_balance, 2) }}
                                    </small>
                                    @else
                                    <small style="color: #10b981; font-size: 11px;">
                                        Fully Paid
                                    </small>
                                    @endif
                                </div>
                            @else
                                <span class="status-badge status-inactive" style="font-size: 11px;">
                                    <i class="fas fa-times"></i> Not Paid
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($trainee->package_type)
                                <span class="badge badge-info" style="font-size: 11px;">
                                    {{ $trainee->package_type === 'package' ? '4 Courses' : '1 Course' }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size: 11px;">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-secondary" style="font-size: 11px;">
                                <i class="fas fa-book"></i> {{ $trainee->accessible_courses_count }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                @if($trainee->status === 'Active')
                                <a href="{{ route('admin.trainees.view-as', $trainee->id) }}" 
                                   class="action-btn" 
                                   title="View As Trainee"
                                   style="color: #667eea;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.payments.create', ['trainee_id' => $trainee->id]) }}" 
                                   class="action-btn" 
                                   title="Record Payment"
                                   style="color: #10b981;">
                                    <i class="fas fa-money-bill"></i>
                                </a>
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
            <i class="fas fa-users" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
            <h3>No Trainees Found</h3>
            <p>No trainees match your search criteria.</p>
            <a href="{{ route('admin.trainees.manage') }}" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Clear Filters
            </a>
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
    grid-template-columns: 200px 1fr auto;
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

.data-table tbody tr:last-child td {
    border-bottom: none;
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

.badge-secondary {
    background: #e5e7eb;
    color: #374151;
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

.btn-success {
    background: #10b981;
    color: white;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
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
    
    .data-table {
        font-size: 12px;
    }
    
    .data-table th,
    .data-table td {
        padding: 10px 8px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.trainee-checkbox');
    const bulkActivateBtn = document.getElementById('bulkActivateBtn');
    const bulkDeactivateBtn = document.getElementById('bulkDeactivateBtn');

    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkbox change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSelectAll();
            updateBulkActions();
        });
    });

    function updateSelectAll() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const someChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (selectAll) {
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        }
    }

    function updateBulkActions() {
        const selected = Array.from(checkboxes).filter(cb => cb.checked);
        const selectedIds = selected.map(cb => cb.value);
        
        if (selected.length > 0) {
            // Check if any selected are inactive
            const hasInactive = selected.some(cb => cb.dataset.status === 'Inactive');
            // Check if any selected are active
            const hasActive = selected.some(cb => cb.dataset.status === 'Active');
            
            bulkActivateBtn.style.display = hasInactive ? 'inline-flex' : 'none';
            bulkDeactivateBtn.style.display = hasActive ? 'inline-flex' : 'none';
        } else {
            bulkActivateBtn.style.display = 'none';
            bulkDeactivateBtn.style.display = 'none';
        }
    }
});

function bulkAction(action) {
    const selected = Array.from(document.querySelectorAll('.trainee-checkbox:checked'));
    const selectedIds = selected.map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Please select at least one trainee');
        return;
    }
    
    const actionText = action === 'activate' ? 'activate' : 'deactivate';
    if (!confirm(`Are you sure you want to ${actionText} ${selectedIds.length} trainee(s)?`)) {
        return;
    }
    
    document.getElementById('bulkAction').value = action;
    document.getElementById('bulkTraineeIds').value = JSON.stringify(selectedIds);
    document.getElementById('bulkActionForm').submit();
}
</script>
@endsection
