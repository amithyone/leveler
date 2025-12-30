@extends('layouts.frontend')

@section('title', $course->title . ' - Leveler')

@section('content')
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb" style="margin-bottom: 15px;">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('courses') }}">Courses</a>
            <span>/</span>
            <span>{{ $course->title }}</span>
        </nav>
        <h1>{{ $course->title }}</h1>
        <p style="margin-top: 10px; color: #ffffff;">Course Code: {{ $course->code }}</p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="course-details-layout">
            <div class="course-details-main">
                <div class="course-header-section">
                    <div class="course-badges">
                        <span class="course-code">{{ $course->code }}</span>
                        <span class="course-status {{ $course->status === 'Active' ? 'active' : 'inactive' }}">
                            {{ $course->status }}
                        </span>
                    </div>
                </div>

                @if($course->description)
                <div class="course-section">
                    <h2>Course Description</h2>
                    <div class="course-description">
                        {!! nl2br(e($course->description)) !!}
                    </div>
                </div>
                @endif

                <div class="course-section">
                    <h2>Course Information</h2>
                    <div class="course-info-grid">
                        @if($course->duration_hours)
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Duration</strong>
                                <p>{{ $course->duration_hours }} hours</p>
                            </div>
                        </div>
                        @endif
                        <div class="info-item">
                            <i class="fas fa-question-circle"></i>
                            <div>
                                <strong>Questions</strong>
                                <p>{{ $course->questionPools()->count() }} questions available</p>
                            </div>
                        </div>
                        @if($course->schedules()->count() > 0)
                        <div class="info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <strong>Available Schedules</strong>
                                <p>{{ $course->schedules()->where('status', '!=', 'Cancelled')->count() }} schedule(s)</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($schedules->count() > 0)
                <div class="course-section">
                    <h2>Available Schedules</h2>
                    <div class="schedules-list">
                        @foreach($schedules as $schedule)
                        <div class="schedule-item">
                            <div class="schedule-date">
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <strong>{{ $schedule->start_date->format('M d, Y') }}</strong>
                                    @if($schedule->start_date->format('Y-m-d') !== $schedule->end_date->format('Y-m-d'))
                                        <span> - {{ $schedule->end_date->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="schedule-time">
                                <i class="fas fa-clock"></i>
                                <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}</span>
                            </div>
                            @if($schedule->venue)
                            <div class="schedule-venue">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $schedule->venue }}</span>
                            </div>
                            @endif
                            <div class="schedule-status">
                                <span class="status-badge status-{{ strtolower($schedule->status) }}">{{ $schedule->status }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($hasAccess && ($course->training_link || $course->whatsapp_link))
                <div class="course-section training-link-section">
                    <h2><i class="fas fa-video"></i> Course Resources</h2>
                    <p>Access your training materials and join the course community.</p>
                    
                    @if($course->training_link)
                    <div class="training-link-card" style="margin-bottom: 20px;">
                        <div class="training-link-content">
                            <i class="fas fa-link"></i>
                            <div>
                                <strong>Training Link</strong>
                                <p>Click below to access your training materials</p>
                            </div>
                        </div>
                        <a href="{{ $course->training_link }}" target="_blank" class="btn btn-primary btn-lg" rel="noopener noreferrer">
                            <i class="fas fa-external-link-alt"></i> Access Training
                        </a>
                    </div>
                    @endif
                    
                    @if($course->whatsapp_link)
                    <div class="training-link-card" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); border-color: #25D366;">
                        <div class="training-link-content">
                            <i class="fab fa-whatsapp" style="color: white;"></i>
                            <div style="color: white;">
                                <strong>WhatsApp Group</strong>
                                <p>Join the course WhatsApp group for discussions and updates</p>
                            </div>
                        </div>
                        <a href="{{ $course->whatsapp_link }}" target="_blank" class="btn btn-lg" style="background: white; color: #25D366; border: none;" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp"></i> Join Group
                        </a>
                    </div>
                    @endif
                </div>
                @endif

                <div class="course-section">
                    <h2>Ready to Get Started?</h2>
                    <p>Register now to enroll in this course and start your learning journey.</p>
                    <div class="course-actions">
                        <a href="{{ route('trainee.register') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus"></i> Register Now
                        </a>
                        <a href="{{ route('courses') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Back to Courses
                        </a>
                    </div>
                </div>
            </div>

            <div class="course-details-sidebar">
                <div class="sidebar-card">
                    <h3>Quick Info</h3>
                    <ul class="quick-info-list">
                        <li>
                            <i class="fas fa-code"></i>
                            <span><strong>Code:</strong> {{ $course->code }}</span>
                        </li>
                        @if($course->duration_hours)
                        <li>
                            <i class="fas fa-clock"></i>
                            <span><strong>Duration:</strong> {{ $course->duration_hours }}h</span>
                        </li>
                        @endif
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Status:</strong> {{ $course->status }}</span>
                        </li>
                        <li>
                            <i class="fas fa-question-circle"></i>
                            <span><strong>Questions:</strong> {{ $course->questionPools()->count() }}</span>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-card">
                    <h3>Need Help?</h3>
                    <p>Have questions about this course? Contact us for more information.</p>
                    <a href="{{ route('contact') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-envelope"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
}

.breadcrumb a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: color 0.3s;
}

.breadcrumb a:hover {
    color: #fff;
}

.course-details-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 40px;
    margin-top: 30px;
}

.course-details-main {
    background: white;
    border-radius: 12px;
    padding: 40px;
}

.course-header-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.course-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.course-code {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
}

.course-status {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
}

.course-status.active {
    background: #d1fae5;
    color: #065f46;
}

.course-status.inactive {
    background: #fee2e2;
    color: #991b1b;
}

.course-section {
    margin-bottom: 40px;
}

.course-section:last-child {
    margin-bottom: 0;
}

.course-section h2 {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.course-description {
    font-size: 16px;
    line-height: 1.8;
    color: #555;
}

.course-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.info-item i {
    font-size: 24px;
    color: #667eea;
    margin-top: 5px;
}

.info-item strong {
    display: block;
    color: #333;
    margin-bottom: 5px;
    font-size: 14px;
}

.info-item p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.schedules-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.schedule-item {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.schedule-date,
.schedule-time,
.schedule-venue {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    color: #555;
}

.schedule-date:last-child,
.schedule-time:last-child,
.schedule-venue:last-child {
    margin-bottom: 0;
}

.schedule-date i,
.schedule-time i,
.schedule-venue i {
    color: #667eea;
    width: 20px;
}

.schedule-date strong {
    color: #333;
}

.schedule-status {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.status-scheduled {
    background: #dbeafe;
    color: #1e40af;
}

.status-ongoing {
    background: #fef3c7;
    color: #92400e;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.course-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
}

.course-details-sidebar {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.sidebar-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.sidebar-card h3 {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.quick-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.quick-info-list li {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.quick-info-list li:last-child {
    border-bottom: none;
}

.quick-info-list i {
    color: #667eea;
    width: 20px;
}

.quick-info-list span {
    color: #555;
    font-size: 14px;
}

.btn-block {
    width: 100%;
    text-align: center;
}

.training-link-section {
    background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
    padding: 30px;
    border-radius: 12px;
    border: 2px solid #667eea;
}

.training-link-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    flex-wrap: wrap;
}

.training-link-content {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.training-link-content i {
    font-size: 32px;
    color: #667eea;
}

.training-link-content strong {
    display: block;
    font-size: 18px;
    color: #333;
    margin-bottom: 5px;
}

.training-link-content p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

@media (max-width: 968px) {
    .course-details-layout {
        grid-template-columns: 1fr;
    }
    
    .course-details-sidebar {
        order: -1;
    }
    
    .training-link-card {
        flex-direction: column;
        text-align: center;
    }
    
    .training-link-content {
        flex-direction: column;
        text-align: center;
    }
    
    .training-link-card .btn {
        width: 100%;
    }
}
</style>
@endsection

