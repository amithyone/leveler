<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Leveler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .credentials {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .credentials strong {
            color: #667eea;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Leveler!</h1>
        <p>Your Professional Development Journey Begins Here</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $trainee->full_name }},</p>
        
        <p>Welcome to Leveler! We're excited to have you join our community of professionals committed to growth and excellence.</p>
        
        <p>Your registration has been successfully completed. Here are your login credentials:</p>
        
        <div class="credentials">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            @if($password)
            <p><strong>Password:</strong> {{ $password }}</p>
            <p style="font-size: 12px; color: #666; margin-top: 10px;">
                <em>Please change your password after your first login for security purposes.</em>
            </p>
            @endif
            @if($trainee->username)
            <p><strong>Username:</strong> {{ $trainee->username }}</p>
            @endif
        </div>
        
        <p>You can now:</p>
        <ul>
            <li>Access your dashboard and view your selected courses</li>
            <li>Complete your payment to activate course access</li>
            <li>Start your learning journey once payment is confirmed</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ url('/trainee/login') }}" class="button">Login to Your Account</a>
        </div>
        
        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
        
    </div>
    
    @include('emails.partials.signature')
</body>
</html>
