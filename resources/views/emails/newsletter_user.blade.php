<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to The Watch Store Newsletter</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#222;-webkit-font-smoothing:antialiased;">
    @php
    $joinedDate = optional($subscriber->created_at)->format('d M Y • h:i A') ?? date('d M Y • h:i A');
    @endphp
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);border:1px solid #e5e7eb;">



                    <!-- Status Banner -->
                    <tr>
                        <td style="background-color:#120f0f;padding:26px 28px;text-align:left;color:#ffffff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="150" align="left" valign="middle" style="padding-right:20px;">
                                        <a href="{{ url('/') }}" target="_blank" style="text-decoration:none;display:block;">
                                            <img src="{{ asset('frontend/assets/images/tws_logo.png') }}" alt="The Watch Store" width="150" style="max-width:150px;width:100%;height:auto;display:block;border:0;" />
                                        </a>
                                    </td>
                                    <td align="left" valign="middle">
                                        <span style="display:inline-block;background-color:rgba(220,38,38,0.2);color:#ff6b6b;border:1px solid rgba(220,38,38,0.4);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:10px;">
                                            🎁 VIP Membership
                                        </span>
                                        <h1 style="margin:0 0 6px;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">
                                            Welcome to The Watch Store!
                                        </h1>
                                        <p style="margin:0;font-size:13px;color:#d1d5db;">
                                            📅 Joined: <strong style="color:#ffffff;">{{ $joinedDate }}</strong>
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
                                Hello Watch Enthusiast,
                            </p>
                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Thank you for subscribing to <strong>The Watch Store</strong> newsletter! You are now part of our inner circle and will receive exclusive access to:
                            </p>

                            <!-- Perks Grid -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;font-size:13px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;color:#1e293b;">
                                                    🌟 <strong>New Arrivals:</strong> First look at luxury and trending watch collections
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;color:#1e293b;">
                                                    🏷️ <strong>Exclusive Offers:</strong> Member-only discounts and seasonal promotions
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;color:#1e293b;">
                                                    ⌚ <strong>Expert Care Guides:</strong> Horology insights and watch maintenance tips
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Subscribed Email Card -->
                            <p style="margin:0 0 22px;font-size:13px;color:#64748b;text-align:center;">
                                Subscribed Email: <strong style="color:#111827;">{{ $subscriber->email }}</strong>
                            </p>

                            <!-- CTA -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;text-align:center;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/') }}" style="display:inline-block;background-color:#dc2626;color:#ffffff;padding:12px 32px;font-size:14px;font-weight:700;text-decoration:none;border-radius:8px;box-shadow:0 4px 12px rgba(220,38,38,0.25);">
                                            Explore Watch Collections
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px 0 0;font-size:13px;line-height:1.6;color:#6b7280;text-align:center;">
                                Warm regards,<br>
                                <strong style="color:#111827;">Team The Watch Store</strong>
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