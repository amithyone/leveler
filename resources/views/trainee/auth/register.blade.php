<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Registration - Leveler</title>
    <link rel="stylesheet" href="{{ asset('css/trainee-auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-large">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Leveler</h1>
                <p>Create Your Account</p>
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

            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('trainee.register') }}" class="auth-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="surname">
                            <i class="fas fa-user"></i> Surname <span class="required">*</span>
                        </label>
                        <input type="text" id="surname" name="surname" value="{{ old('surname') }}" required autofocus placeholder="Enter your surname">
                    </div>

                    <div class="form-group">
                        <label for="first_name">
                            <i class="fas fa-user"></i> First Name <span class="required">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required placeholder="Enter your first name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="middle_name">
                            <i class="fas fa-user"></i> Middle Name
                        </label>
                        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="Enter your middle name (optional)">
                    </div>

                    <div class="form-group">
                        <label for="gender">
                            <i class="fas fa-venus-mars"></i> Gender <span class="required">*</span>
                        </label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Male</option>
                            <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email address">
                    <small class="form-text">You'll use this email to log in</small>
                </div>

                <div class="form-group">
                    <label for="phone_number">
                        <i class="fas fa-phone"></i> Phone Number <span class="required">*</span>
                    </label>
                    <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required placeholder="e.g., 2348061234567">
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

                <div class="form-info">
                    <p><i class="fas fa-info-circle"></i> After registration, you'll become a user. Select a course and make a payment to become a trainee and gain access to courses.</p>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('trainee.login') }}">Sign In</a></p>
                <p><a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>

