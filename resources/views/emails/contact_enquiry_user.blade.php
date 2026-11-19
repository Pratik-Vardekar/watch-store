<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You For Contacting Us - The Watch Store</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#222;-webkit-font-smoothing:antialiased;">
    @php
    $submissionDate = optional($enquiry->created_at)->format('d M Y • h:i A') ?? date('d M Y • h:i A');
    @endphp
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);border:1px solid #e5e7eb;">



                    <!-- Status Banner -->
                    <tr>
                        <td style="background-color:#120f0f;padding:24px 28px;text-align:left;color:#ffffff;">
                            <a href="{{ url('/') }}" target="_blank" style="text-decoration:none;display:block;margin-bottom:20px;">
                                <img src="{{ asset('frontend/assets/images/tws_logo.png') }}" alt="The Watch Store" width="170" style="max-width:170px;width:100%;height:auto;display:block;border:0;" />
                            </a>
                            <span style="display:inline-block;background-color:rgba(220,38,38,0.2);color:#ff6b6b;border:1px solid rgba(220,38,38,0.4);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:4px 14px;border-radius:20px;margin-bottom:10px;">
                                Enquiry Received
                            </span>
                            <h1 style="margin:0 0 6px;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">
                                We Have Received Your Message
                            </h1>
                            <p style="margin:0;font-size:13px;color:#d1d5db;">
                                📅 Submitted on: <strong style="color:#ffffff;">{{ $submissionDate }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:28px 28px 20px;">
                            <p style="margin:0 0 14px;font-size:16px;color:#111827;line-height:1.5;">
                                Hi <strong>{{ $enquiry->full_name }}</strong>,
                            </p>
                            <p style="margin:0 0 22px;font-size:14px;line-height:1.6;color:#4b5563;">
                                Thank you for contacting <strong>The Watch Store</strong>. Our support team has received your enquiry and is reviewing it. We will get back to you shortly via email or phone.
                            </p>

                            <!-- Enquiry Reference Card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="left">
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Enquiry Reference</span>
                                                    <span style="font-size:17px;font-weight:800;color:#dc2626;">#ENQ-{{ $enquiry->id }}</span>
                                                </td>
                                                <td align="right">
                                                    <span style="font-size:12px;color:#64748b;text-transform:uppercase;font-weight:600;display:block;margin-bottom:3px;">Status</span>
                                                    <span style="display:inline-block;background-color:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;">
                                                        Under Review
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Submitted Details -->
                            <h3 style="margin:0 0 12px;font-size:14px;color:#111827;text-transform:uppercase;letter-spacing:0.5px;">📋 Details Provided</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:24px;font-size:13px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;width:35%;">Name</td>
                                                <td style="padding:5px 0;color:#1e293b;font-weight:700;">{{ $enquiry->full_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Email Address</td>
                                                <td style="padding:5px 0;color:#1e293b;font-weight:600;">{{ $enquiry->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Contact Number</td>
                                                <td style="padding:5px 0;color:#1e293b;font-weight:600;">{{ $enquiry->contact_no }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0;color:#64748b;">Submission Time</td>
                                                <td style="padding:5px 0;color:#1e293b;">{{ $submissionDate }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Message / Comment Box -->
                            <h3 style="margin:0 0 10px;font-size:14px;color:#111827;text-transform:uppercase;letter-spacing:0.5px;">💬 Your Message</h3>
                            <div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-left:4px solid #dc2626;padding:16px 18px;border-radius:6px;font-size:13px;line-height:1.6;color:#334155;margin-bottom:24px;">
                                @if($enquiry->comment)
                                {!! nl2br(e($enquiry->comment)) !!}
                                @else
                                <em style="color:#94a3b8;">No specific comment provided.</em>
                                @endif
                            </div>

                            <!-- CTA -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;text-align:center;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/') }}" style="display:inline-block;background-color:#dc2626;color:#ffffff;padding:12px 28px;font-size:14px;font-weight:700;text-decoration:none;border-radius:8px;box-shadow:0 4px 12px rgba(220,38,38,0.25);">
                                            Visit The Watch Store
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:16px 0 0;font-size:13px;line-height:1.6;color:#6b7280;text-align:center;">
                                Warm regards,<br>
                                <strong style="color:#111827;">The Watch Store Support Team</strong>
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