<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Interview Scheduled - Kwikster</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
            color: #2c3e50;
            background: #f4f6f9;
            padding: 20px;
        }
        .email-container {
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 20px solid #7c3aed;
        }
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .content {
            padding: 50px 40px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        .intro {
            font-size: 16px;
            margin-bottom: 30px;
            color: #34495e;
        }
        .details-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
            padding: 30px;
            border-radius: 12px;
            margin: 25px 0;
            border: 1px solid #e3f2fd;
        }
        .details-card h3 {
            color: #4f46e5;
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-item {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 12px;
    font-size: 16px;
}

.detail-item strong {
    color: #2c3e50;
    font-weight: 600;
    margin-right: 6px;
}

.detail-item span {
    flex: 1;
    word-break: break-word;
}

@media (max-width: 600px) {
    .detail-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .detail-item strong {
        margin-bottom: 4px;
    }
}
        .meeting-link {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
            border: 1px solid #2196f3;
        }
        .meeting-link h3 {
            color: #1976d2;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .meeting-link a {
            color: #1976d2;
            text-decoration: none;
            font-weight: 600;
            word-break: break-all;
        }
        .meeting-link a:hover {
            text-decoration: underline;
        }
        .instructions {
            background: #fff3e0;
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
            border-left: 5px solid #ff9800;
        }
        .instructions h3 {
            color: #f57c00;
            margin-bottom: 15px;
        }
        .good-luck {
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            color: #27ae60;
            margin: 30px 0;
            padding: 20px;
            background: #e8f5e8;
            border-radius: 12px;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .company-info {
            color: #495057;
            font-size: 14px;
            font-weight: 500;
        }
        @media (max-width: 600px) {
            .email-container { margin: 10px; border-radius: 12px; }
            .header { padding: 30px 20px; }
            .content { padding: 30px 25px; }
            .details-card, .meeting-link, .instructions { padding: 20px; }
            .detail-item strong { min-width: 120px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎯 Interview Scheduled</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear <strong>{{ $lead->name }}</strong>,
            </div>
            
            <div class="intro">
                Your interview has been scheduled for the <strong>{{ $interview->job_role }}</strong> position. We're excited to meet you!
            </div>
            
            <div class="details-card">
                <h3>📋 Interview Details</h3>
                <div class="detail-item">
                    <strong>📅 Date:</strong> {{ date('d M Y', strtotime($interview->interview_date)) }}
                </div>
                <div class="detail-item">
                    <strong>⏰ Time:</strong> {{ \Carbon\Carbon::parse($interview->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($interview->end_time)->format('g:i A') }}
                </div>
                <div class="detail-item">
                    <strong>👤 Interviewer:</strong> {{ $interview->interviewer }}
                </div>
                <div class="detail-item">
                    <strong>🔄 Round:</strong> {{ $interview->interview_round }}
                </div>
                <div class="detail-item">
                    <strong>💻 Mode:</strong> {{ $interview->interview_mode }}
                </div>
                @if($interview->meeting_platform)
                <div class="detail-item">
                    <strong>🖥️ Platform:</strong> {{ $interview->meeting_platform }}
                </div>
                @endif
            </div>
            
            @if($interview->meeting_link)
            <div class="meeting-link">
                <h3>🔗 Meeting Link</h3>
                <p><a href="{{ $interview->meeting_link }}" target="_blank">{{ $interview->meeting_link }}</a></p>
                <p style="margin-top: 10px; font-size: 14px; color: #666;"><em>Please join 10 minutes before the scheduled time.</em></p>
            </div>
            @endif
            
            @if($interview->instructions)
            <div class="instructions">
                <h3>📝 Special Instructions</h3>
                <p>{{ $interview->instructions }}</p>
            </div>
            @endif
            
            <div class="good-luck">
                🍀 Good luck with your interview! We're looking forward to meeting you.
            </div>
        </div>
        
        <div class="footer">
            <p>Best regards,<br><strong>Kwikster Team</strong></p>
            <div class="company-info">
                Kwikster Innovative Optimisations Pvt Ltd<br>
                Professional Recruitment Services
            </div>
        </div>
    </div>
</body>
</html>