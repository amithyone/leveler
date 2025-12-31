@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h1 class="page-title">View Email</h1>
        <div class="header-actions">
            @if($email)
                <a href="{{ route('admin.mail.compose', ['to' => $email['from'] ?? '', 'subject' => 'Re: ' . ($email['subject'] ?? '')]) }}" class="btn btn-primary">
                    <i class="fas fa-reply"></i> Reply
                </a>
                <form action="{{ route('admin.mail.delete', $email['number']) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="folder" value="{{ $folder ?? 'INBOX' }}">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this email?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.mail.index', ['folder' => $folder ?? 'INBOX']) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to {{ $folder ?? 'INBOX' }}
            </a>
        </div>
    </div>
</div>

<div class="page-content">
    @if($error)
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    @elseif($email)
        <div class="mail-view-container">
            <div class="mail-header">
                <div class="mail-header-top">
                    <div class="mail-subject-large">
                        {{ $email['subject'] }}
                    </div>
                </div>
                <div class="mail-header-info">
                    <div class="mail-info-row">
                        <span class="info-label">From:</span>
                        <span class="info-value">
                            @if($email['from_name'])
                                {{ $email['from_name'] }} &lt;{{ $email['from'] }}&gt;
                            @else
                                {{ $email['from'] }}
                            @endif
                        </span>
                    </div>
                    <div class="mail-info-row">
                        <span class="info-label">To:</span>
                        <span class="info-value">{{ implode(', ', $email['to']) }}</span>
                    </div>
                    @if(count($email['cc']) > 0)
                        <div class="mail-info-row">
                            <span class="info-label">CC:</span>
                            <span class="info-value">{{ implode(', ', $email['cc']) }}</span>
                        </div>
                    @endif
                    <div class="mail-info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($email['date'])->format('F d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            @if(count($email['attachments']) > 0)
                <div class="mail-attachments">
                    <h4><i class="fas fa-paperclip"></i> Attachments</h4>
                    <ul>
                        @foreach($email['attachments'] as $attachment)
                            <li>
                                <i class="fas fa-file"></i> {{ $attachment['filename'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mail-body">
                {!! $email['body'] !!}
            </div>

            <div class="mail-footer-actions">
                <a href="{{ route('admin.mail.compose', ['to' => $email['from'] ?? '', 'subject' => 'Re: ' . ($email['subject'] ?? '')]) }}" class="btn btn-primary">
                    <i class="fas fa-reply"></i> Reply
                </a>
                <a href="{{ route('admin.mail.compose', ['to' => '', 'subject' => 'Fwd: ' . ($email['subject'] ?? '')]) }}" class="btn btn-secondary">
                    <i class="fas fa-share"></i> Forward
                </a>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-envelope-open"></i>
            <p>Email not found</p>
        </div>
    @endif
</div>

<style>
.mail-view-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.mail-header {
    padding: 25px 30px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.mail-header-top {
    margin-bottom: 15px;
}

.mail-subject-large {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
}

.mail-header-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.mail-info-row {
    display: flex;
    gap: 10px;
    font-size: 14px;
}

.info-label {
    font-weight: 600;
    color: #6b7280;
    min-width: 60px;
}

.info-value {
    color: #374151;
}

.mail-attachments {
    padding: 20px 30px;
    border-bottom: 1px solid #e5e7eb;
    background: #fef3c7;
}

.mail-attachments h4 {
    margin: 0 0 10px 0;
    font-size: 16px;
    color: #92400e;
}

.mail-attachments ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mail-attachments li {
    padding: 8px 0;
    color: #78350f;
}

.mail-body {
    padding: 30px;
    min-height: 200px;
    line-height: 1.6;
    color: #374151;
}

.mail-body img {
    max-width: 100%;
    height: auto;
}

.mail-footer-actions {
    padding: 20px 30px;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    gap: 10px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.delete-form {
    display: inline;
}
</style>
@endsection
