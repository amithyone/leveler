@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Partners Management</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title">All Partners</h2>
            <a href="{{ route('admin.partners.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Partner
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if($partners->count() > 0)
        <div class="table-container">
            <table class="trainee-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Website</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partners as $index => $partner)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($partner->logo)
                            <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" style="max-width: 80px; max-height: 60px; object-fit: contain; border-radius: 4px;">
                            @else
                            <span style="color: #999;">No logo</span>
                            @endif
                        </td>
                        <td><strong>{{ $partner->name }}</strong></td>
                        <td>{{ Str::limit($partner->description ?? 'N/A', 50) }}</td>
                        <td>
                            @if($partner->website)
                            <a href="{{ $partner->website }}" target="_blank" rel="noopener noreferrer" style="color: #667eea;">
                                {{ Str::limit($partner->website, 30) }} <i class="fas fa-external-link-alt"></i>
                            </a>
                            @else
                            <span style="color: #999;">N/A</span>
                            @endif
                        </td>
                        <td>{{ $partner->display_order }}</td>
                        <td>
                            <span class="status-badge {{ $partner->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $partner->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.partners.edit', $partner->id) }}" class="action-btn" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('admin.partners.destroy', $partner->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this partner?');">
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
        @else
        <p style="padding: 40px; text-align: center; color: #666;">
            No partners found. <a href="{{ route('admin.partners.create') }}">Add a partner</a>
        </p>
        @endif
    </div>
</div>
@endsection

