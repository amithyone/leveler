@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Compose Email</h1>
</div>

<div class="page-content">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="mail-compose-container">
        <form action="{{ route('admin.mail.send') }}" method="POST" class="mail-compose-form">
            @csrf
            
            <div class="form-group">
                <label for="to">To <span class="required">*</span></label>
                <input type="email" name="to" id="to" class="form-control" value="{{ old('to', $to) }}" required>
                @error('to')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cc">CC</label>
                <input type="email" name="cc" id="cc" class="form-control" value="{{ old('cc') }}">
                @error('cc')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="bcc">BCC</label>
                <input type="email" name="bcc" id="bcc" class="form-control" value="{{ old('bcc') }}">
                @error('bcc')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="subject">Subject <span class="required">*</span></label>
                <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject', $subject) }}" required>
                @error('subject')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="body">Message <span class="required">*</span></label>
                <textarea name="body" id="body" class="form-control" rows="15" required>{{ old('body') }}</textarea>
                @error('body')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.mail.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Email
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.mail-compose-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 30px;
}

.mail-compose-form {
    max-width: 100%;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

textarea.form-control {
    resize: vertical;
    font-family: inherit;
}

.error-message {
    display: block;
    color: #ef4444;
    font-size: 14px;
    margin-top: 5px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}
</style>
@endsection
