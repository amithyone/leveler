@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="{{ route('admin.schedules.index') }}" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Create Schedule
            </h1>
            <p style="margin: 0; color: #666;">Add a new training schedule</p>
        </div>
    </div>
</div>

<div class="page-content">
    @if($errors->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <ul style="margin: 10px 0 0 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="content-section">
        <form action="{{ route('admin.schedules.store') }}" method="POST">
            @csrf

            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-book"></i> Course Information
                </h3>

                <div class="form-group">
                    <label for="course_id">Course <span class="required">*</span></label>
                    <select name="course_id" id="course_id" class="form-control" required>
                        <option value="">Select a course</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->code }} - {{ $course->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-calendar"></i> Date & Time
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date <span class="required">*</span></label>
                        <input type="date" name="start_date" id="start_date" class="form-control" 
                               value="{{ old('start_date') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date <span class="required">*</span></label>
                        <input type="date" name="end_date" id="end_date" class="form-control" 
                               value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time">Start Time <span class="required">*</span></label>
                        <input type="time" name="start_time" id="start_time" class="form-control" 
                               value="{{ old('start_time') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time <span class="required">*</span></label>
                        <input type="time" name="end_time" id="end_time" class="form-control" 
                               value="{{ old('end_time') }}" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Venue & Status
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="venue">Venue</label>
                        <input type="text" name="venue" id="venue" class="form-control" 
                               value="{{ old('venue') }}" placeholder="Training venue location">
                    </div>

                    <div class="form-group">
                        <label for="status">Status <span class="required">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Scheduled" {{ old('status') === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="Ongoing" {{ old('status') === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ old('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.content-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}

.form-section:last-of-type {
    border-bottom: none;
}

.form-section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #f0f0f0;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 2px solid #ef4444;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

