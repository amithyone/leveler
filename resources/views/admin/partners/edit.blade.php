@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Partner</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Edit Partner</h2>

        @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.partners.update', $partner->id) }}" enctype="multipart/form-data" class="trainee-form">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="name">Partner Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $partner->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $partner->display_order) }}" min="0">
                    <small class="form-text">Lower numbers appear first</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="4" placeholder="Brief description about the partner">{{ old('description', $partner->description) }}</textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="website">Website URL</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $partner->website) }}" placeholder="https://example.com">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="logo">Partner Logo</label>
                    @if($partner->logo)
                    <div class="current-image" style="margin-bottom: 15px;">
                        <img src="{{ Storage::url($partner->logo) }}" alt="Current Logo" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 2px solid #e0e0e0; display: block; margin-bottom: 10px;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remove_logo" value="1">
                            <span>Remove current logo</span>
                        </label>
                    </div>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/*" onchange="previewImage(this, 'logo_preview')">
                    <small class="form-text">Recommended: PNG or JPG, max 2MB. Logo will be displayed at max 200px width.</small>
                    <div id="logo_preview" class="image-preview" style="margin-top: 15px; display: none;">
                        <img src="" alt="Logo Preview" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 2px solid #e0e0e0;">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $partner->is_active) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                    <small class="form-text">Only active partners will be displayed on the frontend</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Partner</button>
                <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>

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

