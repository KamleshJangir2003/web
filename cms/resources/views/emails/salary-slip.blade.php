<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; }
        .salary-info { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #667eea; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .total { font-weight: bold; font-size: 18px; color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>💰 Salary Slip</h2>
            <p>{{ date('F', mktime(0, 0, 0, $salaryRecord->month, 1)) }} {{ $salaryRecord->year }}</p>
        </div>
        
        <div class="content">
            <div class="salary-info">
                <h3>Employee Details</h3>
                <p><strong>Name:</strong> {{ $salaryRecord->employee->first_name }} {{ $salaryRecord->employee->last_name }}</p>
                <p><strong>Designation:</strong> {{ $salaryRecord->employee->job_title ?? 'N/A' }}</p>
                <p><strong>Department:</strong> {{ $salaryRecord->employee->department ?? 'N/A' }}</p>
            </div>

            <table>
                <tr>
                    <th>Description</th>
                    <th>Amount (₹)</th>
                </tr>
                <tr>
                    <td>In-Hand Salary</td>
                    <td>{{ number_format($salaryRecord->basic_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>Working Days</td>
                    <td>{{ number_format($salaryRecord->working_days, 1) }}</td>
                </tr>
                <tr>
                    <td>Employee PF</td>
                    <td>- {{ number_format($salaryRecord->employee_pf ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Employee ESI</td>
                    <td>- {{ number_format($salaryRecord->employee_esi ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Deduction</td>
                    <td>- {{ number_format($salaryRecord->deduction ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="total">Net Take Home</td>
                    <td class="total">₹{{ number_format($salaryRecord->net_salary, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>This is a system-generated email. Please do not reply.</p>
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
