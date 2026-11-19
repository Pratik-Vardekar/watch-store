<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Newsletter Subscriber - The Watch Store Admin</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#222;-webkit-font-smoothing:antialiased;">
    @php
    $joinedDate = optional($subscriber->created_at)->format('d M Y • h:i A') ?? date('d M Y • h:i A');
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
                                            📢 Newsletter Subscription
                                        </span>
                                        <h1 style="margin:0 0 6px;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">
                                            New Newsletter Subscriber
                                        </h1>
                                        <p style="margin:0;font-size:13px;color:#d1d5db;">
                                            📅 Subscribed on: <strong style="color:#ffffff;">{{ $joinedDate }}</strong>
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
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Subscriber ID</span>
                                                    <span style="font-size:18px;font-weight:800;color:#dc2626;">#SUB-{{ $subscriber->id }}</span>
                                                </td>
                                                <td align="right">
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Status</span>
                                                    <span style="display:inline-block;background-color:#dcfce7;color:#15803d;font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;">
                                                        Active
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Subscriber Details Box -->
                            <h3 style="margin:0 0 12px;font-size:14px;color:#111827;text-transform:uppercase;letter-spacing:0.5px;">👤 Subscriber Information</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;font-size:13px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;color:#64748b;width:35%;">Subscriber Email</td>
                                                <td style="padding:6px 0;">
                                                    <a href="mailto:{{ $subscriber->email }}" style="color:#dc2626;font-weight:700;text-decoration:none;font-size:14px;">{{ $subscriber->email }}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;color:#64748b;">Subscribed Date</td>
                                                <td style="padding:6px 0;color:#1e293b;">{{ $joinedDate }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0;font-size:13px;line-height:1.6;color:#6b7280;text-align:center;">
                                A new user has opted in to receive marketing emails and product updates.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc;padding:20px 28px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e5e7eb;">
                            <p style="margin:0 0 4px;color:#64748b;font-weight:600;">The Watch Store Management System</p>
                            <p style="margin:0;">Automated subscriber notification generated at {{ $joinedDate }}.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>