<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Alert #{{ $order->order_id }} - The Watch Store Admin</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#222;-webkit-font-smoothing:antialiased;">
    @php
    $gstBreakdown = $order->gstBreakdown();
    $shipping = $order->shippingAddress;
    $billing = $order->billingAddress;
    $orderDate = optional($order->created_at)->format('d M Y • h:i A') ?? date('d M Y • h:i A');

    $grandTotal = (float) $order->grand_total;
    $breakdown = $order->getPaymentBreakdown();
    $paidAmount = $breakdown['paid'];
    $dueAmount = $breakdown['due'];
    $isPaid = $breakdown['is_paid'];
    $isPartial = $breakdown['is_partial'];

    if ($isPartial) {
    $paymentMethodLabel = 'Partial Payment (Online Advance + COD)';
    } elseif (($order->payment_method ?? '') === 'razorpay') {
    $paymentMethodLabel = 'Online (Razorpay)';
    } elseif (strtoupper($order->payment_method ?? '') === 'COD') {
    $paymentMethodLabel = 'Cash on Delivery (COD)';
    } else {
    $paymentMethodLabel = ucfirst($order->payment_method ?? 'Cash on Delivery');
    }
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
                                        <span style="display:inline-block;background-color:rgba(220,38,38,0.2);color:#ff6b6b;border:1px solid rgba(220,38,38,0.4);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:10px;">
                                            🛡️ Admin Notification
                                        </span>
                                        <h1 style="margin:0 0 6px;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">
                                            New Order Received!
                                        </h1>
                                        <p style="margin:0;font-size:13px;color:#d1d5db;">
                                            📅 Placed on: <strong style="color:#ffffff;">{{ $orderDate }}</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:28px 28px 20px;">

                            <!-- Summary Bar -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="left">
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Order ID</span>
                                                    <span style="font-size:18px;font-weight:800;color:#dc2626;">#{{ $order->order_id }}</span>
                                                </td>
                                                <td align="right">
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Payment Status</span>
                                                    @if($isPaid || $dueAmount <= 0)
                                                        <span style="display:inline-block;background-color:#dcfce7;color:#15803d;font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;">
                                                        Paid in Full
                                                        </span>
                                                        @elseif($isPartial)
                                                        <span style="display:inline-block;background-color:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;">
                                                            Partially Paid
                                                        </span>
                                                        @else
                                                        <span style="display:inline-block;background-color:#fef3c7;color:#b45309;font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;">
                                                            Pending (COD)
                                                        </span>
                                                        @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>


                            <!-- Items Table -->
                            @if($order->items && $order->items->isNotEmpty())
                            <h3 style="margin:0 0 12px;font-size:14px;color:#111827;text-transform:uppercase;letter-spacing:0.5px;">📦 Order Items</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:24px;font-size:13px;">
                                <thead>
                                    <tr style="background-color:#f1f5f9;">
                                        <th align="left" style="padding:10px 12px;color:#475569;font-weight:700;border-radius:6px 0 0 6px;">Product</th>
                                        <th align="center" style="padding:10px 12px;color:#475569;font-weight:700;">Qty</th>
                                        <th align="right" style="padding:10px 12px;color:#475569;font-weight:700;border-radius:0 6px 6px 0;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td style="padding:12px 10px;border-bottom:1px solid #f1f5f9;color:#1e293b;">
                                            <strong>{{ $item->product->name ?? 'Product' }}</strong>
                                            @if(!empty($item->variant?->name))
                                            <br><small style="color:#64748b;">Variant: {{ $item->variant->name }}</small>
                                            @endif
                                        </td>
                                        <td align="center" style="padding:12px 10px;border-bottom:1px solid #f1f5f9;color:#334155;font-weight:600;">
                                            {{ $item->quantity }}
                                        </td>
                                        <td align="right" style="padding:12px 10px;border-bottom:1px solid #f1f5f9;color:#1e293b;font-weight:700;">
                                            ₹{{ number_format((float) $item->total, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif

                            <!-- Financial Breakdown -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;margin-bottom:24px;border-top:1px solid #e2e8f0;padding-top:10px;">
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">Subtotal</td>
                                    <td style="padding:6px 0;text-align:right;color:#1e293b;font-weight:600;">₹{{ number_format((float) $order->subtotal, 2) }}</td>
                                </tr>
                                @if((float) $order->discount > 0)
                                <tr>
                                    <td style="padding:6px 0;color:#dc2626;">Discount</td>
                                    <td style="padding:6px 0;text-align:right;color:#dc2626;font-weight:600;">-₹{{ number_format((float) $order->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">Shipping Charge</td>
                                    <td style="padding:6px 0;text-align:right;color:#1e293b;font-weight:600;">
                                        @if((float) $order->shipping_charge > 0)
                                        ₹{{ number_format((float) $order->shipping_charge, 2) }}
                                        @else
                                        <span style="color:#16a34a;font-weight:700;">FREE</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">{{ $gstBreakdown['gst_rate_label'] ?? 'GST' }}</td>
                                    <td style="padding:6px 0;text-align:right;color:#1e293b;font-weight:600;">₹{{ number_format((float) ($gstBreakdown['gst_total'] ?? 0), 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 0 6px;font-size:17px;font-weight:800;color:#111827;border-top:2px solid #111827;">Sub Total</td>
                                    <td style="padding:14px 0 6px;text-align:right;font-size:18px;font-weight:800;color:#111827;border-top:2px solid #111827;">₹{{ number_format((float) $order->grand_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0 4px;font-size:14px;color:#16a34a;font-weight:700;border-top:1px dashed #cbd5e1;">Paid Payment (Online)</td>
                                    <td style="padding:8px 0 4px;text-align:right;font-size:14px;color:#16a34a;font-weight:700;border-top:1px dashed #cbd5e1;">₹{{ number_format($paidAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0 6px;font-size:15px;color:{{ $dueAmount > 0 ? '#dc2626' : '#64748b' }};font-weight:800;">Due Payment {{ $dueAmount > 0 ? '(Collect on Delivery)' : '' }}</td>
                                    <td style="padding:4px 0 6px;text-align:right;font-size:16px;color:{{ $dueAmount > 0 ? '#dc2626' : '#64748b' }};font-weight:800;">₹{{ number_format($dueAmount, 2) }}</td>
                                </tr>
                            </table>


                            <!-- Payment Overview Notice -->
                            @if($isPartial)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eff6ff;border:1px solid #bfdbfe;border-left:4px solid #2563eb;border-radius:8px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0 0 4px;font-size:13px;font-weight:700;color:#1e40af;">
                                            ⚡ Partial Payment Order Notice
                                        </p>
                                        <p style="margin:0;font-size:13px;color:#1e3a8a;line-height:1.5;">
                                            Customer paid <strong>₹{{ number_format($paidAmount, 2) }}</strong> online in advance. <strong style="color:#dc2626;">₹{{ number_format($dueAmount, 2) }}</strong> remains due and must be collected upon delivery (COD).
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @elseif($dueAmount > 0)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fffbeb;border:1px solid #fde68a;border-left:4px solid #d97706;border-radius:8px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0 0 4px;font-size:13px;font-weight:700;color:#92400e;">
                                            ⚠️ Cash on Delivery (COD) Order
                                        </p>
                                        <p style="margin:0;font-size:13px;color:#78350f;line-height:1.5;">
                                            Full amount of <strong style="color:#dc2626;">₹{{ number_format($dueAmount, 2) }}</strong> must be collected from customer upon delivery.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @else
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #16a34a;border-radius:8px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0 0 4px;font-size:13px;font-weight:700;color:#166534;">
                                            ✅ Full Payment Received
                                        </p>
                                        <p style="margin:0;font-size:13px;color:#14532d;line-height:1.5;">
                                            Full order amount of <strong>₹{{ number_format($paidAmount, 2) }}</strong> was received online. No collection needed on delivery.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Shipping & Billing Address Grid -->
                            @if($shipping)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <h4 style="margin:0 0 8px;font-size:13px;color:#1e293b;text-transform:uppercase;letter-spacing:0.5px;">📍 Shipping Address</h4>
                                        <p style="margin:0;font-size:13px;line-height:1.6;color:#475569;">
                                            <strong>{{ $shipping->name }}</strong><br>
                                            @if(!empty($shipping->phone))Phone: {{ $shipping->phone }}<br>@endif
                                            {{ $shipping->address }}
                                            @if(!empty($shipping->landmark)), {{ $shipping->landmark }}@endif<br>
                                            {{ $shipping->city }}@if(!empty($shipping->state)), {{ $shipping->state }}@endif
                                            @if(!empty($shipping->pincode)) - {{ $shipping->pincode }}@endif
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Admin Quick Action Buttons -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px;text-align:center;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('order.show', $order->id) }}" style="display:inline-block;background-color:#dc2626;color:#ffffff;padding:12px 24px;font-size:13px;font-weight:700;text-decoration:none;border-radius:8px;margin-right:8px;box-shadow:0 4px 12px rgba(220,38,38,0.25);">
                                            📋 View Order in Admin
                                        </a>
                                        <a href="{{ route('order.pdf', $order->id) }}" style="display:inline-block;background-color:#1e293b;color:#ffffff;padding:12px 24px;font-size:13px;font-weight:700;text-decoration:none;border-radius:8px;">
                                            📄 Download Invoice
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
                            <p style="margin:0;">This is an automated administrative notification regarding order #{{ $order->order_id }}.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>