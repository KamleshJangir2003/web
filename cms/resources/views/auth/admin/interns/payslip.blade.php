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

        .btn-success {
            background: #25D366;
            color: #fff;
        }

        .btn-info {
            background: #0088cc;
            color: #fff;
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
        <button onclick="sendWhatsApp()" class="btn btn-success">📱 Send WhatsApp</button>
        <button onclick="showEmailModal()" class="btn btn-info">📧 Send Email</button>
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('admin.interns.payment', $intern->id) }}" class="btn btn-secondary">Back</a>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:30px; border-radius:10px; width:400px; box-shadow:0 5px 20px rgba(0,0,0,0.3);">
            <h3 style="margin-top:0; color:#333;">Send Payslip via Email</h3>
            <input type="email" id="emailInput" placeholder="Enter email address" value="{{ $intern->email ?? '' }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; margin-bottom:15px; font-size:14px;">
            <div style="text-align:right;">
                <button onclick="closeEmailModal()" style="padding:8px 16px; background:#777; color:#fff; border:none; border-radius:5px; cursor:pointer; margin-right:10px;">Cancel</button>
                <button onclick="sendEmailWithPDF()" style="padding:8px 16px; background:#0088cc; color:#fff; border:none; border-radius:5px; cursor:pointer;">Send</button>
            </div>
        </div>
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

<script>
function sendWhatsApp() {
    const phone = '{{ $intern->number ?? "" }}';
    if (!phone) {
        alert('Intern phone number not available');
        return;
    }
    
    fetch('{{ route("admin.interns.send-payslip-whatsapp", $intern->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.open(`https://wa.me/${data.phone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(data.message)}`, '_blank');
        } else {
            alert(data.message);
        }
    })
    .catch(err => alert('Error sending WhatsApp'));
}

function showEmailModal() {
    document.getElementById('emailModal').style.display = 'block';
}

function closeEmailModal() {
    document.getElementById('emailModal').style.display = 'none';
}

function sendEmailWithPDF() {
    const email = document.getElementById('emailInput').value;
    if (!email) {
        alert('Please enter email address');
        return;
    }
    
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        alert('Please enter valid email address');
        return;
    }
    
    // Show loading
    const sendBtn = event.target;
    sendBtn.disabled = true;
    sendBtn.textContent = 'Sending...';
    
    fetch('{{ route("admin.interns.send-payslip-email", $intern->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email: email })
    })
    .then(res => res.json())
    .then(data => {
        sendBtn.disabled = false;
        sendBtn.textContent = 'Send';
        
        if (data.success) {
            alert('✅ ' + data.message);
            closeEmailModal();
        } else {
            alert('❌ ' + (data.message || 'Error sending email'));
        }
    })
    .catch(err => {
        sendBtn.disabled = false;
        sendBtn.textContent = 'Send';
        console.error('Error:', err);
        alert('❌ Network error. Please check your connection and try again.');
    });
}
</script>

</body>
</html>
