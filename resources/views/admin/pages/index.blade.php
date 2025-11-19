@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div class="page-title-actions">
        <h1 class="page-title"><i class="fas fa-file-alt"></i> Pages Management</h1>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Page
        </a>
    </div>
</div>

<div class="page-content">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="content-section">
        <h2 class="section-title">All Pages</h2>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $index => $page)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $page->title }}</strong></td>
                        <td><code>{{ $page->slug }}</code></td>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($page->page_type) }}</span>
                        </td>
                        <td>
                            <span class="status-badge {{ $page->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $page->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $page->order }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.pages.show', $page->id) }}" class="action-btn view-btn" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="action-btn edit-btn" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No pages found. <a href="{{ route('admin.pages.create') }}">Create one now</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

