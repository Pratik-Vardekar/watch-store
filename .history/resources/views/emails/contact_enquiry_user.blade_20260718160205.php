<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#222;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#E85D04;padding:24px 28px;text-align:center;">
                            <h1 style="margin:0;font-size:22px;line-height:1.3;color:#ffffff;">Cheriks</h1>
                            <p style="margin:8px 0 0;font-size:14px;color:#ffe8d6;">Thank You</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 12px;font-size:16px;">
                                Hi {{ $enquiry->full_name }},
                            </p>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.5;color:#444;">
                                Thank you for reaching out to us! We have received your message and will get back to you shortly.
                            </p>

                            <h2 style="margin:0 0 12px;font-size:16px;">Your message</h2>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;font-size:14px;">
                                <tr>
                                    <td style="padding:6px 0;color:#666;width:120px;">Name</td>
                                    <td style="padding:6px 0;font-weight:bold;">{{ $enquiry->full_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#666;">Email</td>
                                    <td style="padding:6px 0;font-weight:bold;">{{ $enquiry->email }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#666;">Contact No.</td>
                                    <td style="padding:6px 0;font-weight:bold;">{{ $enquiry->contact_no }}</td>
                                </tr>
                                @if($enquiry->comment)
                                <tr>
                                    <td style="padding:6px 0;color:#666;vertical-align:top;">Comment</td>
                                    <td style="padding:6px 0;">{!! nl2br(e($enquiry->comment)) !!}</td>
                                </tr>
                                @endif
                            </table>

                            <p style="margin:0;font-size:14px;line-height:1.5;color:#666;">
                                Warm regards,<br>
                                <strong>Team Cheriks</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fafafa;padding:16px 28px;text-align:center;font-size:12px;color:#999;">
                            © {{ date('Y') }} Cheriks. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
