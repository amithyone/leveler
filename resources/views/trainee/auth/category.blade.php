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
        .category-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f5f5f5;
            position: relative;
        }
        .category-card.disabled:hover {
            transform: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-color: transparent;
        }
        .category-card.disabled::after {
            content: "Coming Soon";
            position: absolute;
            top: 10px;
            right: 10px;
            background: #999;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .package-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .package-modal.active {
            display: flex;
        }
        .package-modal-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }
        .package-modal-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .package-modal-header h2 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .package-modal-header p {
            color: #666;
        }
        .package-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .package-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        .package-card:hover {
            border-color: #4a90e2;
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        .package-card.selected {
            border-color: #4a90e2;
            background: #f0f7ff;
        }
        .package-card.package-a {
            border-color: #fbbf24;
        }
        .package-card.package-a.selected {
            background: #fef3c7;
            border-color: #f59e0b;
        }
        .package-card.package-b {
            border-color: #fb923c;
        }
        .package-card.package-b.selected {
            background: #fed7aa;
            border-color: #f97316;
        }
        .package-card.package-c {
            border-color: #34d399;
        }
        .package-card.package-c.selected {
            background: #d1fae5;
            border-color: #10b981;
        }
        .package-card.package-d {
            border-color: #a78bfa;
        }
        .package-card.package-d.selected {
            background: #e9d5ff;
            border-color: #8b5cf6;
        }
        .package-name {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .package-courses {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }
        .package-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .package-installments {
            font-size: 0.85rem;
            color: #666;
        }
        .close-modal {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }
        .close-modal:hover {
            color: #333;
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

                    <div class="category-card disabled" data-type="working_class">
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

    <!-- Package Selection Modal -->
    <div class="package-modal" id="packageModal">
        <div class="package-modal-content">
            <button class="close-modal" onclick="closePackageModal()">&times;</button>
            <div class="package-modal-header">
                <h2><i class="fas fa-gift"></i> NYSC Exclusive Packages</h2>
                <p>Select a package that suits your learning needs</p>
            </div>
            <div class="package-grid">
                <div class="package-card package-a" onclick="selectPackage('A', 1, 10000, 1)">
                    <div class="package-name">Package A</div>
                    <div class="package-courses">1 Course</div>
                    <div class="package-price">₦10,000</div>
                    <div class="package-installments">1 Installment</div>
                </div>
                <div class="package-card package-b" onclick="selectPackage('B', 3, 18000, 2)">
                    <div class="package-name">Package B</div>
                    <div class="package-courses">2-3 Courses</div>
                    <div class="package-price">₦18,000</div>
                    <div class="package-installments">2 Installments</div>
                </div>
                <div class="package-card package-c" onclick="selectPackage('C', 6, 25000, 3)">
                    <div class="package-name">Package C</div>
                    <div class="package-courses">4-6 Courses</div>
                    <div class="package-price">₦25,000</div>
                    <div class="package-installments">3 Installments</div>
                </div>
                <div class="package-card package-d" onclick="selectPackage('D', 9, 30000, 3)">
                    <div class="package-name">Package D</div>
                    <div class="package-courses">7-9 Courses</div>
                    <div class="package-price">₦30,000</div>
                    <div class="package-installments">3 Installments</div>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-block" onclick="proceedWithPackage()" id="proceedPackageBtn" disabled>
                <i class="fas fa-arrow-right"></i> Continue with Selected Package
            </button>
        </div>
    </div>

    <script>
        let selectedPackage = null;
        let selectedPackageData = null;

        function selectCategory(type) {
            if (type === 'working_class') {
                return; // Disabled
            }

            // Remove selected class from all cards
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');

            // Set hidden input value
            document.getElementById('userType').value = type;

            // If NYSC, show package modal
            if (type === 'nysc') {
                document.getElementById('packageModal').classList.add('active');
            } else {
                // Enable continue button for other categories
                document.getElementById('continueBtn').disabled = false;
            }
        }

        function selectPackage(packageType, maxCourses, totalAmount, installments) {
            // Remove selected class from all package cards
            document.querySelectorAll('.package-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked package
            event.currentTarget.classList.add('selected');

            selectedPackage = packageType;
            selectedPackageData = {
                type: packageType,
                maxCourses: maxCourses,
                totalAmount: totalAmount,
                installments: installments
            };

            // Enable proceed button
            document.getElementById('proceedPackageBtn').disabled = false;
        }

        function proceedWithPackage() {
            if (!selectedPackage) {
                alert('Please select a package');
                return;
            }

            // Store package data in session storage
            sessionStorage.setItem('selectedPackage', JSON.stringify(selectedPackageData));

            // Close modal
            closePackageModal();

            // Enable continue button
            document.getElementById('continueBtn').disabled = false;
        }

        function closePackageModal() {
            document.getElementById('packageModal').classList.remove('active');
        }

        // Handle form submission
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            const userType = document.getElementById('userType').value;
            
            if (userType === 'nysc' && !selectedPackage) {
                e.preventDefault();
                alert('Please select a package');
                document.getElementById('packageModal').classList.add('active');
                return false;
            }

            // Add package data to form if NYSC
            if (userType === 'nysc' && selectedPackageData) {
                const packageInput = document.createElement('input');
                packageInput.type = 'hidden';
                packageInput.name = 'package_type';
                packageInput.value = selectedPackageData.type;
                this.appendChild(packageInput);

                const maxCoursesInput = document.createElement('input');
                maxCoursesInput.type = 'hidden';
                maxCoursesInput.name = 'max_courses';
                maxCoursesInput.value = selectedPackageData.maxCourses;
                this.appendChild(maxCoursesInput);

                const totalAmountInput = document.createElement('input');
                totalAmountInput.type = 'hidden';
                totalAmountInput.name = 'total_amount';
                totalAmountInput.value = selectedPackageData.totalAmount;
                this.appendChild(totalAmountInput);
            }
        });
    </script>
</body>
</html>

