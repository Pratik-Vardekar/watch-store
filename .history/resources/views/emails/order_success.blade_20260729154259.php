<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#222;">
    @php $gstBreakdown = $order->gstBreakdown(); @endphp
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#E85D04;padding:24px 28px;text-align:center;">
                            <h1 style="margin:0;font-size:22px;line-height:1.3;color:#ffffff;">Cheriks</h1>
                            <p style="margin:8px 0 0;font-size:14px;color:#ffe8d6;">Order Confirmation</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 12px;font-size:16px;">
                                Hi {{ $order->user->name ?? 'there' }},
                            </p>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.5;color:#444;">
                                Thank you for your order! We’ve received it and will start packing your items shortly.
                                @if(!empty($order->invoice_no))
                                    Your invoice <strong>{{ $order->invoice_no }}</strong> is attached to this email.
                                @else
                                    Your invoice PDF is attached to this email.
                                @endif
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fff7f2;border-radius:6px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <p style="margin:0 0 6px;font-size:13px;color:#666;">Order ID</p>
                                        <p style="margin:0;font-size:18px;font-weight:bold;color:#E85D04;">{{ $order->order_id }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;font-size:14px;">
                                <tr>
                                    <td style="padding:6px 0;color:#666;">Payment method</td>
                                    <td style="padding:6px 0;text-align:right;font-weight:bold;">
                                        @if(($order->payment_method ?? '') === 'razorpay')
                                            Razorpay ({{ ucfirst($order->payment_status ?? 'pending') }})
                                        @else
                                            Cash on Delivery
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#666;">Order status</td>
                                    <td style="padding:6px 0;text-align:right;font-weight:bold;">{{ ucfirst($order->order_status ?? 'pending') }}</td>
                                </tr>
                            </table>

                            @if($order->items->isNotEmpty())
                            <h2 style="margin:0 0 12px;font-size:16px;">Items</h2>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;font-size:14px;">
                                <thead>
                                    <tr>
                                        <th align="left" style="padding:8px 0;border-bottom:1px solid #eee;color:#666;font-weight:normal;">Product</th>
                                        <th align="center" style="padding:8px 0;border-bottom:1px solid #eee;color:#666;font-weight:normal;">Qty</th>
                                        <th align="right" style="padding:8px 0;border-bottom:1px solid #eee;color:#666;font-weight:normal;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td style="padding:10px 0;border-bottom:1px solid #f0f0f0;">
                                            {{ $item->product->name ?? 'Product' }}
                                        </td>
                                        <td align="center" style="padding:10px 0;border-bottom:1px solid #f0f0f0;">{{ $item->quantity }}</td>
                                        <td align="right" style="padding:10px 0;border-bottom:1px solid #f0f0f0;">₹{{ number_format((float) $item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:4px 0;color:#666;">Subtotal</td>
                                    <td style="padding:4px 0;text-align:right;">₹{{ number_format((float) $order->subtotal, 2) }}</td>
                                </tr>
                                @if((float) $order->discount > 0)
                                <tr>
                                    <td style="padding:4px 0;color:#666;">Discount</td>
                                    <td style="padding:4px 0;text-align:right;">-₹{{ number_format((float) $order->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:4px 0;color:#666;">Shipping</td>
                                    <td style="padding:4px 0;text-align:right;">₹{{ number_format((float) $order->shipping_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0;color:#666;">{{ $gstBreakdown['gst_rate_label'] }}</td>
                                    <td style="padding:4px 0;text-align:right;">₹{{ number_format((float) $gstBreakdown['gst_total'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0 0;font-size:16px;font-weight:bold;">Grand total</td>
                                    <td style="padding:10px 0 0;text-align:right;font-size:16px;font-weight:bold;color:#E85D04;">₹{{ number_format((float) $order->grand_total, 2) }}</td>
                                </tr>
                            </table>

                            @php $shipping = $order->shippingAddress; @endphp
                            @if($shipping)
                            <h2 style="margin:0 0 8px;font-size:16px;">Shipping address</h2>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#444;">
                                {{ $shipping->name }}<br>
                                @if(!empty($shipping->phone)){{ $shipping->phone }}<br>@endif
                                {{ $shipping->address }}
                                @if(!empty($shipping->landmark)), {{ $shipping->landmark }}@endif<br>
                                {{ $shipping->city }}@if(!empty($shipping->state)), {{ $shipping->state }}@endif
                                @if(!empty($shipping->pincode)) — {{ $shipping->pincode }}@endif
                            </p>
                            @endif

                            <p style="margin:0;font-size:13px;line-height:1.5;color:#777;">
                                If you have any questions, just reply to this email. Thank you for shopping with Cheriks!
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
