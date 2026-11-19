<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Shipped</title>
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
        .tracking-info {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #4caf50;
        }
        .button {
            display: inline-block;
            background: #e85d04;
            color: #fff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background: #d35400;
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
        <h1>🚚 Order Shipped!</h1>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $order->user->name ?? 'Customer' }}</strong>,</p>

        <p>Great news! Your order <strong>#{{ $order->order_id }}</strong> has been shipped and is on its way to you.</p>

        <div class="tracking-info">
            @if($order->tracking_id)
                <p><strong>Tracking Number:</strong> {{ $order->tracking_id }}</p>
            @endif
            @if($order->courier_name)
                <p><strong>Courier:</strong> {{ $order->courier_name }}</p>
            @endif
            @if($order->tracking_url)
                <p><strong>Track Your Order:</strong> <a href="{{ $order->tracking_url }}" target="_blank">{{ $order->tracking_url }}</a></p>
            @endif
        </div>

        <div class="order-details">
            <h3>Order Summary</h3>
            <table>
                <tr>
                    <td class="label">Order ID:</td>
                    <td>#{{ $order->order_id }}</td>
                </tr>
                <tr>
                    <td class="label">Order Date:</td>
                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Shipped Date:</td>
                    <td>{{ $order->shipped_at ? $order->shipped_at->format('d M, Y') : 'Today' }}</td>
                </tr>
                <tr>
                    <td class="label">Total Amount:</td>
                    <td><strong>₹{{ number_format($order->grand_total, 2) }}</strong></td>
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
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Subtotal:</td>
                    <td style="padding: 10px; text-align: right;">₹{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Discount:</td>
                    <td style="padding: 10px; text-align: right; color: #e85d04;">-₹{{ number_format($order->discount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Shipping:</td>
                    <td style="padding: 10px; text-align: right;">₹{{ number_format($order->shipping_charge ?? 0, 2) }}</td>
                </tr>
                <tr style="background: #e85d04; color: #fff;">
                    <td colspan="2" style="padding: 10px; text-align: right; font-weight: bold;">Grand Total:</td>
                    <td style="padding: 10px; text-align: right; font-weight: bold;">₹{{ number_format($order->grand_total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: center;">
            <a href="{{ route('myaccount.orders') }}" class="button">View My Orders</a>
        </div>

        <p style="margin-top: 20px;">
            <strong>Delivery Address:</strong><br>
            @if($order->shippingAddress)
                {{ $order->shippingAddress->name }}<br>
                {{ $order->shippingAddress->address }}<br>
                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}<br>
                {{ $order->shippingAddress->pincode }}<br>
                Phone: {{ $order->shippingAddress->phone }}
            @endif
        </p>

        <p>Your order is expected to be delivered within <strong>2-5 working days</strong>.</p>

        <p>
            If you have any questions about your order, please don't hesitate to 
            <a href="{{ route('contact.us') }}">contact us</a>.
        </p>

        <p>Thank you for shopping with us!</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>
            <small>
                This email was sent to {{ $order->user->email ?? '' }} regarding your order #{{ $order->order_id }}.
            </small>
        </p>
    </div>
</body>
</html>