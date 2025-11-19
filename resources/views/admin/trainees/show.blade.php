@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 class="page-title">
                <a href="{{ route('admin.trainees.index') }}" style="color: #667eea; text-decoration: none; margin-right: 10px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Trainee Profile
            </h1>
            <p style="margin: 0; color: #666;">{{ $trainee->full_name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.trainees.edit', $trainee->id) }}" class="btn btn-primary">
                <i class="fas fa-pencil-alt"></i> Edit Profile
            </a>
            @if($trainee->status === 'Active')
            <a href="{{ route('admin.trainees.view-as', $trainee->id) }}" class="btn btn-secondary">
                <i class="fas fa-user-secret"></i> View As Trainee
            </a>
            @endif
            <a href="{{ route('admin.payments.create', ['trainee_id' => $trainee->id]) }}" class="btn btn-success">
                <i class="fas fa-money-bill"></i> Record Payment
            </a>
        </div>
    </div>
</div>

<div class="page-content">
    @if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Personal Information -->
    <div class="content-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-user"></i> Personal Information
            </h2>
            <span class="status-badge {{ $trainee->status === 'Active' ? 'status-active' : 'status-inactive' }}">
                {{ $trainee->status }}
            </span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <label>Full Name</label>
                <div class="info-value">{{ $trainee->full_name }}</div>
            </div>

            <div class="info-item">
                <label>Surname</label>
                <div class="info-value">{{ $trainee->surname }}</div>
            </div>

            <div class="info-item">
                <label>First Name</label>
                <div class="info-value">{{ $trainee->first_name }}</div>
            </div>

            @if($trainee->middle_name)
            <div class="info-item">
                <label>Middle Name</label>
                <div class="info-value">{{ $trainee->middle_name }}</div>
            </div>
            @endif

            <div class="info-item">
                <label>Gender</label>
                <div class="info-value">{{ $trainee->gender === 'M' ? 'Male' : 'Female' }}</div>
            </div>

            <div class="info-item">
                <label>Phone Number</label>
                <div class="info-value">{{ $trainee->phone_number }}</div>
            </div>

            <div class="info-item">
                <label>Username</label>
                <div class="info-value">
                    <code style="background: #f0f0f0; padding: 6px 12px; border-radius: 6px; font-size: 14px;">
                        {{ $trainee->username }}
                    </code>
                </div>
            </div>

            <div class="info-item">
                <label>Password</label>
                <div class="info-value">
                    <code style="background: #f0f0f0; padding: 6px 12px; border-radius: 6px; font-size: 14px;">
                        {{ $trainee->password }}
                    </code>
                    <button onclick="copyToClipboard('{{ $trainee->password }}')" class="btn-copy" title="Copy Password">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="content-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-money-bill-wave"></i> Payment Information
            </h2>
        </div>

        <div class="payment-summary">
            <div class="payment-card">
                <div class="payment-label">Payment Status</div>
                <div class="payment-value">
                    @if($trainee->has_payment)
                        <span class="status-badge status-active">
                            <i class="fas fa-check-circle"></i> Paid
                        </span>
                    @else
                        <span class="status-badge status-inactive">
                            <i class="fas fa-times-circle"></i> Not Paid
                        </span>
                    @endif
                </div>
            </div>

            @if($trainee->package_type)
            <div class="payment-card">
                <div class="payment-label">Package Type</div>
                <div class="payment-value">
                    <span class="badge badge-info">
                        {{ $trainee->package_type === 'package' ? '4 Courses Package' : 'Single Course' }}
                    </span>
                </div>
            </div>
            @endif

            <div class="payment-card">
                <div class="payment-label">Total Paid</div>
                <div class="payment-value">₦{{ number_format($trainee->total_paid_amount, 2) }}</div>
            </div>

            @if($trainee->total_required > 0)
            <div class="payment-card">
                <div class="payment-label">Total Required</div>
                <div class="payment-value">₦{{ number_format($trainee->total_required, 2) }}</div>
            </div>

            <div class="payment-card">
                <div class="payment-label">Remaining Balance</div>
                <div class="payment-value" style="color: {{ $trainee->remaining_balance > 0 ? '#f59e0b' : '#10b981' }};">
                    ₦{{ number_format($trainee->remaining_balance, 2) }}
                </div>
            </div>

            <div class="payment-card full-width">
                <div class="payment-label">Payment Progress</div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: {{ $trainee->payment_progress }}%;">
                        {{ number_format($trainee->payment_progress, 1) }}%
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment History -->
        @if($trainee->payments->count() > 0)
        <div style="margin-top: 30px;">
            <h3 style="margin-bottom: 15px; color: #333;">Payment History</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Status</th>
                            <th>Package</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trainee->payments->sortByDesc('payment_date') as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td>₦{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>
                                <code style="font-size: 11px;">{{ $payment->transaction_reference ?? 'N/A' }}</code>
                            </td>
                            <td>
                                <span class="status-badge {{ $payment->status === 'Completed' ? 'status-active' : 'status-inactive' }}">
                                    {{ $payment->status }}
                                </span>
                            </td>
                            <td>
                                @if($payment->package_type)
                                    <span class="badge badge-info" style="font-size: 11px;">
                                        {{ $payment->package_type === 'package' ? '4 Courses' : '1 Course' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Course Access -->
    <div class="content-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-book"></i> Course Access
            </h2>
            <span class="badge badge-secondary">{{ $trainee->accessibleCourses->count() }} Courses</span>
        </div>

        @if($trainee->accessibleCourses->count() > 0)
        <div class="courses-grid">
            @foreach($trainee->accessibleCourses as $course)
            <div class="course-card">
                <div class="course-code-badge">{{ $course->code }}</div>
                <h4>{{ $course->title }}</h4>
                <div class="course-meta">
                    <span><i class="fas fa-clock"></i> {{ $course->duration_hours ?? 'N/A' }} hrs</span>
                    <span><i class="fas fa-check-circle"></i> {{ $course->pivot->granted_at ? $course->pivot->granted_at->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state-small">
            <i class="fas fa-book-open"></i>
            <p>No course access granted yet.</p>
        </div>
        @endif
    </div>

    <!-- Assessment Results -->
    @if($trainee->myResults->count() > 0)
    <div class="content-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-poll"></i> Assessment Results
            </h2>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainee->myResults->sortByDesc('completed_at') as $result)
                    <tr>
                        <td>
                            <strong>{{ $result->course->code }}</strong>
                            <br>
                            <small style="color: #666;">{{ $result->course->title }}</small>
                        </td>
                        <td>
                            <strong>{{ $result->score }} / {{ $result->total_questions }}</strong>
                            <br>
                            <small style="color: #666;">{{ number_format($result->percentage, 1) }}%</small>
                        </td>
                        <td>
                            <span class="status-badge {{ $result->status === 'passed' ? 'status-active' : 'status-inactive' }}">
                                {{ ucfirst($result->status) }}
                            </span>
                        </td>
                        <td>{{ $result->completed_at ? $result->completed_at->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<style>
.content-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item label {
    font-size: 12px;
    color: #666;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 16px;
    color: #333;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-copy {
    background: #f0f0f0;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
}

.btn-copy:hover {
    background: #667eea;
    color: white;
}

.payment-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.payment-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 2px solid #e0e0e0;
}

.payment-card.full-width {
    grid-column: 1 / -1;
}

.payment-label {
    font-size: 12px;
    color: #666;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.payment-value {
    font-size: 18px;
    font-weight: 700;
    color: #333;
}

.progress-bar-container {
    background: #e0e0e0;
    border-radius: 10px;
    height: 30px;
    overflow: hidden;
    margin-top: 10px;
}

.progress-bar {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
    transition: width 0.3s;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
}

.course-card {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.2s;
}

.course-card:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.course-code-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 11px;
    display: inline-block;
    margin-bottom: 10px;
}

.course-card h4 {
    font-size: 14px;
    color: #333;
    margin: 10px 0;
    font-weight: 600;
}

.course-meta {
    display: flex;
    gap: 15px;
    font-size: 12px;
    color: #666;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.badge-secondary {
    background: #e5e7eb;
    color: #374151;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.data-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.data-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
}

.data-table td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

.empty-state-small {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.empty-state-small i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
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

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-success {
    background: #10b981;
    color: white;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 2px solid #10b981;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .payment-summary {
        grid-template-columns: 1fr;
    }
    
    .courses-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Password copied to clipboard!');
    }, function() {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Password copied to clipboard!');
    });
}
</script>
@endsection

