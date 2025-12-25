@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="page-title"><i class="fas fa-folder"></i> Blog Categories</h1>
        <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Posts
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
        <h2 class="section-title">Add New Category</h2>
        <form method="POST" action="{{ route('admin.blog.categories.store') }}" class="default-form" style="margin-bottom: 40px;">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="order">Order</label>
                    <input type="number" id="order" name="order" class="form-control" value="{{ old('order', 0) }}">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
        </form>

        <h2 class="section-title">All Categories</h2>
        @if($categories->count() > 0)
        <div class="table-container">
            <table class="trainee-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Posts</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $index => $category)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td><code>{{ $category->slug }}</code></td>
                        <td>{{ Str::limit($category->description ?? 'N/A', 50) }}</td>
                        <td>{{ $category->order }}</td>
                        <td>
                            <span class="status-badge {{ $category->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $category->posts->count() }}</td>
                        <td class="actions-cell">
                            <form action="{{ route('admin.blog.categories.destroy', $category->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This will not delete posts in this category.');">
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
        <p style="padding: 40px; text-align: center; color: #666;">No categories found. Add one above.</p>
        @endif
    </div>
</div>
@endsection

