<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            line-height: 1.6; 
            color: #000; 
            margin: 0;
            padding: 20px;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 40px;
            background: #fff;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .content { 
            margin: 20px 0; 
            font-size: 14px;
        }
        .content h3 {
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
        }
        .content p {
            margin: 10px 0;
            text-align: justify;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .footer { 
            margin-top: 40px; 
            padding-top: 20px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        ol, ul {
            margin: 10px 0;
            padding-left: 25px;
        }
        li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
              <!-- Company Logo -->
    <div style="margin-bottom:15px;">
    @php
        $logoPath = public_path('Kwikster.jpeg');
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $logo = null;
        }
    @endphp
    @if($logo)
        <img src="{{ $logo }}" style="height:80px;">
    @endif
    </div>
            <h2>KWIKSTER INNOVATIVE OPTIMISATIONS PVT LTD</h2>
            <p>21/281, Kaveri Path, Madhyam Marg Road</p>
            <p>Mansarovar, Jaipur, Rajasthan – 302020</p>
            <p>+91 96805 80889 | hr@thekwikster.com</p>
            <p>www.thekwikster.com</p>
        </div>

        <div class="content">
            <p><strong>Date:</strong> {{ now()->format('d F Y') }}</p>
            
            <h3>JOINING LETTER</h3>
            
            <p><strong>To,</strong><br>
            {{ $employee->gender == 'male' ? 'Mr.' : ($employee->gender == 'female' ? 'Ms.' : '') }} {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}</p>

            <p>
    Dear {{ explode(' ', $employee->full_name ?? $employee->first_name)[0] }},
</p>

            <p>We are pleased to confirm your appointment as <strong>{{ $employee->department ?? 'Employee' }}</strong> at Kwikster Innovative Optimisations Pvt Ltd, effective from <strong>{{ $employee->joining_date ? $employee->joining_date->format('d F Y') : now()->format('d F Y') }}</strong>. This is a Full-Time employment position.</p>

            <p>You are expected to devote your full professional time and attention to the Company's business and shall not engage in any other employment or consulting activity that may create a conflict of interest.</p>

            <div class="section-title">1. Salary & Compensation Structure</div>
            <p>You will receive a monthly Net Pay of <strong>₹{{ number_format($employee->in_hand_salary ?? 0, 0) }}/-</strong>, payable as per the Company's payroll cycle.</p>

            <p><strong>Salary Structure:</strong></p>
            <table>
                <tr>
                    <th>Salary Head</th>
                    <th>Amount (₹)</th>
                </tr>
                @php
    $netPay = $employee->in_hand_salary ?? 0;

    // Reverse calculation (Basic 60%, PF 12%, ESIC 0.75%)
    // Net = Gross - PF - ESIC
    // PF = 12% of Basic = 7.2% of Gross
    // ESIC = 0.75% of Gross
    // Total deduction = 7.95%
    // Net = 92.05% of Gross

    $gross = round($netPay / 0.9205, 2);

    $basic = round($gross * 0.60, 2);
    $hra = round($gross * 0.40, 2);

    $employeePF = round($basic * 0.12, 2);
    $employeeESIC = round($gross * 0.0075, 2);

    $employerPF = round($basic * 0.13, 2);
    $employerESIC = round($gross * 0.0325, 2);

    $ctc = round($gross + $employerPF + $employerESIC, 2);
@endphp

<tr>
    <td>Basic (60%)</td>
    <td>{{ number_format($basic, 2) }}</td>
</tr>
<tr>
    <td>HRA (40%)</td>
    <td>{{ number_format($hra, 2) }}</td>
</tr>
<tr>
    <td>Employer PF (13%)</td>
    <td>{{ number_format($employerPF, 2) }}</td>
</tr>
<tr>
    <td>Employer ESIC (3.25%)</td>
    <td>{{ number_format($employerESIC, 2) }}</td>
</tr>
<tr>
    <td><strong>CTC</strong></td>
    <td><strong>{{ number_format($ctc, 2) }}</strong></td>
</tr>

            </table>

            <p><strong>Deductions:</strong></p>
            <table>
                <tr>
                    <th>Deduction Head</th>
                    <th>Amount (₹)</th>
                </tr>
                <tr>
                    <td>Employee PF</td>
                    <td>{{ number_format($employeePF, 0) }}</td>
                </tr>
                <tr>
                    <td>Employee ESIC</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td><strong>Net Pay</strong></td>
                    <td><strong>{{ number_format($netPay, 0) }}</strong></td>
                </tr>
            </table>

            <p>Salary and incentives are subject to performance and Company policies.</p>
            <p>Targets may be assigned verbally or in writing. Failure to achieve assigned targets may result in salary deductions as per Company policy.</p>

            <div class="section-title">2. Nature of Employment</div>
            <p>You are appointed as a full-time employee and shall devote your entire professional time, attention, and abilities to the business of the Company.</p>

            <div class="section-title">3. Probation Period</div>
            <p>You shall be on probation for 3 months from the date of joining.</p>
            <p>Probation may be extended by 60 days based on performance.</p>
            <p>Confirmation is subject to satisfactory performance evaluation and written confirmation from the Company.</p>

            <div class="section-title">4. Performance & KPI</div>
            <p>Your employment continuation is subject to:</p>
            <ul>
                <li>Minimum productivity standards</li>
                <li>Quality benchmarks</li>
                <li>Compliance metrics</li>
                <li>Attendance standards</li>
            </ul>
            <p>Failure to meet required standards may result in placement under Performance Improvement Plan (PIP).</p>

            <div class="section-title">5. Working Hours</div>
            <p>Company operates on a 24x7 business model.</p>
            <p>You agree to work rotational shifts including night shifts.</p>
            <p>Minimum working hours: 8 hours per day (excluding breaks).</p>

            <div class="section-title">6. Leave & Attendance</div>
            <p>Leave will be governed by Company policy.</p>
            <p>Leave must be pre-approved.</p>
            <p>Salary will be processed strictly as per attendance records.</p>

            <div class="section-title">7. Notice Period & Termination</div>
            <p>Either party may terminate employment with 30 days written notice.</p>
            <p>Company may terminate without notice in cases of serious misconduct, fraud, or breach of confidentiality.</p>

            <div class="section-title">8. Confidentiality & Data Protection</div>
            <p>All Company data including:</p>
            <ul>
                <li>Client information</li>
                <li>CRM records</li>
                <li>Leads & contact details</li>
                <li>Scripts & pricing</li>
                <li>Recordings & internal communications</li>
            </ul>
            <p>are strictly confidential and remain Company property. These obligations continue even after separation.</p>

            <div class="section-title">9. Monitoring Consent</div>
            <p>You consent to:</p>
            <ul>
                <li>Call recording</li>
                <li>Screen monitoring</li>
                <li>CRM logging</li>
                <li>CCTV monitoring</li>
            </ul>
            <p>for compliance and quality control purposes.</p>

            <div class="section-title">10. Non-Solicitation</div>
            <p>For 12 months after separation, you shall not:</p>
            <ul>
                <li>Solicit Company clients</li>
                <li>Induce employees to resign</li>
                <li>Engage Company clients independently</li>
            </ul>

            <div class="section-title">11. Background Verification</div>
            <p>Employment is subject to satisfactory verification of credentials and documents.</p>

            <div class="section-title">12. Governing Law</div>
            <p>This employment shall be governed by the laws of India and subject to jurisdiction of courts at Jaipur, Rajasthan.</p>

            <div class="section-title">Acceptance</div>
            <p>Please sign below as acknowledgment and acceptance of the above terms.</p>
        </div>

        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <p><strong>For</strong><br>
                    <strong>Kwikster Innovative Optimisations Pvt Ltd</strong></p>
                    <br><br>
                    <p>_______________________<br>
                    <strong>Vishwash Agarwal</strong><br>
                    Director</p>
                </div>
                <div class="signature-box">
                    <p><strong>Accepted By:</strong></p>
                    <br>
                    <p>Employee Name: _______________________</p>
                    <p>Signature: _______________________</p>
                    <p>Date: _______________________</p>
                </div>
            </div>

            <div class="section-title" style="margin-top: 40px;">ANNEXURE – CONFIDENTIALITY & NON-DISCLOSURE UNDERTAKING</div>
            <p>I, <strong>{{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}</strong>, agree that:</p>
            <ul>
                <li>All customer data, CRM records, scripts, pricing models, and internal documents are confidential.</li>
                <li>I shall not disclose, copy, store, or misuse such information.</li>
                <li>I consent to monitoring systems.</li>
                <li>I shall not solicit Company clients for 12 months post separation.</li>
                <li>Breach may result in legal action.</li>
            </ul>
        </div>
    </div>
</body>
</html>
