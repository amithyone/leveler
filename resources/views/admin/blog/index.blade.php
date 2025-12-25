@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title"><i class="fas fa-blog"></i> Blog Posts</h1>
            <p style="margin: 0; color: #666;">Manage your blog posts and articles</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.blog.categories') }}" class="btn btn-secondary">
                <i class="fas fa-folder"></i> Categories
            </a>
            <a href="{{ route('admin.blog.tags') }}" class="btn btn-secondary">
                <i class="fas fa-tags"></i> Tags
            </a>
            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Post
            </a>
        </div>
    </div>
</div>

<div class="page-content">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="content-section">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.blog.index') }}" style="margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search posts...">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        @if($posts->count() > 0)
        <div class="table-container">
            <table class="trainee-table">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $index => $post)
                    <tr>
                        <td>{{ ($posts->currentPage() - 1) * $posts->perPage() + $index + 1 }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                @if($post->featured_image)
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                @endif
                                <div>
                                    <strong>{{ $post->title }}</strong>
                                    @if($post->is_featured)
                                    <span class="badge badge-info" style="margin-left: 5px;">Featured</span>
                                    @endif
                                    <br>
                                    <small style="color: #666;">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 60) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($post->category)
                            <span class="badge badge-secondary">{{ $post->category->name }}</span>
                            @else
                            <span style="color: #999;">Uncategorized</span>
                            @endif
                        </td>
                        <td>{{ $post->author->name ?? 'N/A' }}</td>
                        <td>
                            <span class="status-badge 
                                {{ $post->status === 'published' ? 'status-active' : ($post->status === 'draft' ? 'status-pending' : 'status-inactive') }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </td>
                        <td>{{ number_format($post->views) }}</td>
                        <td>
                            @if($post->published_at)
                            {{ $post->published_at->format('M d, Y') }}
                            @else
                            <span style="color: #999;">Not published</span>
                            @endif
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.blog.show', $post->id) }}" class="action-btn" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.blog.edit', $post->id) }}" class="action-btn" title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('admin.blog.destroy', $post->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
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

        <!-- Pagination -->
        <div style="margin-top: 30px;">
            {{ $posts->links() }}
        </div>
        @else
        <div style="padding: 60px; text-align: center; color: #666;">
            <i class="fas fa-blog" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
            <p style="font-size: 18px; margin-bottom: 10px;">No blog posts found</p>
            <p style="margin-bottom: 30px;">Get started by creating your first blog post</p>
            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Your First Post
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

