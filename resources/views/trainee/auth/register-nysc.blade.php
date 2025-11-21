<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NYSC Registration - Leveler</title>
    <link rel="stylesheet" href="{{ asset('css/trainee-auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .course-selection {
            margin-top: 1.5rem;
        }
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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
            text-align: center;
        }
        .course-label:hover {
            border-color: #4a90e2;
            background: #f0f7ff;
        }
        .course-checkbox:checked + .course-label {
            border-color: #4a90e2;
            background: #4a90e2;
            color: white;
        }
        .course-number {
            font-weight: bold;
            font-size: 1.2rem;
        }
        .course-price {
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .pricing-summary {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .pricing-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }
        .pricing-total {
            font-weight: bold;
            font-size: 1.2rem;
            border-top: 2px solid #ddd;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
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
                        <i class="fas fa-book"></i> Select Course(s) (1-9) <span class="required">*</span>
                    </label>
                    <div class="course-grid">
                        @for($i = 1; $i <= 9; $i++)
                            @php
                                $pricing = \App\Services\CoursePricingService::getNyscPricing();
                                $coursePrice = $pricing[$i]['price'] ?? 0;
                                $installment = $pricing[$i]['installment'] ?? 1;
                                $duration = $pricing[$i]['duration'] ?? '';
                            @endphp
                            <div>
                                <input type="checkbox" id="course_{{ $i }}" name="courses[]" value="{{ $i }}" class="course-checkbox" onchange="updatePricing()">
                                <label for="course_{{ $i }}" class="course-label">
                                    <div class="course-number">Course {{ $i }}</div>
                                    <div class="course-price">₦{{ number_format($coursePrice) }}</div>
                                    <div style="font-size: 0.8rem; margin-top: 0.3rem;">{{ $installment }}x Installment</div>
                                    <div style="font-size: 0.8rem;">{{ $duration }}</div>
                                </label>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="pricing-summary" id="pricingSummary" style="display: none;">
                    <h3 style="margin-bottom: 1rem;">Pricing Summary</h3>
                    <div id="pricingDetails"></div>
                    <div class="pricing-item pricing-total">
                        <span>Total:</span>
                        <span id="totalAmount">₦0</span>
                    </div>
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
        const nyscPricing = @json(\App\Services\CoursePricingService::getNyscPricing());

        function updatePricing() {
            const checkboxes = document.querySelectorAll('.course-checkbox:checked');
            const selectedCourses = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            if (selectedCourses.length === 0) {
                document.getElementById('pricingSummary').style.display = 'none';
                return;
            }

            document.getElementById('pricingSummary').style.display = 'block';
            
            let total = 0;
            let html = '';
            
            selectedCourses.forEach(courseNum => {
                const course = nyscPricing[courseNum];
                if (course) {
                    total += course.price;
                    html += `
                        <div class="pricing-item">
                            <span>Course ${courseNum} (${course.duration}):</span>
                            <span>₦${course.price.toLocaleString()}</span>
                        </div>
                    `;
                }
            });
            
            document.getElementById('pricingDetails').innerHTML = html;
            document.getElementById('totalAmount').textContent = '₦' + total.toLocaleString();
        }

        // Validate form submission
        document.getElementById('nyscForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.course-checkbox:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one course');
                return false;
            }
        });
    </script>
</body>
</html>

