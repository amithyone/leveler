<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Courses - Leveler</title>
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
        .course-limit-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #856404;
        }
        .info-alert {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 900px;">
            <div class="auth-header">
                <div class="logo-large">
                    <i class="fas fa-book"></i>
                </div>
                <h1>Select Your Courses</h1>
                <p>Choose the courses you want to enroll in</p>
            </div>

            @if(session('info'))
            <div class="info-alert">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
            </div>
            @endif

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

            @if(isset($packageInfo))
            <div class="package-info">
                <h3><i class="fas fa-gift"></i> Package {{ $packageInfo['type'] }}</h3>
                <p><strong>Courses:</strong> 
                    @if($packageInfo['type'] === 'A')
                        1 Course
                    @elseif($packageInfo['type'] === 'B')
                        2-3 Courses
                    @elseif($packageInfo['type'] === 'C')
                        4-6 Courses
                    @elseif($packageInfo['type'] === 'D')
                        7-9 Courses
                    @elseif($packageInfo['type'] === 'package')
                        4 Courses
                    @elseif($packageInfo['type'] === 'single')
                        1 Course
                    @else
                        {{ $packageInfo['min_courses'] ?? 1 }}-{{ $packageInfo['max_courses'] ?? 9 }} Courses
                    @endif
                </p>
                <p><strong>Total Amount:</strong> ₦{{ number_format($packageInfo['total_amount']) }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('courses.select.save') }}" id="courseSelectionForm">
                @csrf

                <div class="course-selection">
                    <label style="font-weight: bold; margin-bottom: 1rem; display: block;">
                        <i class="fas fa-book"></i> Select Course(s) 
                        @if(isset($packageInfo))
                            @if($packageInfo['min_courses'] == $packageInfo['max_courses'])
                                (Select {{ $packageInfo['min_courses'] }} course{{ $packageInfo['min_courses'] > 1 ? 's' : '' }})
                            @else
                                (Select {{ $packageInfo['min_courses'] }}-{{ $packageInfo['max_courses'] }} courses)
                            @endif
                        @endif
                        <span class="required">*</span>
                    </label>
                    
                    @if(isset($packageInfo))
                    <div class="course-limit-info">
                        <i class="fas fa-info-circle"></i> 
                        You can select 
                        @if($packageInfo['min_courses'] == $packageInfo['max_courses'])
                            {{ $packageInfo['min_courses'] }} course{{ $packageInfo['min_courses'] > 1 ? 's' : '' }}
                        @else
                            between {{ $packageInfo['min_courses'] }} and {{ $packageInfo['max_courses'] }} courses
                        @endif
                        for Package {{ $packageInfo['type'] }}
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
                                {{ in_array($course->id, $selectedCourses ?? []) ? 'checked' : '' }}
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

                <div style="margin-top: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                    <p style="margin: 0; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> Selected: <span id="selectedCount">0</span> course(s)
                    </p>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                    <i class="fas fa-save"></i> Save Course Selection & Continue to Payment
                </button>
            </form>

            <div class="auth-footer">
                <p><a href="{{ route('trainee.dashboard') }}"><i class="fas fa-arrow-left"></i> Back to Dashboard</a></p>
            </div>
        </div>
    </div>

    <script>
        const packageInfo = @json($packageInfo ?? null);
        const minCourses = packageInfo ? (packageInfo.min_courses ?? 1) : 1;
        const maxCourses = packageInfo ? (packageInfo.max_courses ?? 9) : 9;

        function updateCourseSelection() {
            const checkboxes = document.querySelectorAll('.course-checkbox:checked');
            const selectedCount = checkboxes.length;
            
            document.getElementById('selectedCount').textContent = selectedCount;
            
            // Disable/enable checkboxes based on limits
            const allCheckboxes = document.querySelectorAll('.course-checkbox');
            allCheckboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    if (selectedCount >= maxCourses) {
                        checkbox.disabled = true;
                        checkbox.parentElement.querySelector('.course-label').style.opacity = '0.5';
                        checkbox.parentElement.querySelector('.course-label').style.cursor = 'not-allowed';
                    } else {
                        checkbox.disabled = false;
                        checkbox.parentElement.querySelector('.course-label').style.opacity = '1';
                        checkbox.parentElement.querySelector('.course-label').style.cursor = 'pointer';
                    }
                } else {
                    checkbox.disabled = false;
                    checkbox.parentElement.querySelector('.course-label').style.opacity = '1';
                    checkbox.parentElement.querySelector('.course-label').style.cursor = 'pointer';
                }
            });
        }

        // Validate form submission
        document.getElementById('courseSelectionForm').addEventListener('submit', function(e) {
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
            // Enable all checkboxes initially
            const allCheckboxes = document.querySelectorAll('.course-checkbox');
            allCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
            });
            updateCourseSelection();
        });
    </script>
</body>
</html>

