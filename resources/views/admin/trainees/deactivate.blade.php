@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Deactivate Trainees</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Deactivate Trainees</h2>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.trainees.deactivate') }}">
            @csrf
            @method('POST')
            
            @if($trainees->count() > 0)
            <div class="table-container">
                <table class="trainee-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trainees as $index => $trainee)
                        <tr>
                            <td><input type="checkbox" name="trainee_ids[]" value="{{ $trainee->id }}"></td>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $trainee->full_name }}</td>
                            <td>{{ $trainee->gender }}</td>
                            <td>{{ $trainee->username }}</td>
                            <td>
                                <span class="status-badge status-active">{{ $trainee->status }}</span>
                            </td>
                            <td class="actions-cell">
                                <a href="{{ route('admin.trainees.view-as', $trainee->id) }}" class="action-btn view-as-btn" title="View As Trainee">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Deactivate Selected</button>
            </div>
            @else
            <p>No active trainees found.</p>
            @endif
        </form>
    </div>
</div>

<script>
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="trainee_ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
