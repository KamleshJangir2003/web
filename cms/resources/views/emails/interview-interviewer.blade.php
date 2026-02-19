<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Interview Assignment - Kwikster</title>
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
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #16a34a 0%, #059669 100%);
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
            border-top: 20px solid #059669;
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
        .candidate-card {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            padding: 30px;
            border-radius: 12px;
            margin: 25px 0;
            border: 1px solid #f39c12;
        }
        .candidate-card h3 {
            color: #d68910;
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .details-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
            padding: 30px;
            border-radius: 12px;
            margin: 25px 0;
            border: 1px solid #e3f2fd;
        }
        .details-card h3 {
            color: #16a34a;
            font-size: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 16px;
        }
        .detail-item strong {
            min-width: 140px;
            color: #2c3e50;
            font-weight: 600;
        }
        .meeting-link a {
            color: #16a34a;
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
        .reminder {
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            color: #16a34a;
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
            .candidate-card, .details-card, .instructions { padding: 20px; }
            .detail-item strong { min-width: 120px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📋 Interview Assignment</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear Interviewer,
            </div>
            
            <div class="intro">
                You have been assigned to conduct an interview. Please review the details below and prepare accordingly.
            </div>
            
            <div class="candidate-card">
                <h3>👤 Candidate Information</h3>
                <div class="detail-item">
                    <strong>Name:</strong> {{ $interview->candidate_name }}
                </div>
                <div class="detail-item">
                    <strong>Email:</strong> {{ $interview->candidate_email ?? 'Not provided' }}
                </div>
                <div class="detail-item">
                    <strong>Phone:</strong> {{ $lead->number }}
                </div>
                <div class="detail-item">
                    <strong>Role:</strong> {{ $interview->job_role }}
                </div>
            </div>
            
            <div class="details-card">
                <h3>📋 Interview Details</h3>
                <div class="detail-item">
                    <strong>📅 Date:</strong> {{ date('d M Y', strtotime($interview->interview_date)) }}
                </div>
                <div class="detail-item">
                    <strong>⏰ Time:</strong> {{ date('g:i A', strtotime($interview->start_time)) }} - {{ date('g:i A', strtotime($interview->end_time)) }}
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
                @if($interview->meeting_link)
                <div class="detail-item meeting-link">
                    <strong>🔗 Meeting Link:</strong> <a href="{{ $interview->meeting_link }}" target="_blank">{{ $interview->meeting_link }}</a>
                </div>
                @endif
            </div>
            
            @if($interview->instructions)
            <div class="instructions">
                <h3>📝 Special Instructions</h3>
                <p>{{ $interview->instructions }}</p>
            </div>
            @endif
            
            <div class="reminder">
                👍 Please be prepared and join on time. Thank you for your cooperation!
            </div>
        </div>
        
        <div class="footer">
            <p>Best regards,<br><strong>Kwikster HR Team</strong></p>
            <div class="company-info">
                Kwikster Innovative Optimisations Pvt Ltd<br>
                Human Resources Department
            </div>
        </div>
    </div>
</body>
</html>