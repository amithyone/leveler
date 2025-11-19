@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1 class="page-title">Activate Trainees</h1>
</div>

<div class="page-content">
    <div class="content-section">
        <h2 class="section-title">Activate Trainees</h2>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.trainees.activate') }}">
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
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trainees as $index => $trainee)
                        <tr>
                            <td>
                                <input type="checkbox" name="trainee_ids[]" value="{{ $trainee->id }}" 
                                    {{ $trainee->has_payment ? '' : 'disabled' }} 
                                    title="{{ $trainee->has_payment ? '' : 'No completed payment found' }}">
                            </td>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $trainee->full_name }}</td>
                            <td>{{ $trainee->gender }}</td>
                            <td>{{ $trainee->username }}</td>
                            <td>
                                @if($trainee->has_payment)
                                    <span class="status-badge status-active">Paid</span>
                                @else
                                    <span class="status-badge status-inactive">Not Paid</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-inactive">{{ $trainee->status }}</span>
                            </td>
                            <td class="actions-cell">
                                @if($trainee->has_payment && $trainee->status === 'Active')
                                <a href="{{ route('admin.trainees.view-as', $trainee->id) }}" class="action-btn" title="View As Trainee" style="color: #667eea;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Activate Selected Trainees</button>
                <a href="{{ route('admin.payments.create') }}" class="btn btn-secondary">Record Payment</a>
            </div>
            @else
            <p>No inactive trainees found.</p>
            @endif
        </form>
    </div>
</div>

<script>
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="trainee_ids[]"]:not(:disabled)');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
