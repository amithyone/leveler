@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Manual Payment Management</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title">Payment Settings</h2>
            <a href="{{ route('admin.manual-payments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Payment Setting
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if($settings->count() > 0)
        <div class="table-container">
            <table class="trainee-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Bank Name</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings as $index => $setting)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $setting->name }}</strong></td>
                        <td>{{ $setting->bank_name ?? 'N/A' }}</td>
                        <td>{{ $setting->account_name ?? 'N/A' }}</td>
                        <td>{{ $setting->account_number ?? 'N/A' }}</td>
                        <td>
                            <span class="status-badge {{ $setting->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $setting->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $setting->display_order }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.manual-payments.edit', $setting->id) }}" class="action-btn" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('admin.manual-payments.destroy', $setting->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this payment setting?');">
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
            No payment settings found. <a href="{{ route('admin.manual-payments.create') }}">Add a payment setting</a>
        </p>
        @endif
    </div>
</div>
@endsection

