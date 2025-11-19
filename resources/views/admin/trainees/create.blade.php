@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Trainee</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Add New Trainee</h2>

        @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.trainees.store') }}" class="trainee-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="surname">Surname *</label>
                    <input type="text" id="surname" name="surname" value="{{ old('surname') }}" required>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                </div>
                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Male</option>
                        <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone_number">Phone Number *</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                </div>
                <div class="form-group">
                    <label for="username">Username (Auto-generated if empty)</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="BCD/XXXXXX">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password (Auto-generated if empty)</label>
                    <input type="text" id="password" name="password" value="{{ old('password') }}" placeholder="Leave empty for auto-generation">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Trainee</button>
                <a href="{{ route('admin.trainees.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

