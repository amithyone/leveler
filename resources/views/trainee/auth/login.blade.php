<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Login - Leveler</title>
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
                <p>Trainee Portal</p>
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

            @if(session('status'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('trainee.login') }}" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email address">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="auth-footer">
                <p><a href="{{ route('trainee.password.request') }}"><i class="fas fa-key"></i> Forgot Password?</a></p>
                <p>Don't have an account? <a href="{{ route('trainee.register') }}">Register Here</a></p>
                <p><a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>

