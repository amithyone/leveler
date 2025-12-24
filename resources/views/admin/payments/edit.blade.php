@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Payment</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Edit Payment</h2>

        @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}" class="trainee-form">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="trainee_id">Trainee *</label>
                    <select name="trainee_id" id="trainee_id" required>
                        <option value="">Select Trainee</option>
                        @foreach($trainees as $trainee)
                        <option value="{{ $trainee->id }}" {{ old('trainee_id', $payment->trainee_id) == $trainee->id ? 'selected' : '' }}>
                            {{ $trainee->full_name }} ({{ $trainee->username }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="amount">Amount (₦) *</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount', $payment->amount) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="payment_method">Payment Method *</label>
                    <select name="payment_method" id="payment_method" required onchange="toggleManualPaymentDetails()">
                        <option value="Cash" {{ old('payment_method', $payment->payment_method) === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Bank Transfer" {{ old('payment_method', $payment->payment_method) === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Mobile Money" {{ old('payment_method', $payment->payment_method) === 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="Card" {{ old('payment_method', $payment->payment_method) === 'Card' ? 'selected' : '' }}>Card</option>
                        <option value="Manual Payment" {{ old('payment_method', $payment->payment_method) === 'Manual Payment' ? 'selected' : '' }}>Manual Payment</option>
                        <option value="Other" {{ old('payment_method', $payment->payment_method) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_date">Payment Date *</label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">Payment Status *</label>
                    <select name="status" id="status" required>
                        <option value="Pending" {{ old('status', $payment->status) === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ old('status', $payment->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Failed" {{ old('status', $payment->status) === 'Failed' ? 'selected' : '' }}>Failed</option>
                        <option value="Refunded" {{ old('status', $payment->status) === 'Refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="receipt_number">Receipt Number</label>
                    <input type="text" name="receipt_number" id="receipt_number" value="{{ old('receipt_number', $payment->receipt_number) }}" placeholder="Optional">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="transaction_reference">Transaction Reference</label>
                    <input type="text" name="transaction_reference" id="transaction_reference" value="{{ old('transaction_reference', $payment->transaction_reference) }}" placeholder="Optional - For bank transfers, mobile money, etc.">
                </div>
            </div>

            <!-- Manual Payment Details Section -->
            @php
                $manualDetails = old('manual_payment_details', $payment->manual_payment_details ?? []);
            @endphp
            <div id="manual_payment_details_section" style="display: {{ old('payment_method', $payment->payment_method) === 'Manual Payment' ? 'block' : 'none' }}; border: 1px solid #e0e0e0; padding: 20px; margin: 20px 0; border-radius: 8px; background: #f9f9f9;">
                <h3 style="margin-top: 0; margin-bottom: 15px; color: #667eea;">Manual Payment Details</h3>
                <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Enter payment instructions and bank account details for manual payments.</p>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="manual_bank_name">Bank Name</label>
                        <input type="text" name="manual_payment_details[bank_name]" id="manual_bank_name" value="{{ old('manual_payment_details.bank_name', $manualDetails['bank_name'] ?? '') }}" placeholder="e.g., First Bank, GTBank">
                    </div>
                    <div class="form-group">
                        <label for="manual_account_name">Account Name</label>
                        <input type="text" name="manual_payment_details[account_name]" id="manual_account_name" value="{{ old('manual_payment_details.account_name', $manualDetails['account_name'] ?? '') }}" placeholder="Account holder name">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="manual_account_number">Account Number</label>
                        <input type="text" name="manual_payment_details[account_number]" id="manual_account_number" value="{{ old('manual_payment_details.account_number', $manualDetails['account_number'] ?? '') }}" placeholder="Account number">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="manual_instructions">Payment Instructions</label>
                        <textarea name="manual_payment_details[instructions]" id="manual_instructions" rows="4" placeholder="Enter payment instructions for the trainee...">{{ old('manual_payment_details.instructions', $manualDetails['instructions'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Additional notes about this payment">{{ old('notes', $payment->notes) }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Payment</button>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-group textarea {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
    transition: border-color 0.2s;
    width: 100%;
    resize: vertical;
}

.form-group textarea:focus {
    outline: none;
    border-color: #6B46C1;
}
</style>

<script>
function toggleManualPaymentDetails() {
    const paymentMethod = document.getElementById('payment_method').value;
    const manualSection = document.getElementById('manual_payment_details_section');
    
    if (paymentMethod === 'Manual Payment') {
        manualSection.style.display = 'block';
    } else {
        manualSection.style.display = 'none';
    }
}

// Check on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleManualPaymentDetails();
});
</script>
@endsection

