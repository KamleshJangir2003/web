<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application Status - Kwikster</title>
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
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 50px 40px;
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
            border-top: 20px solid #c82333;
        }
        .company-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .company-address {
            font-size: 16px;
            opacity: 0.95;
            line-height: 1.6;
        }
        .content {
            padding: 50px 40px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 30px;
            color: #34495e;
        }
        .status-box {
            background: #ffe6e6;
            padding: 30px;
            border-radius: 12px;
            margin: 30px 0;
            border: 2px solid #dc3545;
            text-align: center;
        }
        .status-box h2 {
            color: #dc3545;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .status-box p {
            font-size: 16px;
            color: #721c24;
        }
        .reason-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
            border-left: 5px solid #dc3545;
        }
        .reason-section h3 {
            color: #dc3545;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .reason-text {
            background: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            line-height: 1.8;
            color: #2c3e50;
            border: 1px solid #dee2e6;
        }
        .next-steps {
            background: #e7f3ff;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
            border-left: 5px solid #0056b3;
        }
        .next-steps h3 {
            color: #0056b3;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .next-steps ul {
            list-style: none;
            padding-left: 0;
        }
        .next-steps li {
            margin-bottom: 12px;
            padding-left: 25px;
            position: relative;
            font-size: 16px;
        }
        .next-steps li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #0056b3;
            font-weight: bold;
        }
        .contact-info {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
            text-align: center;
        }
        .contact-info p {
            margin-bottom: 10px;
            font-size: 16px;
        }
        .contact-info a {
            color: #0056b3;
            text-decoration: none;
            font-weight: 600;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
        .closing {
            text-align: center;
            font-size: 16px;
            color: #34495e;
            margin: 35px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 40px;
            text-align: center;
        }
        .footer-content {
            margin-bottom: 20px;
        }
        .footer-content p {
            margin-bottom: 8px;
            font-size: 16px;
        }
        .footer-note {
            font-size: 14px;
            opacity: 0.8;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 20px;
            margin-top: 20px;
        }
        @media (max-width: 600px) {
            .email-container { margin: 10px; border-radius: 12px; }
            .header { padding: 30px 20px; }
            .content { padding: 30px 25px; }
            .status-box, .reason-section, .next-steps, .contact-info { padding: 20px; }
            .status-box h2 { font-size: 24px; }
            .company-name { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="company-name">Kwikster Innovative Optimisations Pvt Ltd</div>
            <div class="company-address">
                21/281, Kaveri path, Madhyam Marg Road<br>
                Mansarovar, Jaipur, Rajasthan, 302020
            </div>
        </div>

        <div class="content">
            <div class="greeting">
                Dear {{ $employee->first_name }},
            </div>

            <div class="status-box">
                <h2>Application Status Update</h2>
                <p>We regret to inform you that your application has not been selected at this time.</p>
            </div>

            <div class="reason-section">
                <h3>📋 Reason for Decision</h3>
                <div class="reason-text">
                    {{ $reason }}
                </div>
            </div>

            <div class="next-steps">
                <h3>💡 What's Next?</h3>
                <ul>
                    <li>We appreciate your interest in joining Kwikster</li>
                    <li>Feel free to apply for other positions in the future</li>
                    <li>Keep an eye on our careers page for new opportunities</li>
                    <li>We wish you the best in your career endeavors</li>
                </ul>
            </div>

            <div class="contact-info">
                <p><strong>📧 Have Questions?</strong></p>
                <p>If you have any questions regarding this decision, please feel free to contact our HR team at <a href="mailto:hr@thekwikster.com">hr@thekwikster.com</a></p>
            </div>

            <div class="closing">
                Thank you for considering Kwikster as your career destination. We wish you all the best!
            </div>
        </div>

        <div class="footer">
            <div class="footer-content">
                <p><strong>Best regards,</strong></p>
                <p><strong>HR Team</strong></p>
                <p><strong>Kwikster Innovative Optimisations Pvt Ltd</strong></p>
            </div>

            <div class="footer-note">
                <p>This is an automated email. Please do not reply to this email address.</p>
                <p>For any queries, please contact us at hr@thekwikster.com</p>
            </div>
        </div>
    </div>
</body>
</html>
