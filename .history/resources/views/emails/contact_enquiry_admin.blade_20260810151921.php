<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Enquiry</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#222;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#120f0f;padding:24px 28px;text-align:center;">
                            <h1 style="margin:0;font-size:22px;line-height:1.3;color:#ffffff;">
                                Watches Admin
                            </h1>
                            <p style="margin:8px 0 0;font-size:14px;color:#ffb300;">
                                New Contact Enquiry
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.5;color:#444;">
                                A new enquiry was submitted from the Watches website contact form.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#e9ecef;border-radius:6px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <p style="margin:0 0 6px;font-size:13px;color:#888;">Enquiry ID</p>
                                        <p style="margin:0;font-size:18px;font-weight:bold;color:#ffb300;">#{{ $enquiry->id }}</p>
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin:0 0 12px;font-size:16px;">Contact Details</h2>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;font-size:14px;">
                                <tr>
                                    <td style="padding:6px 0;color:#666;width:120px;">Name</td>
                                    <td style="padding:6px 0;text-align:right;font-weight:bold;">{{ $enquiry->full_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#666;">Email</td>
                                    <td style="padding:6px 0;text-align:right;font-weight:bold;">
                                        <a href="mailto:{{ $enquiry->email }}" style="color:#120f0f;text-decoration:none;">{{ $enquiry->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#666;">Contact No.</td>
                                    <td style="padding:6px 0;text-align:right;font-weight:bold;">
                                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $enquiry->contact_no) }}" style="color:#120f0f;text-decoration:none;">{{ $enquiry->contact_no }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#666;">Submitted</td>
                                    <td style="padding:6px 0;text-align:right;font-weight:bold;">{{ optional($enquiry->created_at)->format('d M Y, h:i A') }}</td>
                                </tr>
                            </table>

                            <h2 style="margin:0 0 12px;font-size:16px;">Comment</h2>
                            <div style="padding:14px 16px;background:#fafafa;border-radius:6px;font-size:14px;line-height:1.5;color:#444;">
                                @if($enquiry->comment)
                                    {!! nl2br(e($enquiry->comment)) !!}
                                @else
                                    <em style="color:#999;">No comment provided.</em>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:16px 28px;text-align:center;font-size:12px;color:#999;">
                            © {{ date('Y') }} Watches. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
