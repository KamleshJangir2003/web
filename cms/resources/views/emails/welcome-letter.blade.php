<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; background: #4CAF50; color: white; padding: 20px; }
        .content { margin: 20px 0; }
        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎉 Welcome to Kwikster! 🎉</h2>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $employee->full_name }}</strong>,</p>

            <p>Congratulations! We are thrilled to welcome you to the Kwikster family! 🎊</p>

            <p>You have successfully completed your certification period and have been selected as a permanent member of our team.</p>

            <p><strong>Your Details:</strong></p>
            <ul>
                <li><strong>Position:</strong> {{ $employee->job_title ?? 'Employee' }}</li>
                <li><strong>Joining Date:</strong> {{ $employee->joining_date ? $employee->joining_date->format('jS F Y') : 'N/A' }}</li>
                <li><strong>Annual CTC:</strong> ₹{{ number_format($employee->current_ctc ?? 0, 0) }}</li>
            </ul>

            <p>We are excited to have you on board and look forward to your valuable contributions to our organization.</p>

            <p>Your official joining letter has been sent in a separate email. Please review it carefully.</p>

            <p>Welcome aboard! Let's achieve great things together! 🚀</p>
        </div>

        <div class="footer">
            <p><strong>Best Regards,</strong><br>
            HR Team<br>
            Kwikster Innovative Optimisations Pvt Ltd<br>
            <a href="mailto:hr@thekwikster.com">hr@thekwikster.com</a><br>
            +91 96805 80889</p>
        </div>
    </div>
</body>
</html>
