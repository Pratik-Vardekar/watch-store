<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order Has Shipped! - The Watch Store</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#222;-webkit-font-smoothing:antialiased;">
    @php
    $shippedDate = $order->shipped_at ? $order->shipped_at->format('d M Y • h:i A') : date('d M Y • h:i A');
    $shippingAddress = $order->shippingAddress ?? ($order->addresses ? $order->addresses->where('type', 'shipping')->first() : null);
    $gstBreakdown = $order->gstBreakdown();

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
                                        <span style="display:inline-block;background-color:rgba(22,163,74,0.2);color:#4ade80;border:1px solid rgba(74,222,128,0.4);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:10px;">
                                            🚚 Dispatched
                                        </span>
                                        <h1 style="margin:0 0 6px;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">
                                            Your Order Is On The Way!
                                        </h1>
                                        <p style="margin:0;font-size:13px;color:#d1d5db;">
                                            🕒 Shipped on: <strong style="color:#ffffff;">{{ $shippedDate }}</strong>
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
                                Dear <strong>{{ $order->user->name ?? 'Valued Customer' }}</strong>,
                            </p>
                            <p style="margin:0 0 22px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Great news! Your order <strong>#{{ $order->order_id }}</strong> has been carefully packed and handed over to our delivery partner. It will arrive at your doorstep soon.
                            </p>

                            <!-- Tracking Info Card -->
                            @if($order->tracking_id || $order->courier_name || $order->tracking_url)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <h4 style="margin:0 0 10px;font-size:14px;color:#166534;text-transform:uppercase;letter-spacing:0.5px;">📍 Tracking Details</h4>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;color:#1e293b;">
                                            @if($order->courier_name)
                                            <tr>
                                                <td style="padding:4px 0;color:#15803d;width:35%;"><strong>Courier Partner:</strong></td>
                                                <td style="padding:4px 0;font-weight:600;">{{ $order->courier_name }}</td>
                                            </tr>
                                            @endif
                                            @if($order->tracking_id)
                                            <tr>
                                                <td style="padding:4px 0;color:#15803d;"><strong>Tracking Number:</strong></td>
                                                <td style="padding:4px 0;font-weight:700;color:#166534;">{{ $order->tracking_id }}</td>
                                            </tr>
                                            @endif
                                            @if($order->tracking_url)
                                            <tr>
                                                <td colspan="2" style="padding-top:10px;">
                                                    <a href="{{ $order->tracking_url }}" target="_blank" style="display:inline-block;background-color:#16a34a;color:#ffffff;padding:8px 18px;font-size:13px;font-weight:700;text-decoration:none;border-radius:6px;">
                                                        Track Your Package &rarr;
                                                    </a>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Summary Bar -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="left">
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Order Number</span>
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
                            <h3 style="margin:0 0 12px;font-size:14px;color:#111827;text-transform:uppercase;letter-spacing:0.5px;">Shipped Items</h3>
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
                                            <br><small style="color:#64748b;">{{ $item->variant->name }}</small>
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
                                    <td style="padding:4px 0 6px;font-size:15px;color:{{ $dueAmount > 0 ? '#dc2626' : '#64748b' }};font-weight:800;">Due Payment {{ $dueAmount > 0 ? '(Payable on Delivery)' : '' }}</td>
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
                                            You paid <strong>₹{{ number_format($paidAmount, 2) }}</strong> online in advance. <strong style="color:#dc2626;">₹{{ number_format($dueAmount, 2) }}</strong> remains due and must be paid upon delivery (COD).
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
                                            Full amount of <strong style="color:#dc2626;">₹{{ number_format($dueAmount, 2) }}</strong> must be paid upon delivery.
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

                            <!-- Delivery Address -->
                            @if($shippingAddress)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <h4 style="margin:0 0 8px;font-size:13px;color:#1e293b;text-transform:uppercase;letter-spacing:0.5px;">📍 Delivery Address</h4>
                                        <p style="margin:0;font-size:13px;line-height:1.6;color:#475569;">
                                            <strong>{{ $shippingAddress->name }}</strong><br>
                                            @if(!empty($shippingAddress->phone))Phone: {{ $shippingAddress->phone }}<br>@endif
                                            {{ $shippingAddress->address }}<br>
                                            {{ $shippingAddress->city }}, {{ $shippingAddress->state }} - {{ $shippingAddress->pincode }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- CTA -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;text-align:center;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('frontend.order.details', $order->id) }}" style="display:inline-block;background-color:#dc2626;color:#ffffff;padding:12px 28px;font-size:14px;font-weight:700;text-decoration:none;border-radius:8px;box-shadow:0 4px 12px rgba(220,38,38,0.25);">
                                            View Order Status
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0;font-size:13px;line-height:1.6;color:#6b7280;text-align:center;">
                                Expected delivery within <strong>2-5 business days</strong>. If you have questions, please <a href="{{ route('contact.us') }}" style="color:#dc2626;text-decoration:none;font-weight:600;">contact support</a>.
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