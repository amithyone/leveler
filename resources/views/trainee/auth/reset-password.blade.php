<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Leveler</title>
    <link rel="stylesheet" href="{{ asset('css/trainee-auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-large">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Leveler</h1>
                <p>Trainee Portal - Reset Password</p>
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

            <form method="POST" action="{{ route('trainee.password.update') }}" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="Enter your email address">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> New Password
                    </label>
                    <input type="password" id="password" name="password" required placeholder="Enter your new password">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="fas fa-lock"></i> Confirm New Password
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your new password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </form>

            <div class="auth-footer">
                <p><a href="{{ route('trainee.login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>

