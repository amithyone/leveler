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
                        <td>
                            @if($payment->payment_method === 'Manual Payment' && $payment->manual_payment_details)
                            <span style="color: #667eea; cursor: pointer;" title="View Payment Details" onclick="showManualPaymentDetails({{ $payment->id }})">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            @endif
                        </td>
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

<!-- Manual Payment Details Modal -->
<div id="manualPaymentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #667eea;">Manual Payment Details</h2>
            <button onclick="closeManualPaymentModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
        </div>
        <div id="manualPaymentContent"></div>
        <div style="margin-top: 20px; text-align: right;">
            <button onclick="closeManualPaymentModal()" class="btn btn-secondary">Close</button>
        </div>
    </div>
</div>

<script>
const paymentDetails = @json($payments->map(function($p) {
    return [
        'id' => $p->id,
        'details' => $p->manual_payment_details
    ];
})->keyBy('id'));

function showManualPaymentDetails(paymentId) {
    const details = paymentDetails[paymentId]?.details;
    const modal = document.getElementById('manualPaymentModal');
    const content = document.getElementById('manualPaymentContent');
    
    if (!details) {
        content.innerHTML = '<p>No payment details available.</p>';
        modal.style.display = 'flex';
        return;
    }
    
    let html = '<div style="line-height: 1.8;">';
    if (details.bank_name) {
        html += `<p><strong>Bank Name:</strong> ${details.bank_name}</p>`;
    }
    if (details.account_name) {
        html += `<p><strong>Account Name:</strong> ${details.account_name}</p>`;
    }
    if (details.account_number) {
        html += `<p><strong>Account Number:</strong> ${details.account_number}</p>`;
    }
    if (details.instructions) {
        html += `<div style="margin-top: 15px;"><strong>Instructions:</strong><div style="background: #f5f5f5; padding: 15px; border-radius: 4px; margin-top: 10px; white-space: pre-wrap;">${details.instructions}</div></div>`;
    }
    html += '</div>';
    
    content.innerHTML = html;
    modal.style.display = 'flex';
}

function closeManualPaymentModal() {
    document.getElementById('manualPaymentModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('manualPaymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeManualPaymentModal();
    }
});
</script>
@endsection

