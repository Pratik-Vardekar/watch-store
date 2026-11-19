<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancellation Notice - The Watch Store</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#222;-webkit-font-smoothing:antialiased;">
    @php
    $orderNum = $order->order_id ?? $order->id;
    $orderTotal = (float) ($order->grand_total ?? $order->total ?? 0);
    $refundAmount = (float) ($order->paid_amount ?? $orderTotal);
    @endphp
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);border:1px solid #e5e7eb;">



                    <!-- Status Banner -->
                    <tr>
                        <td style="background-color:#120f0f;padding:24px 28px;text-align:left;color:#ffffff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="150" align="left" valign="middle" style="padding-right:20px;">
                                        <a href="{{ url('/') }}" target="_blank" style="text-decoration:none;display:block;">
                                            <img src="{{ asset('frontend/assets/images/tws_logo.png') }}" alt="The Watch Store" width="150" style="max-width:150px;width:100%;height:auto;display:block;border:0;" />
                                        </a>
                                    </td>
                                    <td align="left" valign="middle">
                                        <span style="display:inline-block;background-color:rgba(239,68,68,0.2);color:#fca5a5;border:1px solid rgba(239,68,68,0.4);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:10px;">
                                            Order Cancelled
                                        </span>
                                        <h1 style="margin:0 0 6px;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">
                                            Order Cancellation Confirmed
                                        </h1>
                                        <p style="margin:0;font-size:13px;color:#d1d5db;">
                                            📅 Cancelled on: <strong style="color:#ffffff;">{{ $cancelledAt }}</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:28px 28px 20px;">
                            <p style="margin:0 0 14px;font-size:16px;color:#111827;line-height:1.5;">
                                Dear <strong>{{ $customerName }}</strong>,
                            </p>
                            <p style="margin:0 0 22px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Your order <strong>#{{ $orderNum }}</strong> has been cancelled as requested. We're sorry things didn't work out this time and hope to welcome you back soon!
                            </p>

                            <!-- Key Cancellation Summary Box -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="left">
                                                    <span style="font-size:12px;color:#991b1b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Order ID</span>
                                                    <span style="font-size:18px;font-weight:800;color:#dc2626;">#{{ $orderNum }}</span>
                                                </td>
                                                <td align="right">
                                                    <span style="font-size:12px;color:#991b1b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Status</span>
                                                    <span style="display:inline-block;background-color:#fee2e2;color:#b91c1c;font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;">
                                                        Cancelled
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Refund Information (if applicable) -->
                            @if(($order->payment_status ?? '') === 'paid')
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <h4 style="margin:0 0 6px;font-size:14px;color:#166534;font-weight:700;">💳 Refund Information</h4>
                                        <p style="margin:0;font-size:13px;line-height:1.6;color:#1e293b;">
                                            A refund of <strong>₹{{ number_format($refundAmount, 2) }}</strong> has been initiated to your original payment method. Please allow <strong>5-7 business days</strong> for the amount to reflect in your account.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Items Summary -->
                            @if(count($orderItems) > 0)
                            <h3 style="margin:0 0 12px;font-size:14px;color:#111827;text-transform:uppercase;letter-spacing:0.5px;">Cancelled Items</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:24px;font-size:13px;">
                                <thead>
                                    <tr style="background-color:#f1f5f9;">
                                        <th align="left" style="padding:10px 12px;color:#475569;font-weight:700;border-radius:6px 0 0 6px;">Product</th>
                                        <th align="center" style="padding:10px 12px;color:#475569;font-weight:700;">Qty</th>
                                        <th align="right" style="padding:10px 12px;color:#475569;font-weight:700;border-radius:0 6px 6px 0;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderItems as $item)
                                    <tr>
                                        <td style="padding:12px 10px;border-bottom:1px solid #f1f5f9;color:#1e293b;">
                                            <strong>{{ $item->product->name ?? ($item->name ?? 'Product') }}</strong>
                                        </td>
                                        <td align="center" style="padding:12px 10px;border-bottom:1px solid #f1f5f9;color:#334155;font-weight:600;">
                                            {{ $item->quantity }}
                                        </td>
                                        <td align="right" style="padding:12px 10px;border-bottom:1px solid #f1f5f9;color:#1e293b;font-weight:700;">
                                            ₹{{ number_format((float) $item->price, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif

                            <!-- CTA -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;text-align:center;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/') }}" style="display:inline-block;background-color:#111827;color:#ffffff;padding:12px 28px;font-size:14px;font-weight:700;text-decoration:none;border-radius:8px;">
                                            Explore Our Collection
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0;font-size:13px;line-height:1.6;color:#6b7280;text-align:center;">
                                If you did not request this cancellation or have questions, please <a href="{{ route('contact.us') }}" style="color:#dc2626;text-decoration:none;font-weight:600;">contact our support team</a> immediately.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:20px 28px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 4px;color:#64748b;font-weight:600;">The Watch Store</p>
                            <p style="margin:0;">&copy; {{ date('Y') }} The Watch Store. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>