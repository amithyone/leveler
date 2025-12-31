@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h1 class="page-title">Mail Inbox</h1>
        <a href="{{ route('admin.mail.compose') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Compose
        </a>
    </div>
</div>

<div class="page-content">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($error)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> {{ $error }}
        </div>
    @endif

    <div class="mail-container">
        <div class="mail-toolbar">
            <div class="mail-folder">
                <span class="folder-name">
                    <i class="fas fa-inbox"></i> {{ $folder }}
                </span>
                <span class="folder-count">({{ $totalEmails }} emails)</span>
            </div>
            <div class="mail-actions">
                <a href="{{ route('admin.mail.index', ['folder' => $folder, 'page' => 1]) }}" class="btn btn-sm">
                    <i class="fas fa-sync-alt"></i> Refresh
                </a>
            </div>
        </div>

        @if(count($emails) > 0)
            <div class="mail-list">
                @foreach($emails as $email)
                    <div class="mail-item {{ !$email['seen'] ? 'unread' : '' }}">
                        <div class="mail-checkbox">
                            <input type="checkbox" value="{{ $email['number'] }}">
                        </div>
                        <a href="{{ route('admin.mail.view', $email['number']) }}" class="mail-link">
                            <div class="mail-from">
                                <strong>{{ $email['from_name'] ?: $email['from'] }}</strong>
                                <span class="mail-address">{{ $email['from'] }}</span>
                            </div>
                            <div class="mail-subject">
                                {{ $email['subject'] }}
                            </div>
                            <div class="mail-date">
                                {{ \Carbon\Carbon::parse($email['date'])->format('M d, Y h:i A') }}
                            </div>
                            @if(!$email['seen'])
                                <span class="mail-unread-badge"></span>
                            @endif
                        </a>
                        <div class="mail-actions-item">
                            <form action="{{ route('admin.mail.delete', $email['number']) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon" onclick="return confirm('Are you sure you want to delete this email?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($totalPages > 1)
                <div class="pagination">
                    @if($page > 1)
                        <a href="{{ route('admin.mail.index', ['folder' => $folder, 'page' => $page - 1]) }}" class="btn">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    @endif
                    
                    <span class="page-info">Page {{ $page }} of {{ $totalPages }}</span>
                    
                    @if($page < $totalPages)
                        <a href="{{ route('admin.mail.index', ['folder' => $folder, 'page' => $page + 1]) }}" class="btn">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No emails found</p>
            </div>
        @endif
    </div>
</div>

<style>
.mail-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.mail-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.mail-folder {
    display: flex;
    align-items: center;
    gap: 10px;
}

.folder-name {
    font-weight: 600;
    color: #374151;
}

.folder-count {
    color: #6b7280;
    font-size: 14px;
}

.mail-list {
    max-height: 600px;
    overflow-y: auto;
}

.mail-item {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
    transition: background 0.2s;
}

.mail-item:hover {
    background: #f9fafb;
}

.mail-item.unread {
    background: #eff6ff;
    font-weight: 500;
}

.mail-checkbox {
    margin-right: 15px;
}

.mail-link {
    flex: 1;
    display: grid;
    grid-template-columns: 200px 1fr auto;
    gap: 15px;
    text-decoration: none;
    color: inherit;
}

.mail-from {
    display: flex;
    flex-direction: column;
}

.mail-address {
    font-size: 12px;
    color: #6b7280;
    margin-top: 2px;
}

.mail-subject {
    color: #374151;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mail-date {
    color: #6b7280;
    font-size: 14px;
    text-align: right;
}

.mail-unread-badge {
    width: 8px;
    height: 8px;
    background: #3b82f6;
    border-radius: 50%;
    margin-left: 10px;
}

.mail-actions-item {
    margin-left: 15px;
}

.btn-icon {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 5px;
    transition: color 0.2s;
}

.btn-icon:hover {
    color: #ef4444;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #d1d5db;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-top: 1px solid #e5e7eb;
}

.page-info {
    color: #6b7280;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}
</style>
@endsection
