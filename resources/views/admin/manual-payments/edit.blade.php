@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Payment Account</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Edit Payment Account</h2>

        @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.manual-payments.update', $setting->id) }}" class="trainee-form">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Payment Account Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $setting->name) }}" required placeholder="e.g., Main Account">
                    <small style="display: block; margin-top: 5px; color: #666;">Display name for this payment option</small>
                </div>

                <div class="form-group">
                    <label for="bank_name">Bank Name *</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $setting->bank_name) }}" required placeholder="e.g., Access Bank">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="account_name">Bank Account Name *</label>
                    <input type="text" name="account_name" id="account_name" value="{{ old('account_name', $setting->account_name) }}" required placeholder="e.g., Leveler CC">
                    <small style="display: block; margin-top: 5px; color: #666;">Name on the bank account</small>
                </div>

                <div class="form-group">
                    <label for="account_number">Bank Account Number *</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $setting->account_number) }}" required placeholder="e.g., 1234567890">
                    <small style="display: block; margin-top: 5px; color: #666;">Bank account number</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="payment_instructions">Payment Instructions</label>
                    <textarea name="payment_instructions" id="payment_instructions" rows="4" placeholder="Enter payment instructions for trainees...">{{ old('payment_instructions', $setting->payment_instructions) }}</textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="order">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $setting->order) }}" min="0" placeholder="0">
                    <small style="display: block; margin-top: 5px; color: #666;">Lower numbers appear first</small>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $setting->is_active) ? 'checked' : '' }}>
                        <span>Active (Show to trainees)</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Payment Account
                </button>
                <a href="{{ route('admin.manual-payments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

