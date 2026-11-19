<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Shipped - Admin</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #e85d04;
        }
        .header h1 {
            color: #e85d04;
            margin: 0;
        }
        .content {
            padding: 30px 0;
        }
        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details table td {
            padding: 8px 0;
        }
        .order-details table .label {
            font-weight: bold;
            width: 40%;
        }
        .action-buttons {
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #e85d04;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 Order Shipped Notification</h1>
        <p style="color: #777;">Admin Notification</p>
    </div>

    <div class="content">
        <p>Dear Admin,</p>

        <p>Order <strong>#{{ $order->order_id }}</strong> has been marked as <strong style="color:#e85d04;">SHIPPED</strong>.</p>

        <div class="order-details">
            <h3>Order Details</h3>
            <table>
                <tr>
                    <td class="label">Order ID:</td>
                    <td>#{{ $order->order_id }}</td>
                </tr>
                <tr>
                    <td class="label">Customer:</td>
                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td>{{ $order->user->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Phone:</td>
                    <td>{{ $order->user->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Order Date:</td>
                    <td>{{ $order->created_at->format('d M, Y h:i A') }}</td>
                </tr>
                <tr>
                    <td class="label">Shipped Date:</td>
                    <td>{{ $order->shipped_at ? $order->shipped_at->format('d M, Y h:i A') : 'Today' }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Method:</td>
                    <td>{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Status:</td>
                    <td><span style="background: {{ $order->payment_status === 'paid' ? '#4caf50' : '#ff9800' }}; color:#fff; padding:3px 10px; border-radius:3px;">{{ ucfirst($order->payment_status ?? 'pending') }}</span></td>
                </tr>
                <tr>
                    <td class="label">Order Status:</td>
                    <td><span style="background: #e85d04; color:#fff; padding:3px 10px; border-radius:3px;">Shipped</span></td>
                </tr>
                @if($order->tracking_id)
                <tr>
                    <td class="label">Tracking ID:</td>
                    <td><strong>{{ $order->tracking_id }}</strong></td>
                </tr>
                @endif
                @if($order->courier_name)
                <tr>
                    <td class="label">Courier:</td>
                    <td>{{ $order->courier_name }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Subtotal:</td>
                    <td>₹{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Discount:</td>
                    <td>₹{{ number_format($order->discount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Shipping:</td>
                    <td>₹{{ number_format($order->shipping_charge ?? 0, 2) }}</td>
                </tr>
                <tr style="font-size: 1.2em; font-weight: bold; background: #e85d04; color: #fff;">
                    <td class="label">Grand Total:</td>
                    <td>₹{{ number_format($order->grand_total, 2) }}</td>
                </tr>
            </table>
        </div>

        <h3>Order Items</h3>
        <table style="width:100%; border-collapse: collapse; margin: 15px 0;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Product</th>
                    <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Qty</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Price</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                            {{ $item->product->name ?? 'Product' }}
                            @if($item->variant)
                                <br><small style="color:#777;">{{ $item->variant->name ?? '' }}</small>
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: center; border-bottom: 1px solid #ddd;">{{ $item->quantity }}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;">₹{{ number_format($item->price, 2) }}</td>
                        <td style="padding: 10px; text-align: right; border-bottom: 1px solid #ddd;">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 15px 0;">
            <h4>📋 Shipping Address</h4>
            @if($order->shippingAddress)
                <p style="margin: 0;">
                    {{ $order->shippingAddress->name }}<br>
                    {{ $order->shippingAddress->address }}<br>
                    {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} - {{ $order->shippingAddress->pincode }}<br>
                    Phone: {{ $order->shippingAddress->phone }}
                </p>
            @else
                <p>No shipping address recorded.</p>
            @endif
        </div>

        <div class="action-buttons">
            <a href="{{ route('order.show', $order->id) }}" class="btn">View Order in Admin</a>
            <a href="{{ route('invoice', $order->id) }}" class="btn btn-secondary">Download Invoice</a>
        </div>

        <p>Notification sent to customer: <strong>{{ $order->user->email ?? 'N/A' }}</strong></p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p><small>This is an automated notification sent to the admin regarding order #{{ $order->order_id }}.</small></p>
    </div>
</body>
</html>