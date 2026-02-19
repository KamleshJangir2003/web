<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2eacb3; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
        .footer { text-align: center; padding: 15px; font-size: 12px; color: #777; }
        .info-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #2eacb3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">KWIKSTER</h2>
            <p style="margin:5px 0 0;">Training Receipt</p>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $intern->name }}</strong>,</p>
            <p>Your training receipt is attached with this email.</p>
            
            <div class="info-box">
                <strong>Payment Summary:</strong><br>
                Training Amount: ₹{{ number_format($intern->stipend ?? 0) }}<br>
                Total Paid: ₹{{ number_format($intern->total_paid ?? 0) }}<br>
                Pending: ₹{{ number_format(($intern->stipend ?? 0) - ($intern->total_paid ?? 0)) }}
            </div>
            
            <p>Please find the detailed receipt attached as PDF.</p>
            <p>Best regards,<br><strong>KWIKSTER Team</strong></p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Kwikster. All rights reserved.
        </div>
    </div>
</body>
</html>
