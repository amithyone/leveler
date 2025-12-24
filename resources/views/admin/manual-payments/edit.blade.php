@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Payment Setting</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Edit Payment Setting</h2>

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
                    <label for="name">Setting Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $setting->name) }}" placeholder="e.g., Main Bank Account, Mobile Money" required>
                    <small class="form-text">A descriptive name for this payment setting</small>
                </div>

                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $setting->display_order) }}" min="0" placeholder="0">
                    <small class="form-text">Lower numbers appear first</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="bank_name">Bank Name</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $setting->bank_name) }}" placeholder="e.g., First Bank, GTBank">
                </div>

                <div class="form-group">
                    <label for="account_name">Account Name</label>
                    <input type="text" name="account_name" id="account_name" value="{{ old('account_name', $setting->account_name) }}" placeholder="Account holder name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="account_number">Account Number</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $setting->account_number) }}" placeholder="Account number">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="instructions">Payment Instructions</label>
                    <textarea name="instructions" id="instructions" rows="5" placeholder="Enter payment instructions for trainees...">{{ old('instructions', $setting->instructions) }}</textarea>
                    <small class="form-text">Instructions that will be shown to trainees when making payments</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $setting->is_active) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                    <small class="form-text">Only active settings will be available for use</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Setting</button>
                <a href="{{ route('admin.manual-payments.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

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

