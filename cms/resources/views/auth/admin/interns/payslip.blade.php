<!DOCTYPE html>
<html>
<head>
    <title>Intern Payslip - {{ $intern->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f4f6f9;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2eacb3;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .company-name {
            font-size: 26px;
            font-weight: 700;
            color: #2eacb3;
        }

        .payslip-title {
            font-size: 18px;
            font-weight: 600;
            margin-top: 5px;
            color: #444;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            border-left: 4px solid #2eacb3;
            padding-left: 8px;
            color: #333;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 40px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .info-grid div strong {
            color: #555;
        }

        .summary-box {
            background: #f8fbfc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #e3f2f4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }

        table thead {
            background: #2eacb3;
            color: #fff;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #eaeaea;
            text-align: center;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .total-row {
            background: #eef7f8;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .btn-area {
            margin-bottom: 20px;
            text-align: right;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-primary {
            background: #2eacb3;
            color: #fff;
        }

        .btn-secondary {
            background: #777;
            color: #fff;
            text-decoration: none;
        }

        @media print {
            body { background: #fff; }
            .btn-area { display: none; }
            .container {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="btn-area">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('admin.interns.payment', $intern->id) }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="header">
        <div class="company-name">KWIKSTER</div>
        <div class="payslip-title">INTERN TRAINING RECEIPT</div>
        <div style="font-size: 13px; color:#666;">Generated on: {{ date('d M Y') }}</div>
    </div>

    <!-- Intern Info -->
    <div class="section-title">Intern Information</div>
    <div class="info-grid">
        <div><strong>Name:</strong> {{ $intern->name }}</div>
        <div><strong>Intern ID:</strong> #{{ str_pad($intern->id, 4, '0', STR_PAD_LEFT) }}</div>
        <div><strong>Course:</strong> {{ $intern->course ?? 'Not Set' }}</div>
        <div><strong>Phone:</strong> {{ $intern->number ?? 'Not Set' }}</div>
        <div><strong>Mentor:</strong> {{ $intern->mentor->full_name ?? 'Not Assigned' }}</div>
        <div><strong>HR:</strong> {{ $intern->hr->full_name ?? 'Not Assigned' }}</div>
        <div><strong>Start Date:</strong> {{ $intern->start_date ? $intern->start_date->format('d M Y') : 'Not Set' }}</div>
        <div><strong>Duration:</strong> {{ $intern->internship_duration ?? 'Not Set' }} months</div>
    </div>

    <!-- Payment Summary -->
    <div class="section-title">Payment Summary</div>
    <div class="summary-box">
        <div class="info-grid">
            <div><strong>Training Amount:</strong> ₹{{ number_format($intern->stipend ?? 0) }}</div>
            <div><strong>Total Paid:</strong> ₹{{ number_format($intern->total_paid ?? 0) }}</div>
            <div><strong>Pending Amount:</strong> ₹{{ number_format(($intern->stipend ?? 0) - ($intern->total_paid ?? 0)) }}</div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="section-title">Payment History</div>

    <table>
        <thead>
        <tr>
            <th>S.No.</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Method</th>
            <!-- <th>Notes</th> -->
        </tr>
        </thead>
        <tbody>
        @forelse($intern->payments as $index => $payment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                <td>₹{{ number_format($payment->amount) }}</td>
                <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                <!-- <td>{{ $payment->notes ?? '-' }}</td> -->
            </tr>
        @empty
            <tr>
                <td colspan="5">No payments recorded</td>
            </tr>
        @endforelse
        </tbody>

        @if($intern->payments->count() > 0)
        <tfoot>
        <tr class="total-row">
            <td colspan="2">Total Paid</td>
            <td>₹{{ number_format($intern->payments->sum('amount')) }}</td>
            <td colspan="2"></td>
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
