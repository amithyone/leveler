@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="page-title"><i class="fas fa-tags"></i> Blog Tags</h1>
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
        <h2 class="section-title">Add New Tag</h2>
        <form method="POST" action="{{ route('admin.blog.tags.store') }}" class="default-form" style="margin-bottom: 40px;">
            @csrf
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="name">Tag Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g., Technology, Business">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">Add Tag</button>
                </div>
            </div>
        </form>

        <h2 class="section-title">All Tags</h2>
        @if($tags->count() > 0)
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @foreach($tags as $tag)
            <div style="display: flex; align-items: center; gap: 10px; padding: 10px 15px; background: #f9f9f9; border-radius: 20px; border: 1px solid #e0e0e0;">
                <span><strong>{{ $tag->name }}</strong></span>
                <code style="font-size: 12px; color: #666;">{{ $tag->slug }}</code>
                <span style="color: #666; font-size: 12px;">({{ $tag->posts->count() }} posts)</span>
                <form action="{{ route('admin.blog.tags.destroy', $tag->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 5px;" title="Delete">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <p style="padding: 40px; text-align: center; color: #666;">No tags found. Add one above.</p>
        @endif
    </div>
</div>
@endsection

