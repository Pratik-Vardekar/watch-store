<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚠️ Order Cancelled Alert #{{ $order->order_id ?? $order->id }} - The Watch Store Admin</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#222;-webkit-font-smoothing:antialiased;">
    @php
    $orderNum = $order->order_id ?? $order->id;
    $orderTotal = (float) ($order->grand_total ?? $order->total ?? 0);
    $paidAmount = (float) ($order->paid_amount ?? 0);
    $isPaid = ($order->payment_status ?? '') === 'paid' && $paidAmount > 0;
    @endphp
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);border:1px solid #e5e7eb;">



                    <!-- Admin Notification Banner -->
                    <tr>
                        <td style="background-color:#120f0f;padding:24px 28px;text-align:left;color:#ffffff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="150" align="left" valign="middle" style="padding-right:20px;">
                                        <a href="{{ route('admin.dashboard') }}" target="_blank" style="text-decoration:none;display:block;">
                                            <img src="{{ asset('frontend/assets/images/tws_logo.png') }}" alt="The Watch Store" width="150" style="max-width:150px;width:100%;height:auto;display:block;border:0;" />
                                        </a>
                                    </td>
                                    <td align="left" valign="middle">
                                        <span style="display:inline-block;background-color:rgba(239,68,68,0.2);color:#fca5a5;border:1px solid rgba(239,68,68,0.4);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:10px;">
                                            ⚠️ Admin Cancellation Alert
                                        </span>
                                        <h1 style="margin:0 0 6px;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">
                                            Order Cancelled Alert
                                        </h1>
                                        <p style="margin:0;font-size:13px;color:#d1d5db;">
                                            📅 Cancelled at: <strong style="color:#ffffff;">{{ $cancelledAt }}</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:28px 28px 20px;">

                            <!-- Urgent Refund Notice (if paid) -->
                            @if($isPaid)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fef2f2;border:2px solid #ef4444;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <h4 style="margin:0 0 4px;font-size:15px;color:#b91c1c;font-weight:800;">🚨 REFUND ACTION REQUIRED</h4>
                                        <p style="margin:0;font-size:13px;color:#7f1d1d;line-height:1.5;">
                                            Customer has paid <strong>₹{{ number_format($paidAmount, 2) }}</strong> via {{ ucfirst($order->payment_method ?? 'Online') }}. Please initiate the refund in the payment gateway.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Summary Card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="left">
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Order ID</span>
                                                    <span style="font-size:18px;font-weight:800;color:#dc2626;">#{{ $orderNum }}</span>
                                                </td>
                                                <td align="right">
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Status</span>
                                                    <span style="display:inline-block;background-color:#fee2e2;color:#b91c1c;font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;">
                                                        Cancelled
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Customer Information Box -->
                            <h3 style="margin:0 0 12px;font-size:14px;color:#111827;text-transform:uppercase;letter-spacing:0.5px;">👤 Customer &amp; Order Information</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;font-size:13px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;width:35%;">Customer Name</td>
                                                <td style="padding:5px 0;color:#1e293b;font-weight:700;">{{ $customerName }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Email</td>
                                                <td style="padding:5px 0;">
                                                    @if(!empty($order->user->email))
                                                    <a href="mailto:{{ $order->user->email }}" style="color:#dc2626;font-weight:600;text-decoration:none;">{{ $order->user->email }}</a>
                                                    @else
                                                    <span style="color:#94a3b8;">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Phone</td>
                                                <td style="padding:5px 0;">
                                                    @php $phone = $order->shippingAddress->phone ?? ($order->user->phone ?? ''); @endphp
                                                    @if(!empty($phone))
                                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" style="color:#dc2626;font-weight:600;text-decoration:none;">{{ $phone }}</a>
                                                    @else
                                                    <span style="color:#94a3b8;">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Original Order Date</td>
                                                <td style="padding:5px 0;color:#1e293b;">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y • h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Payment Method</td>
                                                <td style="padding:5px 0;color:#1e293b;font-weight:600;">{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Total Amount</td>
                                                <td style="padding:5px 0;color:#111827;font-weight:800;font-size:15px;">₹{{ number_format($orderTotal, 2) }}</td>
                                            </tr>
                                            @if(!empty($order->cancel_request_reason))
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Reason</td>
                                                <td style="padding:5px 0;color:#b91c1c;font-weight:600;">{{ $order->cancel_request_reason }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Cancelled Items -->
                            @if(count($orderItems) > 0)
                            <h3 style="margin:0 0 12px;font-size:14px;color:#111827;text-transform:uppercase;letter-spacing:0.5px;">📦 Cancelled Items</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:24px;font-size:13px;">
                                <thead>
                                    <tr style="background-color:#f1f5f9;">
                                        <th align="left" style="padding:10px 12px;color:#475569;font-weight:700;border-radius:6px 0 0 6px;">Product</th>
                                        <th align="center" style="padding:10px 12px;color:#475569;font-weight:700;">Qty</th>
                                        <th align="right" style="padding:10px 12px;color:#475569;font-weight:700;border-radius:0 6px 6px 0;">Total</th>
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
                                            ₹{{ number_format((float) ($item->total ?? ($item->price * $item->quantity)), 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif

                            <!-- Admin Quick Action Button -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px;text-align:center;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/admin/order/' . ($order->id ?? '')) }}" style="display:inline-block;background-color:#dc2626;color:#ffffff;padding:12px 24px;font-size:13px;font-weight:700;text-decoration:none;border-radius:8px;box-shadow:0 4px 12px rgba(220,38,38,0.25);">
                                            📋 View Order in Admin Panel
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:20px 28px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 4px;color:#64748b;font-weight:600;">The Watch Store Management System</p>
                            <p style="margin:0;">Automated cancellation alert sent at {{ $cancelledAt }}.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>