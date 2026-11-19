{{-- resources/views/emails/order_cancel_user.blade.php --}}
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
            background: #f8f9fa;
        }
        .container {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
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
        .refund-box {
            background: #d1ecf1;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Order Cancelled</h1>
            <p>We're sorry to see you go</p>
        </div>

        <div class="content">
            <h2>Dear {{ $customerName }},</h2>
            
            <p>We regret to inform you that your order has been cancelled.</p>

            <div class="cancellation-box">
                <strong>📅 Cancellation Time:</strong> {{ $cancelledAt }}<br>
                <strong>🆔 Order Number:</strong> #{{ $order->order_id ?? $order->id }}
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
                        <td><strong>₹{{ number_format($order->grand_total ?? $order->total, 2) }}</strong></td>
                    </tr>
                </table>

                @if(count($orderItems) > 0)
                    <h4 style="margin-top: 20px;">🛍️ Items:</h4>
                    @foreach($orderItems as $item)
                        <div class="order-item">
                            <strong>{{ $item->product->name ?? $item->name ?? 'Product' }}</strong><br>
                            Quantity: {{ $item->quantity }} × ₹{{ number_format($item->price, 2) }}
                        </div>
                    @endforeach
                @endif
            </div>

            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin-top: 0;">📍 Delivery Information</h4>
                @if($order->shippingAddress)
                    <p style="margin: 5px 0;"><strong>Address:</strong> {{ $order->shippingAddress->address ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>City:</strong> {{ $order->shippingAddress->city ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>State:</strong> {{ $order->shippingAddress->state ?? 'N/A' }}</p>
                    <p style="margin: 5px 0;"><strong>Pincode:</strong> {{ $order->shippingAddress->pincode ?? 'N/A' }}</p>
                @endif
            </div>

            @if(($order->payment_status ?? '') === 'paid')
                <div class="refund-box">
                    <strong>💰 Refund Information:</strong><br>
                    Your payment of ₹{{ number_format($order->paid_amount ?? $order->grand_total, 2) }} will be refunded within 5-7 business days.
                </div>
            @endif

            <p style="margin-top: 25px;">
                If you have any questions, please <a href="{{ route('contact.us') }}">contact our support team</a>.
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
    </div>
</body>
</html>