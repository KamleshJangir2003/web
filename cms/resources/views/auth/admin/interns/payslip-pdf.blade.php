<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Intern Payslip - {{ $intern->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; }
        .header { text-align: center; border-bottom: 2px solid #2eacb3; padding-bottom: 15px; margin-bottom: 25px; }
        .company-name { font-size: 26px; font-weight: 700; color: #2eacb3; }
        .payslip-title { font-size: 18px; font-weight: 600; margin-top: 5px; }
        .section-title { font-size: 16px; font-weight: 600; margin: 20px 0 10px; border-left: 4px solid #2eacb3; padding-left: 8px; }
        .info-grid { display: table; width: 100%; margin-bottom: 20px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 5px; width: 50%; }
        .summary-box { background: #f8fbfc; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e3f2f4; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table thead { background: #2eacb3; color: #fff; }
        table th, table td { padding: 10px; border: 1px solid #eaeaea; text-align: center; }
        .total-row { background: #eef7f8; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="company-name">KWIKSTER</div>
        <div class="payslip-title">INTERN TRAINING RECEIPT</div>
        <div style="font-size: 13px; color:#666;">Generated on: {{ date('d M Y') }}</div>
    </div>

    <div class="section-title">Intern Information</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell"><strong>Name:</strong> {{ $intern->name }}</div>
            <div class="info-cell"><strong>Intern ID:</strong> #{{ str_pad($intern->id, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><strong>Course:</strong> {{ $intern->course ?? 'Not Set' }}</div>
            <div class="info-cell"><strong>Phone:</strong> {{ $intern->number ?? 'Not Set' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><strong>Mentor:</strong> {{ $intern->mentor->full_name ?? 'Not Assigned' }}</div>
            <div class="info-cell"><strong>HR:</strong> {{ $intern->hr->full_name ?? 'Not Assigned' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell"><strong>Start Date:</strong> {{ $intern->start_date ? $intern->start_date->format('d M Y') : 'Not Set' }}</div>
            <div class="info-cell"><strong>Duration:</strong> {{ $intern->internship_duration ?? 'Not Set' }} months</div>
        </div>
    </div>

    <div class="section-title">Payment Summary</div>
    <div class="summary-box">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell"><strong>Training Amount:</strong> ₹{{ number_format($intern->stipend ?? 0) }}</div>
                <div class="info-cell"><strong>Total Paid:</strong> ₹{{ number_format($intern->total_paid ?? 0) }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell"><strong>Pending Amount:</strong> ₹{{ number_format(($intern->stipend ?? 0) - ($intern->total_paid ?? 0)) }}</div>
                <div class="info-cell"></div>
            </div>
        </div>
    </div>

    <div class="section-title">Payment History</div>
    <table>
        <thead>
        <tr>
            <th>S.No.</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Method</th>
        </tr>
        </thead>
        <tbody>
        @forelse($intern->payments as $index => $payment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                <td>₹{{ number_format($payment->amount) }}</td>
                <td>{{ $payment->payment_method ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No payments recorded</td>
            </tr>
        @endforelse
        </tbody>
        @if($intern->payments->count() > 0)
        <tfoot>
        <tr class="total-row">
            <td colspan="2">Total Paid</td>
            <td>₹{{ number_format($intern->payments->sum('amount')) }}</td>
            <td></td>
        </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        This is a computer-generated receipt. No signature required. <br>
        © {{ date('Y') }} Kwikster. All rights reserved.
    </div>
</div>
</body>
</html>
