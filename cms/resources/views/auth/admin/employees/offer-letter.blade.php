<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Offer Letter - {{ $employee->full_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.7;
            margin: 0;
            padding: 40px;
            color: #000;
            font-size: 14px;
        }
        .company-info {
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 40px;
        }
        .date {
            margin-bottom: 30px;
            font-weight: bold;
        }
        .candidate-name {
            margin-bottom: 25px;
            font-weight: bold;
        }
        .content p {
            margin-bottom: 18px;
            text-align: justify;
        }
        .ctc-highlight {
            font-weight: bold;
            margin: 15px 0;
        }
        .terms-title {
            margin-top: 30px;
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-section {
            margin-top: 70px;
        }
    </style>
</head>
<body>
     <!-- Company Logo -->
     <div style="text-align:center; margin-bottom:30px;">
        <img src="{{ public_path('Kwikster.jpeg') }}" 
             alt="Kwikster Logo" 
             style="height:100px;">
    </div>

    <!-- Company Info -->
    <div class="company-info">
        21/281, Kaveri path, Madhyam Marg Road,<br>
        Mansarovar, Jaipur, Rajasthan, 302020<br>
        +91 96805 80889<br>
        hr@thekwikster.com<br>
        www.thekwikster.com
    </div>

    <!-- Date -->
    <div class="date">
        {{ date('jS \of F, Y') }}
    </div>

    <!-- Candidate Name -->
    <div class="candidate-name">
        Dear {{ $employee->full_name }},
    </div>
@php
$netPay = $employee->in_hand_salary ?? 0;

// Same logic as Joining Letter
$gross = round($netPay / 0.9205, 2);

$basic = round($gross * 0.60, 2);
$hra = round($gross * 0.40, 2);

$employeePF = round($basic * 0.12, 2);
$employeeESIC = round($gross * 0.0075, 2);

$employerPF = round($basic * 0.13, 2);
$employerESIC = round($gross * 0.0325, 2);

$monthlyCTC = round($gross + $employerPF + $employerESIC, 2);

$annualCTC = round($monthlyCTC * 12, 2);
@endphp
    <!-- Content -->
    <div class="content">
        <p>
            We are delighted to extend an offer of employment to you for the position of 
            <strong>{{ $employee->department ?? 'Insurance Certificate Executive' }}</strong> 
            at <strong>Kwikster Innovative Optimisations Pvt Ltd</strong> starting from 
            <strong>
                {{ $employee->joining_date ? $employee->joining_date->format('jS \of F, Y') : date('jS \of F, Y') }}
            </strong>.
            After the selection process, your qualifications, skills, and interview performance 
            have stood out, and we are excited to have you join our team.
        </p>

        @if($netPay > 0)
<p class="ctc-highlight">
    Your Annual Total CTC will be ₹{{ number_format($annualCTC, 0) }}.
</p>
@endif

        <p>
            Please note that you will be working 5.5 days per week and your base location will be Jaipur.
        </p>

        <p>
            Please indicate your acceptance of this offer by signing and returning this letter 
            by <strong>
                {{ $employee->joining_date ? $employee->joining_date->format('jS \of F, Y') : date('jS \of F, Y') }}
            </strong> through email.
        </p>

        <p>
            If you have any questions or require further information, please feel free to contact 
            HR team at hr@thekwikster.com
        </p>

        <p>
            We are excited to have you join our team and hope you will have a successful career with Kwikster.
        </p>

       <!-- Terms -->
<p class="terms-title">Please note the following terms and conditions:</p>

<ol style="margin-top:15px; padding-left:20px;">
    <li style="margin-bottom:10px;">
        The selected candidate will be required to undergo a five (5) days certification/training period.
    </li>

    <li style="margin-bottom:10px;">
        Attendance during this certification/training period is mandatory, and no leave will be permitted.
    </li>

    <li style="margin-bottom:10px;">
        Any absence or leave taken during these five (5) days may result in immediate termination of the offer.
    </li>

    <li style="margin-bottom:10px;">
        Employment shall commence with a Probation Period of three months, which may be extended at the 
        sole discretion of the Company based on performance, discipline, and business requirements.
    </li>
</ol>

    </div>

    <!-- Signature -->
    <div class="signature-section">
        <p>
            Vishwash Agarwal<br>
            Director<br>
            Kwikster Innovative Optimisations Pvt Ltd
        </p>
    </div>

</body>
</html>
