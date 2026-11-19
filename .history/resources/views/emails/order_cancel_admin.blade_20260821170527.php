{{-- resources/views/emails/order_cancel_admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancelled - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background: #f4f4f4;
        }
        .container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 25px 30px;
        }
        .header h2 {
            margin: 0;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-grid {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            background: #dc3545;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .items-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
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
        .footer {
            padding: 20px 30px;
            background: #f8f9fa;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
        }
        .urgent {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ Order Cancelled Alert</h2>
            <p style="margin: 5px 0 0; opacity: 0.9;">Admin Notification</p>
        </div>

        <div class="content">
            <div class="alert-box">
                <strong>📅 Cancelled At:</strong> {{ $cancelledAt }}<br>
                <strong>🆔 Order #:</strong> {{ $order->order_id ?? $order->id }}
            </div>

            <h3 style="margin-top: 0;">👤 Customer Information</h3>
            <div class="info-grid">
                <div class="info-item"><strong>Name:</strong> {{ $customerName }}</div>
                <div class="info-item"><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</div>
                <div class="info-item"><strong>Phone:</strong> {{ $order->user->phone ?? 'N/A' }}</div>
                <div class="info-item"><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('F j, Y g:i A') }}</div>
                <div class="info-item"><strong>Status:</strong> <span class="status-badge">Cancelled</span></div>
                <div class="info-item"><strong>Total:</strong> <strong>₹{{ number_format($order->grand_total ?? $order->total, 2) }}</strong></div>
            </div>

            <h3>📍 Shipping Address</h3>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0 20px;">
                @if($order->shippingAddress)
                    {{ $order->shippingAddress->address ?? 'N/A' }},
                    {{ $order->shippingAddress->city ?? 'N/A' }},
                    {{ $order->shippingAddress->state ?? 'N/A' }} -
                    {{ $order->shippingAddress->pincode ?? 'N/A' }}
                @else
                    N/A
                @endif
            </div>

            @if(count($orderItems) > 0)
                <h3>🛍️ Items Cancelled</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderItems as $item)
                            <tr>
                                <td>{{ $item->product->name ?? $item->name ?? 'Product' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₹{{ number_format($item->price, 2) }}</td>
                                <td>₹{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div style="background: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin-top: 0;">💰 Payment Information</h4>
                <p><strong>Payment Method:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
                <p><strong>Payment Status:</strong> {{ $order->payment_status ?? 'N/A' }}</p>
                <p><strong>Paid Amount:</strong> ₹{{ number_format($order->paid_amount ?? 0, 2) }}</p>
                @if(($order->payment_status ?? '') === 'paid' && ($order->paid_amount ?? 0) > 0)
                    <p class="urgent">⚠️ REFUND REQUIRED - ₹{{ number_format($order->paid_amount, 2) }}</p>
                @endif
                @if(isset($order->cancel_request_reason))
                    <p><strong>Cancellation Reason:</strong> {{ $order->cancel_request_reason }}</p>
                @endif
            </div>

            <div style="text-align: center; margin-top: 25px;">
                <a href="{{ url('/admin/order/' . $order->id) }}" class="btn">
                    📋 View Order in Admin Panel
                </a>
            </div>
        </div>

        <div class="footer">
            <p style="margin: 0;">This is an automated notification for administrators.</p>
            <p style="margin: 5px 0 0; font-size: 12px;">Sent at: {{ $cancelledAt }}</p>
        </div>
    </div>
</body>
</html>