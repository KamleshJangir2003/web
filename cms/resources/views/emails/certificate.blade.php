<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Congratulations!</h1>
        </div>
        <div class="content">
            <h2>Dear {{ $intern->name }},</h2>
            
            <p>We are pleased to inform you that you have successfully completed your internship at <strong>KWIKSTER</strong>!</p>
            
            <p>Your dedication, hard work, and commitment throughout the internship period have been commendable. We hope this experience has been valuable for your professional growth.</p>
            
            <p><strong>Internship Details:</strong></p>
            <ul>
                <li><strong>Course:</strong> {{ $intern->course ?? 'N/A' }}</li>
                <li><strong>Duration:</strong> {{ $intern->internship_duration ?? 'N/A' }} months</li>
                <li><strong>Period:</strong> {{ $intern->start_date ? $intern->start_date->format('d M Y') : 'N/A' }} to {{ $intern->completion_date ? \Carbon\Carbon::parse($intern->completion_date)->format('d M Y') : 'N/A' }}</li>
                @if($intern->performance_rating)
                <li><strong>Performance Rating:</strong> {{ $intern->performance_rating }}</li>
                @endif
            </ul>
            
            <p>Your <strong>Internship Completion Certificate</strong> is attached to this email. You can download and use it for your future endeavors.</p>
            
            <p>We wish you all the best for your future career!</p>
            
            <p>Best Regards,<br>
            <strong>Team KWIKSTER</strong></p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} KWIKSTER. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
