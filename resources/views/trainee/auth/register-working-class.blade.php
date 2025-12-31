<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Working-Class Registration - Leveler</title>
    <link rel="stylesheet" href="{{ asset('css/trainee-auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .course-selection {
            margin-top: 1.5rem;
        }
        .course-list {
            margin-top: 1rem;
        }
        .course-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .course-item:hover {
            border-color: #4a90e2;
            background: #f0f7ff;
        }
        .course-checkbox {
            margin-right: 1rem;
            width: 20px;
            height: 20px;
        }
        .course-info {
            flex: 1;
        }
        .course-title {
            font-weight: bold;
            margin-bottom: 0.3rem;
        }
        .course-price {
            color: #4a90e2;
            font-weight: bold;
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
        .discount-badge {
            background: #28a745;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }
        .installment-info {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 900px;">
            <div class="auth-header">
                <div class="logo-large">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h1>Working-Class Registration</h1>
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

            <form method="POST" action="{{ route('trainee.register.working-class') }}" id="workingClassForm">
                @csrf

                <div class="form-group">
                    <label for="full_name">
                        <i class="fas fa-user"></i> Full Name <span class="required">*</span>
                    </label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus placeholder="Enter your full name">
                    <small class="form-text">Fill as your name should appear on your certificates</small>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email address">
                    <small class="form-text">You'll use this email to log in</small>
                </div>

                <div class="form-group">
                    <label for="whatsapp_number">
                        <i class="fab fa-whatsapp"></i> WhatsApp Number <span class="required">*</span>
                    </label>
                    <input type="tel" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required placeholder="e.g., 2348061234567">
                    <small class="form-text">Include country code (e.g., 234 for Nigeria)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password <span class="required">*</span>
                        </label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password (min 6 characters)">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-lock"></i> Confirm Password <span class="required">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your password">
                    </div>
                </div>

                <div class="course-selection">
                    <label style="font-weight: bold; margin-bottom: 1rem; display: block;">
                        <i class="fas fa-book"></i> Select Course(s) <span class="required">*</span>
                    </label>
                    <div class="course-list">
                        @php
                            $workingClassPricing = \App\Services\CoursePricingService::getWorkingClassPricing();
                        @endphp
                        @foreach($workingClassPricing as $title => $price)
                            <div class="course-item">
                                <input type="checkbox" id="course_{{ str_replace(' ', '_', $title) }}" name="courses[]" value="{{ $title }}" class="course-checkbox" onchange="updatePricing()">
                                <div class="course-info">
                                    <div class="course-title">{{ $title }}</div>
                                    <div class="course-price">₦{{ number_format($price) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pricing-summary" id="pricingSummary" style="display: none;">
                    <h3 style="margin-bottom: 1rem;">Pricing Summary</h3>
                    <div id="pricingDetails"></div>
                    <div class="pricing-item pricing-total">
                        <span>Total:</span>
                        <span id="totalAmount">₦0</span>
                    </div>
                    <div class="installment-info" id="installmentInfo"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                    <i class="fas fa-user-plus"></i> Complete Registration
                </button>
            </form>

            <div class="auth-footer">
                <p><a href="{{ route('trainee.register') }}"><i class="fas fa-arrow-left"></i> Back to Category Selection</a></p>
            </div>
        </div>
    </div>

    <script>
        const workingClassPricing = @json(\App\Services\CoursePricingService::getWorkingClassPricing());

        function updatePricing() {
            const checkboxes = document.querySelectorAll('.course-checkbox:checked');
            const selectedCourses = Array.from(checkboxes).map(cb => cb.value);
            
            if (selectedCourses.length === 0) {
                document.getElementById('pricingSummary').style.display = 'none';
                return;
            }

            document.getElementById('pricingSummary').style.display = 'block';
            
            let subtotal = 0;
            let html = '';
            
            selectedCourses.forEach(courseTitle => {
                const price = workingClassPricing[courseTitle];
                if (price) {
                    subtotal += price;
                    html += `
                        <div class="pricing-item">
                            <span>${courseTitle}:</span>
                            <span>₦${price.toLocaleString()}</span>
                        </div>
                    `;
                }
            });
            
            // Calculate discount
            const courseCount = selectedCourses.length;
            let discountPercent = 0;
            if (courseCount >= 2 && courseCount <= 3) {
                discountPercent = 5;
            } else if (courseCount >= 4 && courseCount <= 6) {
                discountPercent = 15;
            } else if (courseCount >= 7 && courseCount <= 9) {
                discountPercent = 25;
            }
            
            const discount = (subtotal * discountPercent) / 100;
            const total = subtotal - discount;
            
            if (discountPercent > 0) {
                html += `
                    <div class="pricing-item">
                        <span>Subtotal:</span>
                        <span>₦${subtotal.toLocaleString()}</span>
                    </div>
                    <div class="pricing-item">
                        <span>Discount (${discountPercent}%):</span>
                        <span style="color: #28a745;">-₦${discount.toLocaleString()}</span>
                    </div>
                `;
            }
            
            document.getElementById('pricingDetails').innerHTML = html;
            document.getElementById('totalAmount').textContent = '₦' + total.toLocaleString();
            
            // Installment info
            const installmentAllowed = courseCount > 1;
            if (installmentAllowed) {
                document.getElementById('installmentInfo').innerHTML = '<i class="fas fa-info-circle"></i> Installment payment is available for 2+ courses';
            } else {
                document.getElementById('installmentInfo').innerHTML = '<i class="fas fa-info-circle"></i> Installment payment is only available when selecting 2 or more courses';
            }
        }

        // Validate form submission
        document.getElementById('workingClassForm').addEventListener('submit', function(e) {
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

