@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Admin Dashboard</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Trainee Profiles</h2>
        
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <div class="table-container">
            <table class="trainee-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Phone number</th>
                        <th>Payment Status</th>
                        <th>Remaining Balance</th>
                        <th>Course Access</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainees as $index => $trainee)
                    <tr>
                        <td>{{ ($trainees->currentPage() - 1) * $trainees->perPage() + $index + 1 }}</td>
                        <td>{{ $trainee->full_name }}</td>
                        <td>{{ $trainee->gender }}</td>
                        <td>
                            <code style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                {{ $trainee->username }}
                            </code>
                        </td>
                        <td>
                            <span class="status-badge {{ $trainee->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                {{ $trainee->status }}
                            </span>
                        </td>
                        <td>{{ $trainee->phone_number }}</td>
                        <td>
                            @if($trainee->has_payment ?? false)
                                <span class="status-badge status-active" style="font-size: 11px;">
                                    <i class="fas fa-check"></i> Paid
                                </span>
                            @else
                                <span class="status-badge status-inactive" style="font-size: 11px;">
                                    <i class="fas fa-times"></i> Not Paid
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($trainee->has_payment ?? false)
                                @if(($trainee->remaining_balance ?? 0) > 0)
                                    <span style="color: #f59e0b; font-weight: 600; font-size: 13px;">
                                        ₦{{ number_format($trainee->remaining_balance ?? 0, 2) }}
                                    </span>
                                @else
                                    <span style="color: #10b981; font-weight: 600; font-size: 13px;">
                                        ₦0.00
                                    </span>
                                @endif
                            @else
                                <span class="text-muted" style="font-size: 12px;">-</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="badge badge-secondary" style="font-size: 11px;">
                                    <i class="fas fa-book"></i> {{ $trainee->accessible_courses_count ?? 0 }} Access
                                </span>
                                @if($trainee->selected_courses)
                                    @php
                                        $selectedCount = is_array($trainee->selected_courses) ? count($trainee->selected_courses) : 0;
                                    @endphp
                                    @if($selectedCount > 0)
                                        <small style="color: #666; font-size: 10px;">
                                            {{ $selectedCount }} Selected
                                        </small>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="actions-cell">
                            <div class="action-buttons">
                                <a href="{{ route('admin.trainees.show', $trainee->id) }}" class="action-btn" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.trainees.edit', $trainee->id) }}" class="action-btn" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @if($trainee->status === 'Active')
                                <a href="{{ route('admin.trainees.view-as', $trainee->id) }}" class="action-btn view-as-btn" title="View As Trainee" style="color: #667eea;">
                                    <i class="fas fa-user-secret"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 40px;">
                            No trainees found. <a href="{{ route('admin.trainees.create') }}">Add a trainee</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trainees->hasPages())
        <div class="pagination-wrapper">
            {{ $trainees->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

