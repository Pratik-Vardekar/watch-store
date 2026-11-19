<!-- resources/views/emails/order_cancel_user.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancelled</title>
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
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .cancellation-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-item {
            border-bottom: 1px solid #dee2e6;
            padding: 10px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            background: #dc3545;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">❌ Order Cancelled</h1>
        <p style="margin: 5px 0 0; opacity: 0.9;">We're sorry to see you go</p>
    </div>

    <div class="content">
        <h2>Dear {{ $customerName }},</h2>
        
        <p>We regret to inform you that your order has been cancelled.</p>

        <div class="cancellation-box">
            <strong>📅 Cancellation Time:</strong> {{ $cancelledAt }}<br>
            <strong>🆔 Order Number:</strong> #{{ $order->order_number ?? $order->id }}
        </div>

        <div class="order-details">
            <h3 style="margin-top: 0;">📋 Order Summary</h3>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td><strong>Order Date:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('F j, Y g:i A') }}</td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td><span class="status-badge">Cancelled</span></td>
                </tr>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                </tr>
            </table>

            @if(count($orderItems) > 0)
                <h4 style="margin-top: 20px;">🛍️ Items:</h4>
                @foreach($orderItems as $item)
                    <div class="order-item">
                        <strong>{{ $item->name ?? $item->product_name ?? 'Product' }}</strong><br>
                        Quantity: {{ $item->quantity ?? 1 }} × ${{ number_format($item->price ?? 0, 2) }}
                    </div>
                @endforeach
            @endif
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0;">📍 Delivery Information</h4>
            <p style="margin: 5px 0;"><strong>Address:</strong> {{ $order->address ?? 'N/A' }}</p>
            <p style="margin: 5px 0;"><strong>Phone:</strong> {{ $order->phone ?? 'N/A' }}</p>
        </div>

        @if(isset($order->payment_status) && $order->payment_status === 'paid')
            <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <strong>💰 Refund Information:</strong><br>
                Your payment of ${{ number_format($order->total, 2) }} will be refunded within 5-7 business days.
            </div>
        @endif

        <p style="margin-top: 25px;">
            If you have any questions or need assistance, please don't hesitate to 
            <a href="mailto:{{ config('mail.support.email', 'support@example.com') }}">contact our support team</a>.
        </p>

        <p style="margin-top: 20px;">
            Thank you for your understanding,<br>
            <strong>{{ config('app.name') }} Team</strong>
        </p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>