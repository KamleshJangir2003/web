<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Internship Certificate</title>
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            background: #f4f6fb;
        }

        .certificate-wrapper {
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .certificate {
            width: 1000px;
            background: #ffffff;
            padding: 70px;
            position: relative;
            border: 15px solid #c9a227;
            box-shadow: 0 15px 60px rgba(0,0,0,0.15);
        }

        /* Watermark */
        .certificate::before {
            content: "KWIKSTER";
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 120px;
            color: rgba(0,0,0,0.03);
            font-weight: bold;
            z-index: 0;
            letter-spacing: 10px;
        }

        .header {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .company-name {
            font-size: 40px;
            font-weight: bold;
            color: #2c3e50;
            letter-spacing: 4px;
        }

        .title {
            font-size: 30px;
            margin-top: 15px;
            color: #c9a227;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .divider {
            width: 150px;
            height: 3px;
            background: #c9a227;
            margin: 20px auto;
        }

        .content {
            text-align: center;
            margin-top: 40px;
            line-height: 2;
            font-size: 18px;
            position: relative;
            z-index: 2;
        }

        .intern-name {
            font-size: 36px;
            font-weight: bold;
            margin: 25px 0;
            color: #2c3e50;
        }

        .course {
            font-size: 22px;
            font-weight: bold;
            color: #c9a227;
        }

        .details {
            margin-top: 30px;
            font-size: 16px;
        }

        .footer {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .signature {
            text-align: center;
            width: 250px;
        }

        .signature-line {
            border-top: 2px solid #2c3e50;
            margin-bottom: 10px;
        }

        .date-section {
            margin-top: 50px;
            text-align: center;
            font-size: 14px;
            position: relative;
            z-index: 2;
        }

        .certificate-id {
            font-size: 12px;
            color: #888;
        }

    </style>
</head>
<body>

<div class="certificate-wrapper">
    <div class="certificate">

        <div class="header">
            <div class="company-name">KWIKSTER</div>
            <div class="title">Internship Certificate</div>
            <div class="divider"></div>
        </div>

        <div class="content">
            <p>This is to certify that</p>

            <div class="intern-name">{{ $intern->name }}</div>

            <p>has successfully completed the internship program in</p>

            <div class="course">{{ $intern->course ?? 'Software Development' }}</div>

            <div class="details">
                <p><strong>Duration:</strong> {{ $intern->internship_duration ?? 'N/A' }} Months</p>

                <p><strong>Period:</strong>
                    {{ $intern->start_date ? \Carbon\Carbon::parse($intern->start_date)->format('d M Y') : 'N/A' }}
                    to
                    {{ $intern->completion_date ? \Carbon\Carbon::parse($intern->completion_date)->format('d M Y') : 'N/A' }}
                </p>

                @if($intern->performance_rating)
                <p><strong>Performance:</strong> {{ $intern->performance_rating }}</p>
                @endif

                @if($intern->mentor)
                <p><strong>Mentor:</strong> {{ $intern->mentor->full_name }}</p>
                @endif
            </div>

            <p style="margin-top:30px;">
                We sincerely appreciate the dedication, professionalism and commitment shown during the internship tenure.
            </p>
        </div>

        <div class="footer">
            <div class="signature">
                <div class="signature-line"></div>
                <p><strong>HR Manager</strong></p>
                @if($intern->hr)
                <p>{{ $intern->hr->full_name }}</p>
                @endif
            </div>

            <div class="signature">
                <div class="signature-line"></div>
                <p><strong>Mentor</strong></p>
                @if($intern->mentor)
                <p>{{ $intern->mentor->full_name }}</p>
                @endif
            </div>
        </div>

        <div class="date-section">
            <p>Issued on: {{ $intern->completion_date ? \Carbon\Carbon::parse($intern->completion_date)->format('d F Y') : now()->format('d F Y') }}</p>
            <p class="certificate-id">Certificate ID: KWIK-{{ str_pad($intern->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>

    </div>
</div>

</body>
</html>