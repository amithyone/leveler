<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - Leveler</title>
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
        .receipt-box {
            background: white;
            padding: 25px;
            border-radius: 5px;
            margin: 20px 0;
            border: 2px solid #667eea;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .receipt-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            color: #667eea;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #667eea;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .status-approved {
            background: #10b981;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
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
        <h1>Payment Receipt</h1>
        <p>Your Payment Has Been Approved</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $trainee->full_name }},</p>
        
        <p>We're pleased to inform you that your payment has been successfully processed and approved.</p>
        
        <div class="receipt-box">
            <div class="receipt-header">
                <h2 style="margin: 0; color: #667eea;">PAYMENT RECEIPT</h2>
                <p style="margin: 5px 0; color: #666;">Receipt #{{ $payment->receipt_number ?? $payment->id }}</p>
            </div>
            
            <div class="receipt-row">
                <span class="label">Payment Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}</span>
            </div>
            
            <div class="receipt-row">
                <span class="label">Amount Paid:</span>
                <span class="value">₦{{ number_format($payment->amount, 2) }}</span>
            </div>
            
            <div class="receipt-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ $payment->payment_method }}</span>
            </div>
            
            @if($payment->transaction_reference)
            <div class="receipt-row">
                <span class="label">Transaction Reference:</span>
                <span class="value">{{ $payment->transaction_reference }}</span>
            </div>
            @endif
            
            <div class="receipt-row">
                <span class="label">Status:</span>
                <span class="value">
                    <span class="status-approved">{{ $payment->status }}</span>
                </span>
            </div>
            
            @if($payment->notes)
            <div class="receipt-row">
                <span class="label">Notes:</span>
                <span class="value">{{ $payment->notes }}</span>
            </div>
            @endif
        </div>
        
        <p><strong>What's Next?</strong></p>
        <ul>
            <li>Your course access has been activated</li>
            <li>You can now start your training courses</li>
            <li>Access your dashboard to view your enrolled courses</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ url('/trainee/dashboard') }}" class="button">Access Your Dashboard</a>
        </div>
        
        <p>If you have any questions about this payment or your course access, please contact our support team.</p>
        
        <p>Thank you for choosing Leveler!</p>
        
        <p>Best regards,<br>
        <strong>The Leveler Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} Leveler. All rights reserved.</p>
    </div>
</body>
</html>
