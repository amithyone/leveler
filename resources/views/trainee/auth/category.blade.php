<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Registration Category - Leveler</title>
    <link rel="stylesheet" href="{{ asset('css/trainee-auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .category-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .category-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
            border-color: #4a90e2;
        }
        .category-card.selected {
            border-color: #4a90e2;
            background: #f0f7ff;
        }
        .category-icon {
            font-size: 3rem;
            color: #4a90e2;
            margin-bottom: 1rem;
        }
        .category-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .category-description {
            color: #666;
            margin-bottom: 1rem;
        }
        .category-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .category-features li {
            padding: 0.5rem 0;
            color: #555;
        }
        .category-features li i {
            color: #4a90e2;
            margin-right: 0.5rem;
        }
        .continue-btn {
            margin-top: 2rem;
            width: 100%;
        }
        .continue-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card" style="max-width: 800px;">
            <div class="auth-header">
                <div class="logo-large">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Leveler</h1>
                <p>Select Your Registration Category</p>
            </div>

            <form method="POST" action="{{ route('trainee.register.category') }}" id="categoryForm">
                @csrf
                <input type="hidden" name="user_type" id="userType" required>

                <div class="category-cards">
                    <div class="category-card" data-type="nysc" onclick="selectCategory('nysc')">
                        <div class="category-icon">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div class="category-title">National Youth Service Corps (NYSC)</div>
                        <div class="category-description">
                            Special pricing and installment plans for NYSC members
                        </div>
                        <ul class="category-features">
                            <li><i class="fas fa-check"></i> Flexible installment plans</li>
                            <li><i class="fas fa-check"></i> Affordable course pricing</li>
                            <li><i class="fas fa-check"></i> 365-day access period</li>
                            <li><i class="fas fa-check"></i> State code required</li>
                        </ul>
                    </div>

                    <div class="category-card" data-type="working_class" onclick="selectCategory('working_class')">
                        <div class="category-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="category-title">Working-Class Individual</div>
                        <div class="category-description">
                            Professional development courses with volume discounts
                        </div>
                        <ul class="category-features">
                            <li><i class="fas fa-check"></i> Volume discounts (5-25%)</li>
                            <li><i class="fas fa-check"></i> Installment options (2+ courses)</li>
                            <li><i class="fas fa-check"></i> Professional courses</li>
                            <li><i class="fas fa-check"></i> Email registration</li>
                        </ul>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block continue-btn" id="continueBtn" disabled>
                    <i class="fas fa-arrow-right"></i> Continue
                </button>

                <div class="auth-footer" style="margin-top: 2rem;">
                    <p><a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectCategory(type) {
            // Remove selected class from all cards
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');

            // Set hidden input value
            document.getElementById('userType').value = type;

            // Enable continue button
            document.getElementById('continueBtn').disabled = false;
        }
    </script>
</body>
</html>

