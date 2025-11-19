@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Record Payment</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Record New Payment</h2>

        @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.payments.store') }}" class="trainee-form">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="trainee_id">Trainee *</label>
                    <select name="trainee_id" id="trainee_id" required>
                        <option value="">Select Trainee</option>
                        @foreach($trainees as $trainee)
                        <option value="{{ $trainee->id }}" {{ old('trainee_id') == $trainee->id ? 'selected' : '' }}>
                            {{ $trainee->full_name }} ({{ $trainee->username }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="amount">Amount (₦) *</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="payment_method">Payment Method *</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Mobile Money" {{ old('payment_method') === 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="Card" {{ old('payment_method') === 'Card' ? 'selected' : '' }}>Card</option>
                        <option value="Other" {{ old('payment_method') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_date">Payment Date *</label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">Payment Status *</label>
                    <select name="status" id="status" required>
                        <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Failed" {{ old('status') === 'Failed' ? 'selected' : '' }}>Failed</option>
                        <option value="Refunded" {{ old('status') === 'Refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="receipt_number">Receipt Number</label>
                    <input type="text" name="receipt_number" id="receipt_number" value="{{ old('receipt_number') }}" placeholder="Optional">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="transaction_reference">Transaction Reference</label>
                    <input type="text" name="transaction_reference" id="transaction_reference" value="{{ old('transaction_reference') }}" placeholder="Optional - For bank transfers, mobile money, etc.">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Additional notes about this payment">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Record Payment</button>
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
@endsection

