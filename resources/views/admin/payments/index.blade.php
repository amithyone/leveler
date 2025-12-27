@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Payment Management</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title">All Payments</h2>
            <a href="{{ route('admin.payments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Record Payment
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <!-- Search and Filter -->
        <form method="GET" action="{{ route('admin.payments.index') }}" style="margin-bottom: 20px;">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search by trainee name or username..." class="form-group input" value="{{ request('search') }}" style="width: 100%;">
                </div>
                <div class="form-group">
                    <select name="status" class="form-group input" style="width: 100%;">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Failed" {{ request('status') === 'Failed' ? 'selected' : '' }}>Failed</option>
                        <option value="Refunded" {{ request('status') === 'Refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </div>
        </form>

        @if($payments->count() > 0)
        <div class="table-container">
            <table class="trainee-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Trainee</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                        <th>Receipt #</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $index => $payment)
                    <tr>
                        <td>{{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}</td>
                        <td>{{ $payment->trainee->full_name ?? 'N/A' }}</td>
                        <td>₦{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>
                            <span class="status-badge 
                                {{ $payment->status === 'Completed' ? 'status-active' : '' }}
                                {{ $payment->status === 'Failed' ? 'status-inactive' : '' }}
                                {{ $payment->status === 'Pending' ? 'status-pending' : '' }}
                                {{ $payment->status === 'Refunded' ? 'status-refunded' : '' }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td>{{ $payment->receipt_number ?? 'N/A' }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.payments.edit', $payment->id) }}" class="action-btn" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" title="Delete" style="color: #ef4444;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="pagination-wrapper">
            {{ $payments->links() }}
        </div>
        @endif
        @else
        <p style="padding: 40px; text-align: center; color: #666;">
            No payments found. <a href="{{ route('admin.payments.create') }}">Record a payment</a>
        </p>
        @endif
    </div>
</div>

<style>
.status-pending {
    background-color: #f59e0b;
    color: white;
}

.status-refunded {
    background-color: #8b5cf6;
    color: white;
}
</style>
@endsection

