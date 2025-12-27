<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NYSC Registration - Leveler</title>
    <link rel="stylesheet" href="{{ asset('css/trainee-auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .package-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        .package-info h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.3rem;
        }
        .package-info p {
            margin: 0.25rem 0;
            opacity: 0.9;
        }
        .course-selection {
            margin-top: 1.5rem;
        }
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .course-checkbox {
            display: none;
        }
        .course-label {
            display: block;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: left;
            background: white;
        }
        .course-label:hover {
            border-color: #4a90e2;
            background: #f0f7ff;
        }
        .course-checkbox:checked + .course-label {
            border-color: #4a90e2;
            background: #e0f2ff;
        }
        .course-checkbox:disabled + .course-label {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .course-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .course-checkbox:checked + .course-label .course-title {
            color: #4a90e2;
        }
        .course-code {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.25rem;
        }
        .course-duration {
            font-size: 0.8rem;
            color: #999;
        }
        .pricing-summary {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        .pricing-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }
        .pricing-total {
            font-weight: bold;
            font-size: 1.3rem;
            border-top: 2px solid #ddd;
            padding-top: 1rem;
            margin-top: 1rem;
            color: #333;
        }
        .pricing-installment {
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e0e0e0;
        }
        .course-limit-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 900px;">
            <div class="auth-header">
                <div class="logo-large">
                    <i class="fas fa-flag"></i>
                </div>
                <h1>NYSC Registration</h1>
                <p>Complete your registration details</p>
            </div>

            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(isset($package))
            <div class="package-info">
                <h3><i class="fas fa-gift"></i> Package {{ $package['type'] }}</h3>
                <p><strong>Courses:</strong> 
                    @if($package['type'] === 'A')
                        1 Course
                    @elseif($package['type'] === 'B')
                        2-3 Courses
                    @elseif($package['type'] === 'C')
                        4-6 Courses
                    @else
                        7-9 Courses
                    @endif
                </p>
                <p><strong>Total Amount:</strong> ₦{{ number_format($package['total_amount']) }}</p>
                <p><strong>Installments:</strong> {{ $package['type'] === 'A' ? '1' : ($package['type'] === 'B' ? '2' : '3') }} Payment(s)</p>
            </div>
            @endif

            <form method="POST" action="{{ route('trainee.register.nysc') }}" id="nyscForm">
                @csrf

                <div class="form-group">
                    <label for="full_name">
                        <i class="fas fa-user"></i> Full Name <span class="required">*</span>
                    </label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label for="state_code">
                        <i class="fas fa-map-marker-alt"></i> State Code <span class="required">*</span>
                    </label>
                    <input type="text" id="state_code" name="state_code" value="{{ old('state_code') }}" required placeholder="e.g., AB, LA, FCT" maxlength="10">
                    <small class="form-text">Your NYSC state code</small>
                </div>

                <div class="form-group">
                    <label for="whatsapp_number">
                        <i class="fab fa-whatsapp"></i> WhatsApp Number <span class="required">*</span>
                    </label>
                    <input type="tel" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required placeholder="e.g., 2348061234567">
                    <small class="form-text">Include country code (e.g., 234 for Nigeria)</small>
                </div>

                <div class="course-selection">
                    <label style="font-weight: bold; margin-bottom: 1rem; display: block;">
                        <i class="fas fa-book"></i> Select Course(s) 
                        @if(isset($package))
                            @if($package['type'] === 'A')
                                (Select 1 course)
                            @elseif($package['type'] === 'B')
                                (Select 2-3 courses)
                            @elseif($package['type'] === 'C')
                                (Select 4-6 courses)
                            @else
                                (Select 7-9 courses)
                            @endif
                        @endif
                        <span class="required">*</span>
                    </label>
                    
                    @if(isset($package))
                    <div class="course-limit-info">
                        <i class="fas fa-info-circle"></i> 
                        You can select 
                        @if($package['type'] === 'A')
                            1 course
                        @elseif($package['type'] === 'B')
                            between 2 and 3 courses
                        @elseif($package['type'] === 'C')
                            between 4 and 6 courses
                        @else
                            between 7 and 9 courses
                        @endif
                        for Package {{ $package['type'] }}
                    </div>
                    @endif

                    <div class="course-grid">
                        @foreach($courses as $course)
                        <div>
                            <input 
                                type="checkbox" 
                                id="course_{{ $course->id }}" 
                                name="courses[]" 
                                value="{{ $course->id }}" 
                                class="course-checkbox" 
                                onchange="updateCourseSelection()"
                                @if(isset($package))
                                    @if($package['type'] === 'A' && $loop->index >= 1)
                                        disabled
                                    @elseif($package['type'] === 'B' && $loop->index >= 3)
                                        disabled
                                    @elseif($package['type'] === 'C' && $loop->index >= 6)
                                        disabled
                                    @endif
                                @endif
                            >
                            <label for="course_{{ $course->id }}" class="course-label">
                                <div class="course-title">{{ $course->title }}</div>
                                <div class="course-code">{{ $course->code }}</div>
                                @if($course->duration_hours)
                                <div class="course-duration"><i class="fas fa-clock"></i> {{ $course->duration_hours }} hours</div>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="pricing-summary" id="pricingSummary">
                    <h3 style="margin-bottom: 1rem;">Package Summary</h3>
                    <div class="pricing-item">
                        <span>Package Type:</span>
                        <span><strong>Package {{ $package['type'] ?? 'N/A' }}</strong></span>
                    </div>
                    <div class="pricing-item">
                        <span>Selected Courses:</span>
                        <span id="selectedCount">0</span>
                    </div>
                    <div class="pricing-item pricing-total">
                        <span>Total Package Amount:</span>
                        <span>₦{{ isset($package) ? number_format($package['total_amount']) : '0' }}</span>
                    </div>
                    @if(isset($package) && $package['type'] !== 'A')
                    <div class="pricing-installment">
                        <strong>Initial Payment:</strong> ₦10,000<br>
                        <small>You'll pay ₦10,000 now and the remaining balance can be paid in installments.</small>
                    </div>
                    @elseif(isset($package) && $package['type'] === 'A')
                    <div class="pricing-installment">
                        <strong>Full Payment:</strong> ₦10,000<br>
                        <small>Pay the full amount in one payment.</small>
                    </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                    <i class="fas fa-user-plus"></i> Complete Registration
                </button>
            </form>

            <div class="auth-footer">
                <p><a href="{{ route('trainee.register.category') }}"><i class="fas fa-arrow-left"></i> Back to Category Selection</a></p>
            </div>
        </div>
    </div>

    <script>
        const packageInfo = @json($package ?? null);
        const minCourses = packageInfo ? (packageInfo.type === 'A' ? 1 : (packageInfo.type === 'B' ? 2 : (packageInfo.type === 'C' ? 4 : 7))) : 1;
        const maxCourses = packageInfo ? packageInfo.max_courses : 9;

        function updateCourseSelection() {
            const checkboxes = document.querySelectorAll('.course-checkbox:checked');
            const selectedCount = checkboxes.length;
            
            document.getElementById('selectedCount').textContent = selectedCount;
            
            // Disable/enable checkboxes based on limits
            const allCheckboxes = document.querySelectorAll('.course-checkbox:not(:disabled)');
            allCheckboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    if (selectedCount >= maxCourses) {
                        checkbox.disabled = true;
                    } else {
                        checkbox.disabled = false;
                    }
                }
            });
        }

        // Validate form submission
        document.getElementById('nyscForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.course-checkbox:checked');
            const selectedCount = checkboxes.length;
            
            if (selectedCount < minCourses) {
                e.preventDefault();
                alert(`Please select at least ${minCourses} course(s) for this package.`);
                return false;
            }
            
            if (selectedCount > maxCourses) {
                e.preventDefault();
                alert(`You can select a maximum of ${maxCourses} course(s) for this package.`);
                return false;
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCourseSelection();
        });
    </script>
</body>
</html>
