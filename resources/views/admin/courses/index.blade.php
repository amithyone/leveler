@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Courses</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <p>Redirecting to courses view...</p>
        <script>
            window.location.href = "{{ route('admin.courses.view') }}";
        </script>
    </div>
</div>
@endsection

