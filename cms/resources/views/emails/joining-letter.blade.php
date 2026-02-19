<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin: 20px 0; }
        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Kwikster Innovative Optimisations Pvt Ltd</h2>
        </div>

        <div class="content">
            <p>Dear Mr./Ms. <strong>{{ $employee->full_name }}</strong>,</p>

            <p>Greetings from Kwikster Innovative Optimisations Pvt Ltd.</p>

            <p>We are pleased to confirm your appointment as <strong>{{ $employee->job_title ?? 'Employee' }}</strong> with Kwikster Innovative Optimisations Pvt Ltd, effective from <strong>{{ $employee->joining_date ? $employee->joining_date->format('jS F Y') : 'N/A' }}</strong>.</p>

            <p>Your Annual Total CTC will be <strong>₹{{ number_format($employee->current_ctc ?? 0, 0) }}</strong>, as discussed and agreed upon.</p>

            <p>Please find the attached Joining Letter outlining the terms and conditions of your employment, including details regarding your probation period, training requirements, and work schedule.</p>

            <p>Kindly sign and return a copy of this letter via email as confirmation of your joining.</p>

            <p>We are delighted to welcome you to the Kwikster team and look forward to your valuable contribution to the organization.</p>
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
