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
                        <th>Password</th>
                        <th>Status</th>
                        <th>Phone number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainees as $index => $trainee)
                    <tr>
                        <td>{{ ($trainees->currentPage() - 1) * $trainees->perPage() + $index + 1 }}</td>
                        <td>{{ $trainee->full_name }}</td>
                        <td>{{ $trainee->gender }}</td>
                        <td>{{ $trainee->username }}</td>
                        <td>{{ $trainee->password }}</td>
                        <td>
                            <span class="status-badge {{ $trainee->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                                {{ $trainee->status }}
                            </span>
                        </td>
                        <td>{{ $trainee->phone_number }}</td>
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
                        <td colspan="8" style="text-align: center; padding: 40px;">
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

